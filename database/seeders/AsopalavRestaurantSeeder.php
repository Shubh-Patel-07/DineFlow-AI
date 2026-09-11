<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Customer;

class AsopalavRestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or update Asopalav Restaurant
        $restaurant = Restaurant::updateOrCreate(
            ['name' => 'Asopalav'],
            [
                'address' => 'Gopi Avenue, Near Lili Vadi, Patan-Chansama Highway, Patan.',
                'phone' => '9558106440',
                'logo' => null,
                'is_demo' => false,
                'subscription_status' => 'active',
                'plan_type' => 'pro',
                'onboarding_completed' => true,
                'upi_qr' => null,
                'upi_vpa' => null,
                'settings' => [
                    'dietary_preference' => 'pure_veg',
                    'cuisine' => 'Punjabi, Chinese, Sizzler, Continental',
                    'timings' => [
                        'lunch' => '11:00 AM - 03:30 PM',
                        'dinner' => '07:00 PM - 10:30 PM',
                    ],
                    'rules' => [
                        'All Guest are to wait 20-30 minutes after placing the order.',
                        'We Request you to let us know in advance if you have specific Requirement for jain food.',
                        'We Cook most of the food in cotton oil.',
                        'We Stock all the Vegetable in day to day basis considering the freshness and hygiene for our customer.',
                        'Once the order passed through the kitchen it cannot be replaced or cancelled.',
                        'Outside food is not allowed.',
                    ],
                ]
            ]
        );

        $restaurantId = $restaurant->id;

        // 2. Create Asopalav Admin User
        User::updateOrCreate(
            ['email' => 'asopalav@dineflow.in'],
            [
                'username' => 'asopalav_admin',
                'name' => 'Asopalav Restaurant Admin',
                'phone' => '9558106440',
                'password' => Hash::make('asopalav123'),
                'role' => 'admin',
                'restaurant_id' => $restaurantId,
                'is_active' => true,
            ]
        );

        // 3. Clean Transactional Data for Asopalav (Strict 0 orders, 0 revenue, 0 expenses)
        $orderIds = Order::where('restaurant_id', $restaurantId)->pluck('id');
        if ($orderIds->isNotEmpty()) {
            OrderItem::whereIn('order_id', $orderIds)->delete();
            Order::where('restaurant_id', $restaurantId)->delete();
        }
        Expense::where('restaurant_id', $restaurantId)->delete();
        Customer::where('restaurant_id', $restaurantId)->delete();

        // 4. Create 10 Clean Tables (T-01 to T-10)
        $host = rtrim(config('app.url') ?: url('/'), '/');
        for ($i = 1; $i <= 10; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            Table::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'table_number' => 'T-' . $num
                ],
                [
                    'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($host . "/customer/menu?restaurantId={$restaurantId}&tableId=" . $i),
                    'status' => 'active'
                ]
            );
        }

        // 5. Delete existing menu items for Asopalav to ensure clean sync
        MenuItem::where('restaurant_id', $restaurantId)->delete();

        // 6. Complete Asopalav Menu (19 Categories, 183 Items - Exactly from 10-Page PDF)
        $menuData = [
            // Page 2: SUBH SHARUAT (5 items)
            [
                'category' => 'SUBH SHARUAT',
                'items' => [
                    ['name' => 'Fresh Lemon Water', 'price' => 35.00, 'desc' => 'ફ્રેશ લેમન વોટર (220 ml)'],
                    ['name' => 'Fresh Lemon Soda', 'price' => 50.00, 'desc' => 'ફ્રેશ લેમન સોડા (220 ml)'],
                    ['name' => 'Sweet Lassi', 'price' => 50.00, 'desc' => 'સ્વીટ લસ્સી (220 ml)'],
                    ['name' => 'Sp. Lassi', 'price' => 80.00, 'desc' => 'સ્પેશિયલ લસ્સી (230 ml)'],
                    ['name' => 'Mineral Water', 'price' => 20.00, 'desc' => 'મિનરલ વોટર (1 Lt.)'],
                ]
            ],
            // Page 2: SOUP (10 items)
            [
                'category' => 'SOUP',
                'items' => [
                    ['name' => 'Cream of Tomato Soup', 'price' => 100.00, 'desc' => 'ક્રીમ ઓફ ટામેટો સુપ (200 ml)'],
                    ['name' => 'Cheese Corn Tomato Soup', 'price' => 110.00, 'desc' => 'ચીઝ કોર્ન ટોમેટો સુપ (200 ml)'],
                    ['name' => 'Ministroni Soup', 'price' => 110.00, 'desc' => 'મીનીસ્ટ્રોન સુપ (200 ml)'],
                    ['name' => 'Sweet Corn Veg. Soup', 'price' => 100.00, 'desc' => 'સ્વીટ કોર્ન વેજ. સુપ (200 ml)'],
                    ['name' => 'Veg. Manchow Soup', 'price' => 110.00, 'desc' => 'વેજ. મન્ચાઉ સુપ (200 ml)'],
                    ['name' => 'Veg. Manchurian Soup', 'price' => 110.00, 'desc' => 'વેજ. મન્ચુરીયન સુપ (200 ml)'],
                    ['name' => 'Veg. Hot & Sour Soup', 'price' => 110.00, 'desc' => 'વેજ. હોટ એન્ડ સોર સુપ (200 ml)'],
                    ['name' => 'Mushroom Soup', 'price' => 110.00, 'desc' => 'મશરૂમ સુપ (200 ml)'],
                    ['name' => 'Veg. Noodles Soup', 'price' => 110.00, 'desc' => 'વેજ. નુડલ્સ સુપ (200 ml)'],
                    ['name' => 'Veg. Clear Soup', 'price' => 100.00, 'desc' => 'વેજ. ક્લીયર સુપ (200 ml)'],
                ]
            ],
            // Page 2: PENTRY (12 items)
            [
                'category' => 'PENTRY',
                'items' => [
                    ['name' => 'Painaple Raita', 'price' => 70.00, 'desc' => 'પાઈનેપલ રાયતા (250 gm)'],
                    ['name' => 'Bundi Raita', 'price' => 60.00, 'desc' => 'બુન્દી રાયતા (250 gm)'],
                    ['name' => 'Veg. Raita', 'price' => 60.00, 'desc' => 'વેજ. રાયતા (250 gm)'],
                    ['name' => 'Plain Dahi', 'price' => 60.00, 'desc' => 'પ્લેન દહીં (250 gm)'],
                    ['name' => 'Green Salad', 'price' => 70.00, 'desc' => 'ગ્રીન સલાડ (350 gm)'],
                    ['name' => 'Tomato Salad', 'price' => 60.00, 'desc' => 'ટામેટો સલાડ (350 gm)'],
                    ['name' => 'Masala Papad', 'price' => 33.00, 'desc' => 'મસાલા પાપડ (1 Pcs.)'],
                    ['name' => 'Cheese Masala Papad', 'price' => 45.00, 'desc' => 'ચીઝ મસાલા પાપડ (1 Pcs.)'],
                    ['name' => 'Fried Papad', 'price' => 23.00, 'desc' => 'ફ્રાઈડ પાપડ (1 Pcs.)'],
                    ['name' => 'Roasted Papad', 'price' => 22.00, 'desc' => 'રોસ્ટેડ પાપડ (1 Pcs.)'],
                    ['name' => 'Butter Milk', 'price' => 32.00, 'desc' => 'બટર મિલ્ક (200 ml)'],
                    ['name' => 'Masala Butter Milk', 'price' => 35.00, 'desc' => 'મસાલા બટર મિલ્ક (200 ml)'],
                ]
            ],
            // Page 3: STARTER (13 items)
            [
                'category' => 'STARTER',
                'items' => [
                    ['name' => 'Paneer Tikka Dry (Tandoor)', 'price' => 240.00, 'desc' => 'પનીર ટીક્કા ડ્રાય (તંદુર) (230 gm)'],
                    ['name' => 'Schezwan Lollypop', 'price' => 210.00, 'desc' => 'સીઝવાન લોલીપોપ (8 pcs)'],
                    ['name' => 'Hara Bhara Kabab', 'price' => 195.00, 'desc' => 'હરા ભરા કબાબ (10 pcs)'],
                    ['name' => 'Veg. Crispy', 'price' => 200.00, 'desc' => 'વેજ. ક્રિસ્પી (300 gm)'],
                    ['name' => 'Paneer Chilly (Dry/Gravy)', 'price' => 195.00, 'desc' => 'પનીર ચીલ્લી (ડ્રાય/ગ્રેવી) (300 gm)'],
                    ['name' => 'Veg. Manchurian (Dry/Gravy)', 'price' => 170.00, 'desc' => 'વેજ. મન્ચુરીયન (ડ્રાય/ગ્રેવી) (300 gm)'],
                    ['name' => 'Veg. 65 Dry', 'price' => 170.00, 'desc' => 'વેજ. 65 ડ્રાય (300 gm)'],
                    ['name' => 'Mushroom Chilly Dry', 'price' => 180.00, 'desc' => 'મશરૂમ ચીલ્લી ડ્રાય (300 gm)'],
                    ['name' => 'Chinese Bhell', 'price' => 160.00, 'desc' => 'ચાઈનીઝ ભેલ (300 gm)'],
                    ['name' => 'Veg. Hakka Noodles', 'price' => 160.00, 'desc' => 'વેજ. હક્કા નુડલ્સ (300 gm)'],
                    ['name' => 'Veg. Schezwan Noodles', 'price' => 170.00, 'desc' => 'વેજ. સીઝવાન નુડલ્સ (300 gm)'],
                    ['name' => 'Manchurian Noodles', 'price' => 170.00, 'desc' => 'મન્ચુરીયન નુડલ્સ (300 gm)'],
                    ['name' => 'Garlic Noodles', 'price' => 170.00, 'desc' => 'ગાર્લિક નુડલ્સ (300 gm)'],
                ]
            ],
            // Page 3: SIZZLER (4 items)
            [
                'category' => 'SIZZLER',
                'items' => [
                    ['name' => 'Sp. Asopalav Sizzler', 'price' => 345.00, 'desc' => 'સ્પેશિયલ આસોપાલવ સીઝલર (900 gm)'],
                    ['name' => 'Sp. Paneer Stick Sizzler', 'price' => 330.00, 'desc' => 'સ્પેશિયલ પનીર સ્ટીક સીઝલર (900 gm)'],
                    ['name' => 'Sp. Chinese Sizzler', 'price' => 290.00, 'desc' => 'સ્પેશિયલ ચાઈનીઝ સીઝલર (1 kg)'],
                    ['name' => 'Sp. Cheese Hot Pot Sizzler', 'price' => 330.00, 'desc' => 'સ્પેશિયલ ચીઝ હોટ પોટ સીઝલર (1 kg)'],
                ]
            ],
            // Page 3: BACKED DISH (4 items)
            [
                'category' => 'BACKED DISH',
                'items' => [
                    ['name' => 'Backed Macroni', 'price' => 180.00, 'desc' => 'બેકડ મકોની (300 gm)'],
                    ['name' => 'Backed Macroni with P/A', 'price' => 190.00, 'desc' => 'બેકડ મકોની વીથ પાઈનેપલ (300 gm)'],
                    ['name' => 'Backed Dish with Pasta', 'price' => 190.00, 'desc' => 'બેકડ ડીસ વીથ પાસ્તા (300 gm)'],
                    ['name' => 'Backed Pasta with P/A', 'price' => 200.00, 'desc' => 'બેકડ પાસ્તા વીથ પાઈનેપલ (300 gm)'],
                ]
            ],
            // Page 3: SPECIAL CHEESE (6 items)
            [
                'category' => 'SPECIAL CHEESE',
                'items' => [
                    ['name' => 'Cheese Lababdar', 'price' => 200.00, 'desc' => 'ચીઝ લબાબદાર (280 gm)'],
                    ['name' => 'Cheese Kaju Masala', 'price' => 210.00, 'desc' => 'ચીઝ કાજુ મસાલા (280 gm)'],
                    ['name' => 'Cheese Angoori', 'price' => 190.00, 'desc' => 'ચીઝ અંગુરી (250 gm)'],
                    ['name' => 'Cheese Paneer Masala', 'price' => 195.00, 'desc' => 'ચીઝ પનીર મસાલા (250 gm)'],
                    ['name' => 'Cheese Butter Masala', 'price' => 190.00, 'desc' => 'ચીઝ બટર મસાલા (250 gm)'],
                    ['name' => 'Cheese Tawa Masala', 'price' => 200.00, 'desc' => 'ચીઝ તવા મસાલા (350 gm)'],
                ]
            ],
            // Page 4: PANEER (28 items)
            [
                'category' => 'PANEER',
                'items' => [
                    ['name' => 'Sp. Paneer Asopalav', 'price' => 195.00, 'desc' => 'સ્પેશિયલ પનીર આસોપાલવ (330 gm)'],
                    ['name' => 'Sp. Paneer Chingari', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર ચીંગારી (300 gm)'],
                    ['name' => 'Sp. Paneer Rasida', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર રસીદા (300 gm)'],
                    ['name' => 'Sp. Paneer Achari', 'price' => 170.00, 'desc' => 'સ્પેશિયલ પનીર અચારી (300 gm)'],
                    ['name' => 'Sp. Paneer Angara', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર અંગારા (300 gm)'],
                    ['name' => 'Sp. Paneer Patiyala', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર પટીયાલા (300 gm)'],
                    ['name' => 'Sp. Paneer Garlic', 'price' => 200.00, 'desc' => 'સ્પેશિયલ પનીર ગાર્લિક (300 gm)'],
                    ['name' => 'Sp. Paneer Banjara', 'price' => 190.00, 'desc' => 'સ્પેશિયલ પનીર બંઝારા (300 gm)'],
                    ['name' => 'Sp. Paneer Balti', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર બાલ્ટી (300 gm)'],
                    ['name' => 'Sp. Paneer Tawa Masala', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર તવા મસાલા (350 gm)'],
                    ['name' => 'Sp. Paneer Handi Masala', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર હાન્ડી મસાલા (290 gm)'],
                    ['name' => 'Sp. Paneer Akbari', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર અકબરી (330 gm)'],
                    ['name' => 'Sp. Paneer Taka Tak', 'price' => 185.00, 'desc' => 'સ્પેશિયલ પનીર ટકા ટક (350 gm)'],
                    ['name' => 'Sp. Paneer Toofani', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર તુફાની (350 gm)'],
                    ['name' => 'Sp. Paneer Hungama', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર હંગામા (330 gm)'],
                    ['name' => 'Sp. Paneer Navabi', 'price' => 180.00, 'desc' => 'સ્પેશિયલ પનીર નવાબી (350 gm)'],
                    ['name' => 'Paneer Kadai Masala', 'price' => 180.00, 'desc' => 'પનીર કડાઈ મસાલા (300 gm)'],
                    ['name' => 'Paneer Butter Masala', 'price' => 175.00, 'desc' => 'પનીર બટર મસાલા (250 gm)'],
                    ['name' => 'Paneer Tikka Masala', 'price' => 170.00, 'desc' => 'પનીર ટીક્કા મસાલા (250 gm)'],
                    ['name' => 'Paneer Shahi Masala', 'price' => 170.00, 'desc' => 'પનીર શાહી મસાલા (250 gm)'],
                    ['name' => 'Paneer Pasanda', 'price' => 185.00, 'desc' => 'પનીર પસંદા (250 gm)'],
                    ['name' => 'Paneer Chatpata', 'price' => 180.00, 'desc' => 'પનીર ચટપટા (330 gm)'],
                    ['name' => 'Paneer Bhurji', 'price' => 200.00, 'desc' => 'પનીર ભુરજી (250 gm)'],
                    ['name' => 'Paneer Rajwadi', 'price' => 185.00, 'desc' => 'પનીર રજવાડી (330 gm)'],
                    ['name' => 'Paneer Palak', 'price' => 160.00, 'desc' => 'પનીર પાલક (250 gm)'],
                    ['name' => 'Paneer Mutter', 'price' => 160.00, 'desc' => 'પનીર મટર (250 gm)'],
                    ['name' => 'Paneer Corn Masala', 'price' => 160.00, 'desc' => 'પનીર કોર્ન મસાલા (250 gm)'],
                    ['name' => 'Paneer Hara Masala', 'price' => 185.00, 'desc' => 'પનીર હરા મસાલા (350 gm)'],
                ]
            ],
            // Page 5 & 6: PANJABI (43 items: 28 on Page 5 + 15 on Page 6)
            [
                'category' => 'PANJABI',
                'items' => [
                    // Page 5 items (28)
                    ['name' => 'Sp. Veg. Asopalav', 'price' => 180.00, 'desc' => 'સ્પેશિયલ વેજ. આસોપાલવ (350 gm)'],
                    ['name' => 'Sp. Veg. Sam Savera (2 in 1)', 'price' => 190.00, 'desc' => 'સ્પેશિયલ વેજ. શામ સવેરા (2 ઇન 1) (350 gm)'],
                    ['name' => 'Sp. Veg. Afghani', 'price' => 180.00, 'desc' => 'સ્પેશિયલ વેજ. અફઘાની (350 gm)'],
                    ['name' => 'Sp. Veg. Dilli Six Masala', 'price' => 185.00, 'desc' => 'સ્પેશિયલ વેજ. દિલ્લી સિક્સ મસાલા (350 gm)'],
                    ['name' => 'Sp. Veg. Bhuna Masala', 'price' => 180.00, 'desc' => 'સ્પેશિયલ વેજ. ભુના મસાલા (350 gm)'],
                    ['name' => 'Sp. Veg. Akbari', 'price' => 175.00, 'desc' => 'સ્પેશિયલ વેજ. અકબરી (350 gm)'],
                    ['name' => 'Sp. Veg. Mint Masala', 'price' => 185.00, 'desc' => 'સ્પેશિયલ વેજ. મિન્ટ મસાલા (350 gm)'],
                    ['name' => 'Sp. Veg. Two in One (Sweet/Spicy)', 'price' => 195.00, 'desc' => 'સ્પેશિયલ વેજ. ટુ ઇન વન (સ્વીટ/સ્પાઈસી) (350 gm)'],
                    ['name' => 'Sp. Veg. Handi Masala', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. હાન્ડી મસાલા (350 gm)'],
                    ['name' => 'Sp. Veg. Tiranga', 'price' => 175.00, 'desc' => 'સ્પેશિયલ વેજ. તિરંગા (390 gm)'],
                    ['name' => 'Sp. Veg. Taka Tak', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. ટકા ટક (390 gm)'],
                    ['name' => 'Sp. Veg. Family Masala', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. ફેમીલી મસાલા (390 gm)'],
                    ['name' => 'Sp. Veg. Jwalamukhi', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. જવાલામુખી (400 gm)'],
                    ['name' => 'Sp. Veg. Jal Frezy', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. જાલ ફ્રેઝી (390 gm)'],
                    ['name' => 'Sp. Veg. Dopyaza', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. દોપ્યાઝા (390 gm)'],
                    ['name' => 'Sp. Veg. Hungama', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. હંગામા (400 gm)'],
                    ['name' => 'Sp. Veg. Toofani', 'price' => 170.00, 'desc' => 'સ્પેશિયલ વેજ. તુફાની (400 gm)'],
                    ['name' => 'Sp. Veg. Angara', 'price' => 170.00, 'desc' => 'સ્પેશિયલ વેજ. અંગારા (400 gm)'],
                    ['name' => 'Sp. Veg. Tawa Masala', 'price' => 170.00, 'desc' => 'સ્પેશિયલ વેજ. તવા મસાલા (400 gm)'],
                    ['name' => 'Sp. Veg. Hydrabadi', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. હૈદરાબાદી (300 gm)'],
                    ['name' => 'Sp. Veg. Hara Masala', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. હરા મસાલા (400 gm)'],
                    ['name' => 'Sp. Veg. Kolhapuri', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. કોલ્હાપુરી (290 gm)'],
                    ['name' => 'Sp. Veg. Kadai Masala', 'price' => 160.00, 'desc' => 'સ્પેશિયલ વેજ. કડાઈ મસાલા (300 gm)'],
                    ['name' => 'Sp. Veg. Balti', 'price' => 165.00, 'desc' => 'સ્પેશિયલ વેજ. બાલ્ટી (300 gm)'],
                    ['name' => 'Veg. Jaipuri', 'price' => 160.00, 'desc' => 'વેજ. જયપુરી (300 gm)'],
                    ['name' => 'Veg. Singapuri (Sweet)', 'price' => 165.00, 'desc' => 'વેજ. સીંગાપુરી (સ્વીટ) (300 gm)'],
                    ['name' => 'Veg. Navratna Korma (Sweet)', 'price' => 180.00, 'desc' => 'વેજ. નવરત્ન કોરમા (300 gm)'],
                    ['name' => 'Veg. Makhanwala', 'price' => 165.00, 'desc' => 'વેજ. મખ્ખનવાલા (300 gm)'],
                    // Page 6 items (15)
                    ['name' => 'Dum Aloo Punjabi', 'price' => 170.00, 'desc' => 'દમ આલુ પંજાબી (300 gm)'],
                    ['name' => 'Dum Aloo Kasmiri (Sweet)', 'price' => 175.00, 'desc' => 'દમ આલુ કશ્મીરી (300 gm)'],
                    ['name' => 'Mushroom Butter M/S', 'price' => 175.00, 'desc' => 'મશરૂમ બટર મસાલા (280 gm)'],
                    ['name' => 'Mushroom Mutter M/S', 'price' => 170.00, 'desc' => 'મશરૂમ મટર મસાલા (280 gm)'],
                    ['name' => 'Corn Butter Masala', 'price' => 160.00, 'desc' => 'કોર્ન બટર મસાલા (280 gm)'],
                    ['name' => 'Corn Palak', 'price' => 150.00, 'desc' => 'કોર્ન પાલક (280 gm)'],
                    ['name' => 'Plain Palak', 'price' => 130.00, 'desc' => 'પ્લેન પાલક (280 gm)'],
                    ['name' => 'Mix Vegetable', 'price' => 130.00, 'desc' => 'મીક્સ વેજીટેબલ (280 gm)'],
                    ['name' => 'Chana Masala', 'price' => 120.00, 'desc' => 'ચના મસાલા (280 gm)'],
                    ['name' => 'Aloo Mutter', 'price' => 120.00, 'desc' => 'આલુ મટર (280 gm)'],
                    ['name' => 'Aloo Palak', 'price' => 130.00, 'desc' => 'આલુ પાલક (280 gm)'],
                    ['name' => 'Sev Tomato', 'price' => 120.00, 'desc' => 'સેવ ટામેટો (280 gm)'],
                    ['name' => 'Aloo Methi', 'price' => 130.00, 'desc' => 'આલુ મેથી (280 gm)'],
                    ['name' => 'Methi Malai Mutter', 'price' => 150.00, 'desc' => 'મેથી મલાઈ મટર (280 gm)'],
                    ['name' => 'Aloo Suki Bhaji', 'price' => 130.00, 'desc' => 'આલુ સુકી ભાજી (300 gm)'],
                ]
            ],
            // Page 6: DAL (4 items)
            [
                'category' => 'DAL',
                'items' => [
                    ['name' => 'Dal Fry', 'price' => 100.00, 'desc' => 'દાલ ફ્રાય (300 gm)'],
                    ['name' => 'Dal Fry Tadka', 'price' => 120.00, 'desc' => 'દાલ ફ્રાય તડકા (300 gm)'],
                    ['name' => 'Dal Fry Butter', 'price' => 120.00, 'desc' => 'દાલ ફ્રાય બટર (300 gm)'],
                    ['name' => 'Dal Hariyali', 'price' => 120.00, 'desc' => 'દાલ હરીયાલી (300 gm)'],
                ]
            ],
            // Page 6: KAJU SPECIAL (7 items)
            [
                'category' => 'KAJU SPECIAL',
                'items' => [
                    ['name' => 'Khoya Kaju (Sweet)', 'price' => 210.00, 'desc' => 'ખોયા કાજુ (સ્વીટ) (300 gm)'],
                    ['name' => 'Kaju Carry', 'price' => 210.00, 'desc' => 'કાજુ કરી (300 gm)'],
                    ['name' => 'Fry Kaju Handi Masala', 'price' => 210.00, 'desc' => 'ફ્રાય કાજુ હાન્ડી મસાલા (300 gm)'],
                    ['name' => 'Kaju Tawa Masala', 'price' => 210.00, 'desc' => 'કાજુ તવા મસાલા (350 gm)'],
                    ['name' => 'Kaju Butter Masala', 'price' => 210.00, 'desc' => 'કાજુ બટર મસાલા (250 gm)'],
                    ['name' => 'Kaju Paneer Masala', 'price' => 200.00, 'desc' => 'કાજુ પનીર મસાલા (250 gm)'],
                    ['name' => 'Kaju Masala', 'price' => 200.00, 'desc' => 'કાજુ મસાલા (300 gm)'],
                ]
            ],
            // Page 7: KOFTA (7 items)
            [
                'category' => 'KOFTA',
                'items' => [
                    ['name' => 'Kaju Kofta', 'price' => 190.00, 'desc' => 'કાજુ કોફ્તા (250 gm)'],
                    ['name' => 'Paneer Kofta', 'price' => 170.00, 'desc' => 'પનીર કોફ્તા (250 gm)'],
                    ['name' => 'Cheese Kofta', 'price' => 190.00, 'desc' => 'ચીઝ કોફ્તા (250 gm)'],
                    ['name' => 'Malai Kofta (Sweet)', 'price' => 160.00, 'desc' => 'મલાઈ કોફ્તા (સ્વીટ) (250 gm)'],
                    ['name' => 'Nargisi Kofta', 'price' => 150.00, 'desc' => 'નરગીશ કોફ્તા (250 gm)'],
                    ['name' => 'Vegetable Kofta', 'price' => 150.00, 'desc' => 'વેજીટેબલ કોફ્તા (250 gm)'],
                    ['name' => 'Kadai Kofta', 'price' => 150.00, 'desc' => 'કડાઈ કોફ્તા (250 gm)'],
                ]
            ],
            // Page 7: ROTI (18 items)
            [
                'category' => 'ROTI',
                'items' => [
                    ['name' => 'Tandoori Plain Roti', 'price' => 18.00, 'desc' => 'તંદુરી પ્લેન રોટી (1 Pcs.)'],
                    ['name' => 'Tandoori Butter Roti', 'price' => 20.00, 'desc' => 'તંદુરી બટર રોટી (1 Pcs.)'],
                    ['name' => 'Tandoori Plain Paratha', 'price' => 45.00, 'desc' => 'તંદુરી પ્લેન પરોઠા (1 Pcs.)'],
                    ['name' => 'Tandoori Butter Paratha', 'price' => 55.00, 'desc' => 'તંદુરી બટર પરોઠા (1 Pcs.)'],
                    ['name' => 'Plain Kulcha', 'price' => 50.00, 'desc' => 'પ્લેન કુલ્ચા (1 Pcs.)'],
                    ['name' => 'Butter Kulcha', 'price' => 55.00, 'desc' => 'બટર કુલ્ચા (1 Pcs.)'],
                    ['name' => 'Plain Nan', 'price' => 55.00, 'desc' => 'પ્લેન નાન (1 Pcs.)'],
                    ['name' => 'Butter Nan', 'price' => 60.00, 'desc' => 'બટર નાન (1 Pcs.)'],
                    ['name' => 'Stuff Kulcha', 'price' => 80.00, 'desc' => 'સ્ટફ કુલ્ચા (1 Pcs.)'],
                    ['name' => 'Stuff Nan', 'price' => 90.00, 'desc' => 'સ્ટફ નાન (1 Pcs.)'],
                    ['name' => 'Cheese Nan', 'price' => 110.00, 'desc' => 'ચીઝ નાન (1 Pcs.)'],
                    ['name' => 'Karshmiri Nan', 'price' => 100.00, 'desc' => 'કશ્મીરી નાન (1 Pcs.)'],
                    ['name' => 'Garlic Nan', 'price' => 100.00, 'desc' => 'ગાર્લિક નાન (1 Pcs.)'],
                    ['name' => 'Aloo Parotha', 'price' => 80.00, 'desc' => 'આલુ પરોઠા (1 Pcs.)'],
                    ['name' => 'Cheese Garlic Nan', 'price' => 120.00, 'desc' => 'ચીઝ ગાર્લિક નાન (1 Pcs.)'],
                    ['name' => 'Cheese Chilli Garlic Nan', 'price' => 130.00, 'desc' => 'ચીઝ ચીલ્લી ગાર્લિક નાન (1 Pcs.)'],
                    ['name' => 'Plain Chapati', 'price' => 15.00, 'desc' => 'પ્લેન ચપાતી (1 Pcs.)'],
                    ['name' => 'Butter Chapati', 'price' => 16.00, 'desc' => 'બટર ચપાતી (1 Pcs.)'],
                ]
            ],
            // Page 8: CHINESE RICE (6 items)
            [
                'category' => 'CHINESE RICE',
                'items' => [
                    ['name' => 'Schezwan Fried Rice', 'price' => 160.00, 'desc' => 'સીઝવાન ફ્રાઈડ રાઈસ (220 gm)'],
                    ['name' => 'Hong Kong Fried Rice', 'price' => 160.00, 'desc' => 'હોંગ કોંગ ફ્રાઈડ રાઈસ (220 gm)'],
                    ['name' => 'Onion Fried Rice', 'price' => 140.00, 'desc' => 'ઓનિયન ફ્રાઈડ રાઈસ (220 gm)'],
                    ['name' => 'Veg. Fried Rice', 'price' => 150.00, 'desc' => 'વેજ. ફ્રાઈડ રાઈસ (220 gm)'],
                    ['name' => 'Chinese Fried Rice', 'price' => 150.00, 'desc' => 'ચાઈનીઝ ફ્રાઈડ રાઈસ (230 gm)'],
                    ['name' => 'Manchurian Fried Rice', 'price' => 160.00, 'desc' => 'મન્ચુરીયન ફ્રાઈડ રાઈસ (230 gm)'],
                ]
            ],
            // Page 8: RICE (10 items)
            [
                'category' => 'RICE',
                'items' => [
                    ['name' => 'Sp. Asopalav Biriyani', 'price' => 160.00, 'desc' => 'સ્પેશિયલ આસોપાલવ બીરીયાની (240 gm)'],
                    ['name' => 'Veg. Handi Biriyani', 'price' => 150.00, 'desc' => 'વેજ. હાન્ડી બીરીયાની (240 gm)'],
                    ['name' => 'Veg. Hydrabadi Biriyani', 'price' => 150.00, 'desc' => 'વેજ. હૈદરાબાદી બીરીયાની (240 gm)'],
                    ['name' => 'Veg. Biriyani', 'price' => 150.00, 'desc' => 'વેજ. બીરીયાની (240 gm)'],
                    ['name' => 'Veg. Pulav', 'price' => 130.00, 'desc' => 'વેજ. પુલાવ (220 gm)'],
                    ['name' => 'Pease Pulav', 'price' => 120.00, 'desc' => 'પિસ પુલાવ (210 gm)'],
                    ['name' => 'Kaju Pulav', 'price' => 180.00, 'desc' => 'કાજુ પુલાવ (210 gm)'],
                    ['name' => 'Kashmiri Pulav (Sweet)', 'price' => 160.00, 'desc' => 'કાશ્મીરી પુલાવ (સ્વીટ) (220 gm)'],
                    ['name' => 'Jeera Rice', 'price' => 100.00, 'desc' => 'જીરા રાઈસ (230 gm)'],
                    ['name' => 'Steam Rice', 'price' => 90.00, 'desc' => 'સ્ટીમ રાઈસ (220 gm)'],
                ]
            ],
            // Page 9: ICE CREAM (4 items)
            [
                'category' => 'ICE CREAM',
                'items' => [
                    ['name' => 'Kaju Draksh Ice Cream', 'price' => 50.00, 'desc' => 'કાજુ દ્રાક્ષ આઈસ્ક્રીમ (80 ml)'],
                    ['name' => 'Badam Carnival Ice Cream', 'price' => 50.00, 'desc' => 'બદામ કાર્નિવાલ આઈસ્ક્રીમ (80 ml)'],
                    ['name' => 'Cherry Berry Ice Cream', 'price' => 40.00, 'desc' => 'ચેરી બેરી આઈસ્ક્રીમ (80 ml)'],
                    ['name' => 'Vanilla Ice Cream', 'price' => 40.00, 'desc' => 'વેનીલા આઈસ્ક્રીમ (80 ml)'],
                ]
            ],
            // Page 9: MILK SHEK (6 items)
            [
                'category' => 'MILK SHEK',
                'items' => [
                    ['name' => 'Kaju Draksh Milk Shake', 'price' => 100.00, 'desc' => 'કાજુ દ્રાક્ષ મિલ્ક શેક (230 ml)'],
                    ['name' => 'Badam Carnival Milk Shake', 'price' => 110.00, 'desc' => 'બદામ કાર્નિવાલ મિલ્ક શેક (230 ml)'],
                    ['name' => 'Cherry Berry Milk Shake', 'price' => 95.00, 'desc' => 'ચેરી બેરી મિલ્ક શેક (230 ml)'],
                    ['name' => 'Vanilla Milk Shake', 'price' => 90.00, 'desc' => 'વેનીલા મિલ્ક શેક (230 ml)'],
                    ['name' => 'Cold Coffee with Ice-C', 'price' => 100.00, 'desc' => 'કોલ્ડ કોફી વીથ આઈસ્ક્રીમ (230 ml)'],
                    ['name' => 'Cold Coffee', 'price' => 90.00, 'desc' => 'કોલ્ડ કોફી (230 ml)'],
                ]
            ],
            // Page 9: COLD DRINKS (2 items)
            [
                'category' => 'COLD DRINKS',
                'items' => [
                    ['name' => 'Thums Up', 'price' => 20.00, 'desc' => 'થમ્સ અપ (200 ml / MRP)'],
                    ['name' => 'Fanta, Maza, Sprite', 'price' => 20.00, 'desc' => 'ફેન્ટા, માઝા, સ્પ્રાઈટ (200 ml / MRP)'],
                ]
            ],
            // Page 10: ONLY LUNCH TIME (4 items)
            [
                'category' => 'ONLY LUNCH TIME',
                'items' => [
                    [
                        'name' => 'Sp. Fix Delux Lunch Rs. 210',
                        'price' => 210.00,
                        'desc' => 'સ્પેશિયલ ફિક્સ ડીલક્સ લંચ (400gm) - Tomato Soup, Green Salad, Veg. Man. Dry (3 Pis), 1-Paneer, 1-Veg., Dal Fry, Veg. Pulav, 3 Butter Roti, 1 Papad, Butter Milk, Ice Cream'
                    ],
                    [
                        'name' => 'Fix Delux Lunch Rs. 200',
                        'price' => 200.00,
                        'desc' => 'ફિક્સ ડીલક્સ લંચ (350gm) - Tomato Soup, Paneer Sabji, Veg. Sabji, Dal, Jeera Rice, 1 Papad, Butter Milk, Green Salad, 3 Butter Roti, Ice Cream'
                    ],
                    [
                        'name' => 'Fix Lunch Rs. 170',
                        'price' => 170.00,
                        'desc' => 'ફિક્સ લંચ (350gm) - Paneer Sabji, Veg. Sabji, Dal, Jeera Rice, 1 Papad, Butter Milk, 3 Butter Roti, Onion, Lemon'
                    ],
                    [
                        'name' => 'Pack Lunch Rs. 170',
                        'price' => 170.00,
                        'desc' => 'પેક લંચ (350gm) - Paneer Sabji, Veg. Sabji, Dal, Jeera Rice, 1 Papad, Butter Milk, 3 Butter Roti, Onion, Lemon'
                    ],
                ]
            ],
        ];

        $totalInserted = 0;
        foreach ($menuData as $group) {
            $categoryName = $group['category'];
            foreach ($group['items'] as $item) {
                MenuItem::create([
                    'restaurant_id' => $restaurantId,
                    'name' => $item['name'],
                    'description' => $item['desc'],
                    'price' => $item['price'],
                    'category' => $categoryName,
                    'image' => null,
                    'is_available' => true,
                    'is_veg' => true,
                ]);
                $totalInserted++;
            }
        }

        $this->command->info("Successfully provisioned Asopalav tenant (ID: {$restaurantId}) with {$totalInserted} menu items across 19 categories!");
    }
}
