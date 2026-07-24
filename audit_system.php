<?php
// Full System Audit Script v2 - with safe column checks
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$issues = [];
$ok = [];

echo "==========================================================\n";
echo "  DINEFLOW AI - FULL SYSTEM AUDIT v2\n";
echo "  " . date('Y-m-d H:i:s') . "\n";
echo "==========================================================\n\n";

// Helper: check if column exists
function columnExists($table, $column) {
    try {
        $cols = array_column(DB::select("DESCRIBE `{$table}`"), 'Field');
        return in_array($column, $cols);
    } catch (Exception $e) { return false; }
}

// ---- 1. DATABASE TABLES ----
echo "### 1. DATABASE TABLE CHECKS ###\n";
$tables = ['users','restaurants','tables','menu_items','orders','order_items','inventory','feedbacks','waiter_calls','coupons','customers','feedback'];
$existingTables = [];
foreach ($tables as $tbl) {
    try {
        $count = DB::table($tbl)->count();
        echo "  ✅ {$tbl}: {$count} rows\n";
        $existingTables[] = $tbl;
        $ok[] = "table:{$tbl}";
    } catch (Exception $e) {
        echo "  ❌ {$tbl}: TABLE MISSING\n";
        $issues[] = "MISSING TABLE: '{$tbl}'";
    }
}

// ---- 2. USERS SCHEMA ----
echo "\n### 2. USERS TABLE SCHEMA ###\n";
try {
    $userCols = array_column(DB::select("DESCRIBE users"), 'Field');
    echo "  Columns: " . implode(', ', $userCols) . "\n";
    $required = ['id','name','email','role'];
    foreach ($required as $col) {
        if (in_array($col, $userCols)) {
            echo "  ✅ Column '{$col}' exists\n";
        } else {
            echo "  ❌ Column '{$col}' MISSING\n";
            $issues[] = "users table missing column: {$col}";
        }
    }
    // Check pin
    $hasPinCol = in_array('pin', $userCols);
    echo "  " . ($hasPinCol ? '✅' : '⚠️') . " Column 'pin': " . ($hasPinCol ? 'exists' : 'MISSING - PIN login will fail!') . "\n";
    if (!$hasPinCol) $issues[] = "CRITICAL: users.pin column missing - PIN-based login broken";

    // Users data
    $selectCols = in_array('pin', $userCols) ? 'id,name,username,email,role,pin' : 'id,name,username,email,role';
    $users = DB::select("SELECT {$selectCols} FROM users");
    echo "\n  Users:\n";
    foreach ($users as $u) {
        $pin = isset($u->pin) ? (!empty($u->pin) ? '****' : 'EMPTY') : 'N/A(col missing)';
        $role = $u->role ?? 'NULL';
        echo "  [" . ($role ? '✅' : '❌') . "] {$u->name} | role={$role} | pin={$pin}\n";
        if (!$role) $issues[] = "User '{$u->name}' has no role";
    }
} catch (Exception $e) {
    echo "  ❌ Users check failed: " . $e->getMessage() . "\n";
    $issues[] = "Users audit failed: " . $e->getMessage();
}

// ---- 3. MENU ITEMS ----
echo "\n### 3. MENU ITEMS ###\n";
try {
    $menuCols = array_column(DB::select("DESCRIBE menu_items"), 'Field');
    echo "  Columns: " . implode(', ', $menuCols) . "\n";
    $menuItems = DB::table('menu_items')->get();
    $menuOk = 0; $menuErr = 0;
    foreach ($menuItems as $item) {
        $prob = [];
        if (empty($item->name)) $prob[] = 'no name';
        if (empty($item->category)) $prob[] = 'no category';
        if (isset($item->price) && $item->price <= 0) $prob[] = 'price=0';
        if (empty($item->restaurant_id)) $prob[] = 'no restaurant_id';
        if (count($prob) > 0) {
            echo "  ⚠️  MenuItem#{$item->id} '{$item->name}': " . implode(', ', $prob) . "\n";
            $menuErr++;
            $issues[] = "MenuItem#{$item->id} '{$item->name}': " . implode(', ', $prob);
        } else {
            $menuOk++;
        }
    }
    echo "  ✅ {$menuOk} items OK" . ($menuErr > 0 ? ", ⚠️ {$menuErr} items with issues" : "") . "\n";
    
    // Check is_available
    $unavailable = DB::table('menu_items')->where('is_available', 0)->count();
    echo "  ⚠️  Unavailable items: {$unavailable}\n";
    if ($unavailable > 0) {
        $names = DB::table('menu_items')->where('is_available', 0)->pluck('name')->toArray();
        echo "  Unavailable: " . implode(', ', $names) . "\n";
    }
} catch (Exception $e) {
    echo "  ❌ Menu check error: " . $e->getMessage() . "\n";
    $issues[] = "Menu items: " . $e->getMessage();
}

// ---- 4. ORDERS ----
echo "\n### 4. ORDERS STATUS ###\n";
try {
    $orderCols = array_column(DB::select("DESCRIBE orders"), 'Field');
    echo "  Columns: " . implode(', ', $orderCols) . "\n";
    
    $statuses = DB::table('orders')->select('status', DB::raw('count(*) as cnt'))->groupBy('status')->get();
    foreach ($statuses as $s) echo "  status={$s->status}: {$s->cnt}\n";
    $payStatuses = DB::table('orders')->select('payment_status', DB::raw('count(*) as cnt'))->groupBy('payment_status')->get();
    foreach ($payStatuses as $p) echo "  payment_status={$p->payment_status}: {$p->cnt}\n";

    // Total Revenue
    $totalRev = DB::table('orders')->where('payment_status','paid')->sum('total_amount');
    echo "  💰 Total paid revenue: Rs." . number_format($totalRev, 2) . "\n";

    // Pending orders > 1 hour
    $stuckOrders = DB::table('orders')
        ->where('payment_status','pending')
        ->where('created_at', '<', now()->subHour())
        ->count();
    if ($stuckOrders > 0) {
        echo "  ⚠️  {$stuckOrders} orders stuck pending for >1 hour\n";
        $issues[] = "{$stuckOrders} orders pending for >1 hour";
    }

    // Orders with no items
    $ordersNoItems = DB::table('orders')
        ->whereNotIn('id', DB::table('order_items')->distinct()->pluck('order_id'))
        ->count();
    if ($ordersNoItems > 0) {
        echo "  ⚠️  {$ordersNoItems} orders have NO items\n";
        $issues[] = "{$ordersNoItems} ghost orders (no order_items)";
    } else {
        echo "  ✅ All orders have items\n";
    }
} catch (Exception $e) {
    echo "  ❌ Orders check: " . $e->getMessage() . "\n";
    $issues[] = "Orders: " . $e->getMessage();
}

// ---- 5. ORDER ITEMS INTEGRITY ----
echo "\n### 5. ORDER ITEMS INTEGRITY ###\n";
try {
    $orphanItems = DB::table('order_items')
        ->whereNotIn('order_id', DB::table('orders')->pluck('id'))
        ->count();
    echo ($orphanItems > 0 ? "  ⚠️" : "  ✅") . " Orphan order_items: {$orphanItems}\n";
    if ($orphanItems > 0) $issues[] = "{$orphanItems} orphan order_items";

    $itemsNoMenu = DB::table('order_items')
        ->whereNotIn('menu_item_id', DB::table('menu_items')->pluck('id'))
        ->count();
    echo ($itemsNoMenu > 0 ? "  ⚠️" : "  ✅") . " Items referencing deleted menu: {$itemsNoMenu}\n";
    if ($itemsNoMenu > 0) $issues[] = "{$itemsNoMenu} order_items reference deleted menu_items";
} catch (Exception $e) {
    echo "  ❌ Order items: " . $e->getMessage() . "\n";
    $issues[] = "Order items: " . $e->getMessage();
}

// ---- 6. TABLES ----
echo "\n### 6. TABLES ###\n";
try {
    $tableCols = array_column(DB::select("DESCRIBE `tables`"), 'Field');
    echo "  Columns: " . implode(', ', $tableCols) . "\n";
    $tCount = DB::table('tables')->count();
    echo "  Total: {$tCount}\n";
    if (in_array('status', $tableCols)) {
        $ts = DB::table('tables')->select('status', DB::raw('count(*) as cnt'))->groupBy('status')->get();
        foreach ($ts as $t) echo "  status={$t->status}: {$t->cnt}\n";
    }
    if ($tCount == 0) $issues[] = "No tables configured";
} catch (Exception $e) {
    echo "  ❌ Tables: " . $e->getMessage() . "\n";
    $issues[] = "Tables: " . $e->getMessage();
}

// ---- 7. INVENTORY ----
echo "\n### 7. INVENTORY ###\n";
if (in_array('inventory', $existingTables)) {
    $invCount = DB::table('inventory')->count();
    $invCols = array_column(DB::select("DESCRIBE inventory"), 'Field');
    echo "  Columns: " . implode(', ', $invCols) . "\n";
    echo "  Total items: {$invCount}\n";
    if (in_array('low_stock_alert', $invCols)) {
        $lowStock = DB::table('inventory')->whereRaw('quantity <= low_stock_alert')->count();
        echo "  Low stock: {$lowStock}\n";
    }
} else {
    echo "  ❌ Inventory table MISSING - needs migration\n";
    $issues[] = "CRITICAL: inventory table missing - Inventory module broken";
}

// ---- 8. FEEDBACK ----
echo "\n### 8. FEEDBACK ###\n";
$fbTable = in_array('feedbacks', $existingTables) ? 'feedbacks' : (in_array('feedback', $existingTables) ? 'feedback' : null);
if ($fbTable) {
    $fbCount = DB::table($fbTable)->count();
    $fbCols = array_column(DB::select("DESCRIBE {$fbTable}"), 'Field');
    echo "  Table: {$fbTable}\n";
    echo "  Columns: " . implode(', ', $fbCols) . "\n";
    echo "  Total: {$fbCount}\n";
    if (in_array('rating', $fbCols)) {
        $avg = DB::table($fbTable)->avg('rating');
        echo "  Avg Rating: " . round($avg, 2) . "/5\n";
    }
} else {
    echo "  ❌ Feedback table MISSING\n";
    $issues[] = "CRITICAL: feedback/feedbacks table missing - Feedback module broken";
}

// ---- 9. COUPONS ----
echo "\n### 9. COUPONS ###\n";
if (in_array('coupons', $existingTables)) {
    $coupCols = array_column(DB::select("DESCRIBE coupons"), 'Field');
    echo "  Columns: " . implode(', ', $coupCols) . "\n";
    $coupCount = DB::table('coupons')->count();
    echo "  Total: {$coupCount}\n";
} else {
    echo "  ❌ Coupons table MISSING\n";
    $issues[] = "CRITICAL: coupons table missing - Coupons module broken";
}

// ---- 10. CUSTOMERS ----
echo "\n### 10. CUSTOMERS ###\n";
if (in_array('customers', $existingTables)) {
    $custCols = array_column(DB::select("DESCRIBE customers"), 'Field');
    echo "  Columns: " . implode(', ', $custCols) . "\n";
    $custCount = DB::table('customers')->count();
    echo "  Total: {$custCount}\n";
} else {
    echo "  ❌ Customers table MISSING\n";
    $issues[] = "CRITICAL: customers table missing - Customers module broken";
}

// ---- 11. WAITER CALLS ----
echo "\n### 11. WAITER CALLS ###\n";
try {
    $wcCols = array_column(DB::select("DESCRIBE waiter_calls"), 'Field');
    echo "  Columns: " . implode(', ', $wcCols) . "\n";
    $wcCount = DB::table('waiter_calls')->count();
    $resolvedCol = in_array('is_resolved', $wcCols) ? 'is_resolved' : (in_array('resolved', $wcCols) ? 'resolved' : null);
    echo "  Total: {$wcCount}\n";
    if ($resolvedCol) {
        $wcPending = DB::table('waiter_calls')->where($resolvedCol, 0)->count();
        echo "  Pending: {$wcPending}\n";
    }
} catch (Exception $e) {
    echo "  ❌ Waiter calls: " . $e->getMessage() . "\n";
    $issues[] = "WaiterCalls: " . $e->getMessage();
}

// ---- 12. MISSING MIGRATIONS ----
echo "\n### 12. MIGRATION STATUS ###\n";
try {
    $migrations = DB::table('migrations')->pluck('migration')->toArray();
    echo "  Total migrations run: " . count($migrations) . "\n";
    $migFiles = glob('database/migrations/*.php');
    $migNames = array_map(fn($f) => pathinfo($f, PATHINFO_FILENAME), $migFiles);
    $notRun = array_diff($migNames, $migrations);
    if (count($notRun) > 0) {
        echo "  ⚠️  Pending migrations:\n";
        foreach ($notRun as $m) {
            echo "    - {$m}\n";
            $issues[] = "Pending migration: {$m}";
        }
    } else {
        echo "  ✅ All migrations run\n";
    }
} catch (Exception $e) {
    echo "  ❌ Migration check: " . $e->getMessage() . "\n";
}

// ---- 13. FRONTEND + PUBLIC SYNC ----
echo "\n### 13. FRONTEND / PUBLIC SYNC ###\n";
$syncCheck = [
    'frontend/admin/utils.js',
    'frontend/admin/menu-manager.html',
    'frontend/admin/menu-manager.js',
    'frontend/admin/tables.html',
    'frontend/admin/tables.js',
    'frontend/admin/inventory.html',
    'frontend/admin/inventory.js',
    'frontend/admin/coupons.html',
    'frontend/admin/coupons.js',
    'frontend/admin/customers.html',
    'frontend/admin/customers.js',
    'frontend/admin/feedback.html',
    'frontend/admin/feedback.js',
    'frontend/admin/staff.html',
    'frontend/admin/staff.js',
    'frontend/admin/settings.html',
    'frontend/admin/admin.css',
];
$outOfSync = [];
foreach ($syncCheck as $f) {
    $pub = 'public/' . $f;
    if (!file_exists($f)) {
        echo "  ❌ MISSING src: {$f}\n";
        $issues[] = "Frontend file missing: {$f}";
    } elseif (!file_exists($pub)) {
        echo "  ❌ MISSING public: {$pub}\n";
        $issues[] = "Public file missing: {$pub}";
        $outOfSync[] = $f;
    } else {
        $match = md5_file($f) === md5_file($pub);
        echo "  " . ($match ? "✅ In sync" : "⚠️  OUT OF SYNC") . ": {$f}\n";
        if (!$match) { $outOfSync[] = $f; $issues[] = "Out of sync: {$f}"; }
    }
}

// ---- SUMMARY ----
echo "\n==========================================================\n";
echo "  AUDIT SUMMARY\n";
echo "==========================================================\n";
if (empty($issues)) {
    echo "  🎉 ALL CHECKS PASSED - System is healthy!\n";
} else {
    echo "  ⚠️  " . count($issues) . " ISSUE(S) FOUND:\n\n";
    foreach ($issues as $i => $iss) {
        echo "  " . ($i+1) . ". {$iss}\n";
    }
}

// Out of sync files
if (count($outOfSync) > 0) {
    echo "\n  OUT-OF-SYNC files need: Copy-Item frontend -> public/frontend\n";
}
echo "\n";
