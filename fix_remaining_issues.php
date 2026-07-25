<?php
// Fix remaining audit issues
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing Orphan order_items ===\n";
// Find orphan order_items (where order_id doesn't exist in orders)
$orphanIds = DB::table('order_items')
    ->whereNotIn('order_id', DB::table('orders')->pluck('id'))
    ->pluck('id');

if ($orphanIds->count() > 0) {
    echo "Found " . $orphanIds->count() . " orphan items. Deleting...\n";
    DB::table('order_items')->whereIn('id', $orphanIds)->delete();
    echo "Deleted " . $orphanIds->count() . " orphan order_items\n";
} else {
    echo "No orphan order_items found\n";
}

echo "\n=== Checking numeric_pin column ===\n";
$cols = array_column(DB::select("DESCRIBE users"), 'Field');
echo "Users columns: " . implode(', ', $cols) . "\n";
$hasPinCol = in_array('numeric_pin', $cols);
echo "Has 'numeric_pin' column: " . ($hasPinCol ? 'YES' : 'NO') . "\n";
if ($hasPinCol) {
    $users = DB::table('users')->select('username', 'role', 'numeric_pin')->get();
    echo "\nUser PINs:\n";
    foreach ($users as $u) {
        $pin = $u->numeric_pin;
        echo "  [{$u->role}] {$u->username} => PIN: " . ($pin ? $pin : 'EMPTY') . "\n";
        if (empty($pin)) {
            // Auto-set default pins
            $defaults = ['admin'=>'1234','manager'=>'5678','cashier'=>'9999','kitchen'=>'2580','waiter'=>'3690'];
            $defaultPin = $defaults[$u->username] ?? $defaults[$u->role] ?? '1234';
            DB::table('users')->where('username', $u->username)->update(['numeric_pin' => $defaultPin]);
            echo "    -> Set default PIN: {$defaultPin}\n";
        }
    }
}

echo "\n=== Tables status cleanup ===\n";
// Fix tables with 'active' status -> should be 'available' or 'occupied'
$activeTables = DB::table('tables')->where('status', 'active')->count();
echo "Tables with 'active' status: {$activeTables}\n";
if ($activeTables > 0) {
    DB::table('tables')->where('status', 'active')->update(['status' => 'available']);
    echo "Fixed: changed 'active' -> 'available'\n";
}

echo "\nDONE\n";
