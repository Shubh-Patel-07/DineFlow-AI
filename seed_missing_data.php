<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Seed inventory
if (DB::table('inventory')->count() == 0) {
    $items = [
        ['item_name'=>'Basmati Rice','category'=>'Grains','quantity'=>25,'unit'=>'kg','low_stock_alert'=>5,'cost_per_unit'=>80],
        ['item_name'=>'Cooking Oil','category'=>'Oils','quantity'=>10,'unit'=>'litre','low_stock_alert'=>3,'cost_per_unit'=>120],
        ['item_name'=>'Onions','category'=>'Vegetables','quantity'=>15,'unit'=>'kg','low_stock_alert'=>4,'cost_per_unit'=>25],
        ['item_name'=>'Tomatoes','category'=>'Vegetables','quantity'=>8,'unit'=>'kg','low_stock_alert'=>3,'cost_per_unit'=>30],
        ['item_name'=>'Chicken','category'=>'Meat','quantity'=>12,'unit'=>'kg','low_stock_alert'=>5,'cost_per_unit'=>250],
        ['item_name'=>'Dal Toor','category'=>'Pulses','quantity'=>20,'unit'=>'kg','low_stock_alert'=>5,'cost_per_unit'=>90],
        ['item_name'=>'Milk','category'=>'Dairy','quantity'=>5,'unit'=>'litre','low_stock_alert'=>2,'cost_per_unit'=>55],
        ['item_name'=>'Garam Masala','category'=>'Spices','quantity'=>3,'unit'=>'kg','low_stock_alert'=>1,'cost_per_unit'=>400],
    ];
    foreach ($items as $i) {
        DB::table('inventory')->insert(array_merge($i, ['restaurant_id'=>1,'created_at'=>now(),'updated_at'=>now()]));
    }
    echo "Inventory: " . count($items) . " items seeded\n";
} else {
    echo "Inventory: already has data (" . DB::table('inventory')->count() . " rows)\n";
}

// Seed coupons
if (DB::table('coupons')->count() == 0) {
    $coupons = [
        ['code'=>'WELCOME10','name'=>'Welcome 10% Off','discount_type'=>'percentage','discount_value'=>10,'min_order_amount'=>200,'max_discount'=>50,'is_active'=>1,'expires_at'=>null,'usage_limit'=>null],
        ['code'=>'FLAT50','name'=>'Flat Rs.50 Off','discount_type'=>'fixed','discount_value'=>50,'min_order_amount'=>300,'max_discount'=>null,'is_active'=>1,'expires_at'=>null,'usage_limit'=>null],
        ['code'=>'LUNCH20','name'=>'Lunch Special 20%','discount_type'=>'percentage','discount_value'=>20,'min_order_amount'=>400,'max_discount'=>100,'is_active'=>1,'expires_at'=>now()->addDays(30),'usage_limit'=>100],
        ['code'=>'VIP30','name'=>'VIP 30% Off','discount_type'=>'percentage','discount_value'=>30,'min_order_amount'=>500,'max_discount'=>150,'is_active'=>1,'expires_at'=>now()->addMonths(3),'usage_limit'=>50],
    ];
    foreach ($coupons as $c) {
        DB::table('coupons')->insert(array_merge($c, ['restaurant_id'=>1,'used_count'=>0,'created_at'=>now(),'updated_at'=>now()]));
    }
    echo "Coupons: " . count($coupons) . " items seeded\n";
} else {
    echo "Coupons: already has data (" . DB::table('coupons')->count() . " rows)\n";
}

// Seed customers
if (DB::table('customers')->count() == 0) {
    $customers = [
        ['name'=>'Rahul Shah','phone'=>'9876543210','email'=>'rahul@gmail.com','total_visits'=>5,'total_spent'=>2450,'loyalty_points'=>245],
        ['name'=>'Priya Patel','phone'=>'9898765432','email'=>'priya@gmail.com','total_visits'=>12,'total_spent'=>6800,'loyalty_points'=>680],
        ['name'=>'Amit Desai','phone'=>'9712345678','email'=>null,'total_visits'=>3,'total_spent'=>1200,'loyalty_points'=>120],
        ['name'=>'Neha Joshi','phone'=>'9823456789','email'=>'neha@yahoo.com','total_visits'=>8,'total_spent'=>3600,'loyalty_points'=>360],
        ['name'=>'Vikram Singh','phone'=>'9654321098','email'=>null,'total_visits'=>1,'total_spent'=>580,'loyalty_points'=>58],
    ];
    foreach ($customers as $c) {
        DB::table('customers')->insert(array_merge($c, ['restaurant_id'=>1,'last_visit'=>now()->subDays(rand(1,30)),'created_at'=>now(),'updated_at'=>now()]));
    }
    echo "Customers: " . count($customers) . " items seeded\n";
} else {
    echo "Customers: already has data (" . DB::table('customers')->count() . " rows)\n";
}
echo "DONE\n";
