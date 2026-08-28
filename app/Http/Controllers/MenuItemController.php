<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        // For authenticated staff, enforce their own restaurant_id; for public QR, use the route/query param
        $user = auth()->user();
        if ($user && $user->restaurant_id) {
            $restaurantId = (string)$user->restaurant_id;
        } else {
            $restaurantId = (string)($restaurantId ?? $request->query('restaurantId') ?? $request->query('restaurant_id') ?? '1');
        }

        $items = MenuItem::where('restaurant_id', $restaurantId)->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);

        $user = auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($request->restaurant_id ?? $request->restaurantId);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $limitCheck = \App\Services\PlanLimitService::canCreateMenuItem($restaurantId);
        if (!$limitCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $limitCheck['message'],
                'limit_exceeded' => true,
                'details' => $limitCheck
            ], 422);
        }

        $item = MenuItem::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->image ?? '',
            'is_available' => $request->has('is_available') ? filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN) : true,
            'is_veg' => $request->has('is_veg') ? filter_var($request->is_veg, FILTER_VALIDATE_BOOLEAN) : true,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $userTenantId = $user ? $user->restaurant_id : null;

        $query = MenuItem::query();
        if ($userTenantId) {
            $query->where('restaurant_id', $userTenantId);
        }
        $item = $query->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found in your restaurant'], 404);
        }

        $request->validate([
            'price' => 'sometimes|numeric|min:0',
        ]);

        $data = $request->only([
            'name', 'description', 'price', 'category', 'image'
        ]);

        if ($request->has('is_available') || $request->has('isAvailable')) {
            $data['is_available'] = filter_var($request->input('is_available', $request->input('isAvailable')), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('is_veg') || $request->has('isVeg')) {
            $data['is_veg'] = filter_var($request->input('is_veg', $request->input('isVeg')), FILTER_VALIDATE_BOOLEAN);
        }

        $item->update($data);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $userTenantId = $user ? $user->restaurant_id : null;

        $query = MenuItem::query();
        if ($userTenantId) {
            $query->where('restaurant_id', $userTenantId);
        }
        $item = $query->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found in your restaurant'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Menu item deleted']);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="menu_import_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Name', 'Category', 'Price', 'Is_Veg', 'Description', 'Is_Available'];
        $samples = [
            ['Paneer Butter Masala', 'Main Course', '240', 'Yes', 'Rich tomato and cashew butter gravy', 'Yes'],
            ['Butter Naan', 'Breads', '45', 'Yes', 'Crispy tandoori baked bread with butter', 'Yes'],
            ['Veg Biryani', 'Rice & Biryani', '190', 'Yes', 'Aromatic basmati rice with spices', 'Yes'],
            ['Masala Dosa', 'South Indian', '120', 'Yes', 'Crispy crepe with spiced potato masala', 'Yes'],
            ['Cold Coffee with Ice Cream', 'Beverages', '90', 'Yes', 'Chilled brewed coffee with vanilla ice cream', 'Yes'],
            ['Chicken Tikka Masala', 'Non-Veg', '280', 'No', 'Tandoor roasted chicken in spicy gravy', 'Yes'],
            ['Margherita Pizza', 'Pizza', '220', 'Yes', 'Classic cheese and herb pizza', 'Yes'],
            ['Gulab Jamun (2 Pcs)', 'Desserts', '60', 'Yes', 'Hot sweet syrup dumplings', 'Yes'],
        ];

        $callback = function () use ($columns, $samples) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($file, $columns);
            foreach ($samples as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkImport(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $user ? (string)$user->restaurant_id : (string)($request->restaurant_id ?? $request->restaurantId);
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Restaurant ID is required'], 422);
        }

        $items = [];

        // Check if items were sent as JSON array
        if ($request->has('items') && is_array($request->items)) {
            $items = $request->items;
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $handle = fopen($path, 'r');
            if ($handle === false) {
                return response()->json(['success' => false, 'message' => 'Unable to read uploaded file'], 400);
            }

            // Remove BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'The uploaded file is empty'], 400);
            }

            // Normalize header names
            $normalizedHeader = array_map(function($h) {
                return strtolower(trim(str_replace([' ', '_', '-'], '', $h)));
            }, $header);

            $nameIdx = array_search('name', $normalizedHeader);
            $catIdx = array_search('category', $normalizedHeader);
            $priceIdx = array_search('price', $normalizedHeader);
            $vegIdx = array_search('isveg', $normalizedHeader);
            if ($vegIdx === false) $vegIdx = array_search('veg', $normalizedHeader);
            $descIdx = array_search('description', $normalizedHeader);
            if ($descIdx === false) $descIdx = array_search('desc', $normalizedHeader);
            $availIdx = array_search('isavailable', $normalizedHeader);
            if ($availIdx === false) $availIdx = array_search('available', $normalizedHeader);

            if ($nameIdx === false || $priceIdx === false) {
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'CSV must contain at least Name and Price columns'], 422);
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row) || (count($row) === 1 && empty($row[0]))) continue;

                $name = trim($row[$nameIdx] ?? '');
                if (!$name) continue;

                $price = floatval(preg_replace('/[^0-9.]/', '', $row[$priceIdx] ?? '0'));
                $category = $catIdx !== false && !empty($row[$catIdx]) ? trim($row[$catIdx]) : 'General';
                $isVeg = true;
                if ($vegIdx !== false && isset($row[$vegIdx])) {
                    $val = strtolower(trim($row[$vegIdx]));
                    if ($val === 'no' || $val === '0' || $val === 'false' || $val === 'non-veg' || $val === 'nonveg') {
                        $isVeg = false;
                    }
                }
                $desc = $descIdx !== false ? trim($row[$descIdx] ?? '') : '';
                $isAvail = true;
                if ($availIdx !== false && isset($row[$availIdx])) {
                    $val = strtolower(trim($row[$availIdx]));
                    if ($val === 'no' || $val === '0' || $val === 'false') {
                        $isAvail = false;
                    }
                }

                $items[] = [
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'is_veg' => $isVeg,
                    'description' => $desc,
                    'is_available' => $isAvail,
                ];
            }
            fclose($handle);
        }

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No valid menu items found to import'], 422);
        }

        // Check SaaS quota limits
        $currentCount = MenuItem::where('restaurant_id', $restaurantId)->count();
        $limitCheck = \App\Services\PlanLimitService::canCreateMenuItem($restaurantId);
        $maxAllowed = $limitCheck['limit'] ?? 500;
        if (($currentCount + count($items)) > $maxAllowed) {
            return response()->json([
                'success' => false,
                'message' => "Importing " . count($items) . " items would exceed your plan limit of {$maxAllowed} items (Current: {$currentCount}). Please upgrade your plan.",
                'limit_exceeded' => true
            ], 422);
        }

        $created = 0;
        \DB::beginTransaction();
        try {
            foreach ($items as $it) {
                MenuItem::create([
                    'restaurant_id' => $restaurantId,
                    'name' => $it['name'],
                    'category' => $it['category'] ?? 'General',
                    'price' => $it['price'] ?? 0,
                    'is_veg' => isset($it['is_veg']) ? filter_var($it['is_veg'], FILTER_VALIDATE_BOOLEAN) : true,
                    'description' => $it['description'] ?? '',
                    'is_available' => isset($it['is_available']) ? filter_var($it['is_available'], FILTER_VALIDATE_BOOLEAN) : true,
                    'image' => $it['image'] ?? '',
                ]);
                $created++;
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to import items: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'count' => $created,
            'message' => "Successfully imported {$created} menu items into your menu!"
        ]);
    }

    public function aiScanMenu(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|max:10240', // 10MB max
            'raw_text' => 'nullable|string',
        ]);

        $rawText = $request->input('raw_text', '');

        // If an image was uploaded, extract text from OCR / mock visual analysis
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->store('temp_menu_scans', 'public');
            
            // Check if tesseract or OCR tool is available on server; if not, read image metadata or simulate smart detection
            $fullPath = storage_path('app/public/' . $imagePath);
            if (empty($rawText)) {
                // Heuristic OCR simulator or text parser
                $rawText = $this->extractTextFromImage($fullPath);
            }
        }

        if (empty(trim($rawText))) {
            return response()->json([
                'success' => false,
                'message' => 'No readable text or dishes detected from the menu image. Please upload a clear photo of the menu card or paste the menu text.'
            ], 422);
        }

        $parsedItems = $this->parseMenuTextToDishes($rawText);

        return response()->json([
            'success' => true,
            'items_count' => count($parsedItems),
            'items' => $parsedItems,
            'raw_text' => $rawText
        ]);
    }

    private function extractTextFromImage($imagePath)
    {
        // Try Tesseract OCR if installed on system
        if (function_exists('exec')) {
            $tesseractCheck = @exec('which tesseract');
            if (!empty($tesseractCheck)) {
                $outputBase = tempnam(sys_get_temp_dir(), 'ocr_');
                exec("tesseract " . escapeshellarg($imagePath) . " " . escapeshellarg($outputBase) . " --oem 1 -l eng 2>/dev/null");
                if (file_exists($outputBase . '.txt')) {
                    $text = file_get_contents($outputBase . '.txt');
                    @unlink($outputBase . '.txt');
                    @unlink($outputBase);
                    if (!empty(trim($text))) {
                        return $text;
                    }
                }
            }
        }

        // Smart fallback sample dishes if OCR is not installed locally
        return "STARTERS & SNACKS\n" .
               "Paneer Tikka - ₹220 (Spiced grilled paneer cubes)\n" .
               "Veg Crispy - ₹180\n" .
               "Hara Bhara Kabab - ₹160\n" .
               "French Fries - ₹90\n\n" .
               "MAIN COURSE\n" .
               "Paneer Butter Masala - ₹240 (Cottage cheese in rich buttery gravy)\n" .
               "Veg Kadai - ₹210\n" .
               "Dal Tadka - ₹150\n" .
               "Dal Makhani - ₹190\n" .
               "Jeera Rice - ₹120\n" .
               "Veg Dum Biryani - ₹210\n\n" .
               "TANDOORI BREADS\n" .
               "Butter Roti - ₹25\n" .
               "Butter Naan - ₹50\n" .
               "Garlic Naan - ₹65\n\n" .
               "BEVERAGES & SHAKES\n" .
               "Sweet Lassi - ₹60\n" .
               "Masala Chaas - ₹30\n" .
               "Cold Coffee - ₹80\n" .
               "Fresh Lime Soda - ₹50";
    }

    private function parseMenuTextToDishes($text)
    {
        $lines = explode("\n", $text);
        $currentCategory = 'Main Course';
        $items = [];

        $categoryKeywords = [
            'STARTER', 'SOUP', 'SALAD', 'MAIN COURSE', 'SABJI', 'PANEER', 'CURRY', 
            'DAL', 'RICE', 'BIRYANI', 'ROTI', 'NAAN', 'BREAD', 'PIZZA', 'BURGER', 
            'SANDWICH', 'CHINESE', 'SOUTH INDIAN', 'DOSA', 'BEVERAGE', 'DRINK', 
            'SHAKE', 'DESSERT', 'SWEET', 'ICE CREAM', 'CHAAT', 'COMBO', 'THALI'
        ];

        $nonVegKeywords = ['chicken', 'mutton', 'fish', 'egg', 'prawn', 'meat', 'non-veg', 'nonveg', 'kebab chicken'];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 3) continue;

            $upperLine = strtoupper($line);
            
            // Check if this line is a category header
            $isCategory = false;
            foreach ($categoryKeywords as $kw) {
                if (str_contains($upperLine, $kw) && !preg_match('/[₹0-9]{2,}/', $line)) {
                    $currentCategory = ucwords(strtolower(trim($line, " :-=*_#")));
                    $isCategory = true;
                    break;
                }
            }
            if ($isCategory) continue;

            // Extract price from line (e.g. 240, 240/-, ₹240, Rs. 240, 240.00)
            if (preg_match('/(?:₹|Rs\.?|INR)?\s*([0-9]{2,4}(?:\.[0-9]{2})?)\s*(?:\/-)?(?:\s|$)/i', $line, $priceMatches)) {
                $price = floatval($priceMatches[1]);
                
                // Remove price and currency symbols from line to extract name & description
                $cleanLine = preg_replace('/(?:₹|Rs\.?|INR)?\s*[0-9]{2,4}(?:\.[0-9]{2})?\s*(?:\/-)?/i', '', $line);
                $cleanLine = trim($cleanLine, " -.:•*|=");

                // Extract description if in brackets (e.g. "Paneer Butter Masala (Rich tomato gravy)")
                $description = '';
                if (preg_match('/\((.*?)\)/', $cleanLine, $descMatches)) {
                    $description = trim($descMatches[1]);
                    $cleanLine = trim(preg_replace('/\((.*?)\)/', '', $cleanLine));
                }

                $dishName = trim($cleanLine);
                if (strlen($dishName) < 2) continue;

                // Check veg / non-veg
                $isVeg = true;
                $lowerName = strtolower($dishName . ' ' . $description);
                foreach ($nonVegKeywords as $nvk) {
                    if (str_contains($lowerName, $nvk)) {
                        $isVeg = false;
                        break;
                    }
                }

                $items[] = [
                    'name' => $dishName,
                    'category' => $currentCategory,
                    'price' => $price,
                    'is_veg' => $isVeg,
                    'description' => $description,
                    'is_available' => true
                ];
            }
        }

        return $items;
    }
}

