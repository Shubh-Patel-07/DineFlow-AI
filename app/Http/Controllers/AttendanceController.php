<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffAttendance;
use App\Models\PayrollSlip;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Resolve restaurant ID from request or authenticated user.
     */
    protected function getRestaurantId(Request $request)
    {
        return $request->header('X-Restaurant-ID') ?? auth()->user()?->restaurant_id ?? 1;
    }

    /**
     * Clock-in staff member for today's shift.
     */
    public function clockIn(Request $request)
    {
        $restaurantId = $this->getRestaurantId($request);
        $userId = $request->input('user_id') ?? auth()->id();

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Staff user ID is required.'], 422);
        }

        $user = User::where('restaurant_id', $restaurantId)->find($userId);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Staff member not found.'], 404);
        }

        $today = Carbon::today()->toDateString();

        $existing = StaffAttendance::where('restaurant_id', $restaurantId)
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->clock_in) {
            return response()->json([
                'status' => 'error',
                'message' => 'Staff already clocked in today at ' . Carbon::parse($existing->clock_in)->format('h:i A'),
                'data' => $existing
            ], 400);
        }

        $shift = $request->input('shift', 'REGULAR');
        $now = Carbon::now();

        $attendance = StaffAttendance::updateOrCreate(
            ['restaurant_id' => $restaurantId, 'user_id' => $userId, 'date' => $today],
            [
                'clock_in' => $now,
                'status' => 'PRESENT',
                'shift' => $shift,
                'clock_in_ip' => $request->ip(),
                'notes' => $request->input('notes')
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "{$user->name} clocked in successfully at " . $now->format('h:i A'),
            'data' => $attendance->load('user:id,name,role')
        ]);
    }

    /**
     * Clock-out staff member and calculate shift hours & overtime.
     */
    public function clockOut(Request $request)
    {
        $restaurantId = $this->getRestaurantId($request);
        $userId = $request->input('user_id') ?? auth()->id();

        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Staff user ID is required.'], 422);
        }

        $today = Carbon::today()->toDateString();

        $attendance = StaffAttendance::where('restaurant_id', $restaurantId)
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json([
                'status' => 'error',
                'message' => 'No clock-in record found for today. Please clock in first.'
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'status' => 'error',
                'message' => 'Staff already clocked out today at ' . Carbon::parse($attendance->clock_out)->format('h:i A'),
                'data' => $attendance
            ], 400);
        }

        $now = Carbon::now();
        $clockIn = Carbon::parse($attendance->clock_in);
        $minutesWorked = $clockIn->diffInMinutes($now);
        $totalHours = round($minutesWorked / 60, 2);

        // Standard shift is 8 hours
        $standardShift = 8.0;
        $overtimeHours = 0.0;
        if ($totalHours > $standardShift) {
            $overtimeHours = round($totalHours - $standardShift, 2);
            $totalHours = $standardShift;
        }

        $attendance->update([
            'clock_out' => $now,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'notes' => $request->filled('notes') ? ($attendance->notes . ' | ' . $request->notes) : $attendance->notes
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Clocked out at {$now->format('h:i A')}. Shift: {$totalHours}h, Overtime: {$overtimeHours}h",
            'data' => $attendance->fresh()->load('user:id,name,role')
        ]);
    }

    /**
     * Get real-time status of staff attendance for today.
     */
    public function todayStatus(Request $request)
    {
        $restaurantId = $this->getRestaurantId($request);
        $today = Carbon::today()->toDateString();

        $staff = User::where('restaurant_id', $restaurantId)
            ->whereIn('role', ['admin', 'manager', 'cashier', 'kitchen', 'waiter', 'staff'])
            ->select('id', 'name', 'role', 'phone')
            ->get();

        $attendances = StaffAttendance::where('restaurant_id', $restaurantId)
            ->where('date', $today)
            ->get()
            ->keyBy('user_id');

        $result = $staff->map(function ($member) use ($attendances) {
            $att = $attendances->get($member->id);
            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'role' => $member->role,
                'is_clocked_in' => $att && $att->clock_in && !$att->clock_out,
                'is_clocked_out' => $att && $att->clock_out !== null,
                'clock_in' => $att?->clock_in?->format('h:i A'),
                'clock_out' => $att?->clock_out?->format('h:i A'),
                'total_hours' => $att?->total_hours ?? 0,
                'overtime_hours' => $att?->overtime_hours ?? 0,
                'status' => $att ? $att->status : 'ABSENT',
            ];
        });

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'data' => $result]);
        }

        return view('admin.attendance', ['attendance' => $result]);
    }

    /**
     * Timesheet report with date range.
     */
    public function timesheet(Request $request)
    {
        $restaurantId = $this->getRestaurantId($request);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = StaffAttendance::where('restaurant_id', $restaurantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user:id,name,role')
            ->orderBy('date', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $records = $query->paginate(30);

        return response()->json(['status' => 'success', 'data' => $records]);
    }

    /**
     * Generate payroll for a staff member or all staff for a month.
     */
    public function generatePayroll(Request $request)
    {
        $restaurantId = $this->getRestaurantId($request);

        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2035',
            'user_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_rate_per_hour' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        $userId = $validated['user_id'];
        $basicSalary = (float)$validated['basic_salary'];
        $otRate = (float)($validated['overtime_rate_per_hour'] ?? ($basicSalary / 240)); // default ~30 days * 8h
        $bonus = (float)($validated['bonus'] ?? 0);
        $deductions = (float)($validated['deductions'] ?? 0);

        // Count attendance records for this month
        $attendances = StaffAttendance::where('restaurant_id', $restaurantId)
            ->where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $presentDays = $attendances->count();
        $totalOvertimeHours = $attendances->sum('overtime_hours');
        $overtimePay = round($totalOvertimeHours * $otRate, 2);

        // Pro-rate basic salary assuming 30 days month
        $totalDays = 30;
        $earnedBasic = $totalDays > 0 ? round(($basicSalary / $totalDays) * min($presentDays, $totalDays), 2) : 0;
        $netSalary = max(0, round($earnedBasic + $overtimePay + $bonus - $deductions, 2));

        $slip = PayrollSlip::updateOrCreate(
            [
                'restaurant_id' => $restaurantId,
                'user_id' => $userId,
                'month' => $month,
                'year' => $year
            ],
            [
                'basic_salary' => $basicSalary,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'overtime_pay' => $overtimePay,
                'bonus' => $bonus,
                'deductions' => $deductions,
                'net_salary' => $netSalary,
                'payment_status' => 'PENDING',
                'generated_by' => auth()->user()?->name ?? 'Admin',
                'remarks' => $validated['remarks'] ?? "Present: {$presentDays}d, OT: {$totalOvertimeHours}h"
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Payroll generated successfully',
            'data' => $slip->load('user:id,name,role')
        ]);
    }

    /**
     * Mark a payroll slip as paid.
     */
    public function markPaid(Request $request, $id)
    {
        $restaurantId = $this->getRestaurantId($request);
        $slip = PayrollSlip::where('restaurant_id', $restaurantId)->findOrFail($id);

        $slip->update([
            'payment_status' => 'PAID',
            'payment_date' => $request->input('payment_date', Carbon::today()->toDateString()),
            'payment_mode' => $request->input('payment_mode', 'CASH')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payroll marked as PAID',
            'data' => $slip
        ]);
    }
}
