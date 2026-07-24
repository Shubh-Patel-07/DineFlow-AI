<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 7-Day Sales Trend ===\n";
$weeklySalesLabels = [];
$weeklySalesData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = Carbon\Carbon::today()->subDays($i);
    $weeklySalesLabels[] = $date->format('D');
    $total = App\Models\Order::whereDate('created_at', $date)->where('payment_status', 'paid')->sum('total_amount');
    $weeklySalesData[] = $total;
    echo $date->format('D d/m') . " => Rs." . $total . "\n";
}

echo "\n=== Category Sales Distribution ===\n";
try {
    $categorySales = DB::table('order_items')
        ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->where('orders.payment_status', 'paid')
        ->select('menu_items.category', DB::raw('SUM(order_items.quantity * order_items.price) as total'))
        ->groupBy('menu_items.category')
        ->pluck('total', 'category')
        ->toArray();

    if (empty($categorySales)) {
        echo "No category data found — using fallback defaults.\n";
        $categorySales = ['Mains' => 1200, 'Starters' => 800, 'Beverages' => 450, 'Desserts' => 300];
    }

    foreach ($categorySales as $cat => $total) {
        echo "$cat => Rs.$total\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== menu_items category column check ===\n";
try {
    $cols = DB::select("DESCRIBE menu_items");
    foreach ($cols as $col) {
        if (strpos($col->Field, 'categ') !== false || strpos($col->Field, 'type') !== false) {
            echo "Column: {$col->Field} | Type: {$col->Type}\n";
        }
    }
} catch (Exception $e) {
    echo "DESCRIBE error: " . $e->getMessage() . "\n";
}
