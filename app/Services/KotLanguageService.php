<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;

class KotLanguageService
{
    /**
     * Complete multi-lingual dictionary for KOT generation.
     */
    public static function getKotDictionary(string $lang = 'en'): array
    {
        $normalized = strtolower(trim($lang));
        if (!in_array($normalized, ['en', 'gu', 'hi'])) {
            $normalized = 'en';
        }

        $dictionaries = [
            'en' => [
                'kitchen_kot' => 'KITCHEN KOT',
                'bar_kot' => 'BAR KOT',
                'order_kot' => 'ORDER KOT',
                'food_only' => '*** FOOD ITEMS ONLY ***',
                'drinks_only' => '*** DRINKS & DESSERTS ONLY ***',
                'all_items' => '*** ALL ITEMS ***',
                'order_id' => 'Order ID',
                'table' => 'Table',
                'time' => 'Time',
                'item_name' => 'Item Name',
                'qty' => 'Qty',
                'instructions' => 'INSTRUCTIONS',
                'no_items' => 'No items for this station',
                'waiter' => 'Waiter',
                'notes' => 'Notes',
                'print_btn_kitchen' => '🖨️ Print Kitchen KOT',
                'print_btn_bar' => '🖨️ Print Bar KOT',
                'print_btn_combined' => '🖨️ Print Combined KOT',
            ],
            'gu' => [
                'kitchen_kot' => 'કિચન KOT (રસોડું)',
                'bar_kot' => 'બાર KOT (પીણાં)',
                'order_kot' => 'ઓર્ડર KOT સ્લિપ',
                'food_only' => '*** માત્ર ભોજનની વાનગીઓ ***',
                'drinks_only' => '*** માત્ર પીણાં અને ડેઝર્ટ ***',
                'all_items' => '*** સમગ્ર ઓર્ડરની વાનગીઓ ***',
                'order_id' => 'ઓર્ડર નં.',
                'table' => 'ટેબલ',
                'time' => 'સમય',
                'item_name' => 'વાનગીનું નામ',
                'qty' => 'જથ્થો',
                'instructions' => 'રસોઈ સૂચનાઓ',
                'no_items' => 'આ સ્ટેશન માટે કોઈ વાનગી નથી',
                'waiter' => 'વેઇટર',
                'notes' => 'નોંધ',
                'print_btn_kitchen' => '🖨️ પ્રિન્ટ કિચન KOT',
                'print_btn_bar' => '🖨️ પ્રિન્ટ બાર KOT',
                'print_btn_combined' => '🖨️ પ્રિન્ટ સમગ્ર KOT',
            ],
            'hi' => [
                'kitchen_kot' => 'किचन KOT (रसोई)',
                'bar_kot' => 'बार KOT (पेय पदार्थ)',
                'order_kot' => 'ऑर्डर KOT पर्ची',
                'food_only' => '*** केवल भोजन के व्यंजन ***',
                'drinks_only' => '*** केवल ड्रिंक्स और डेज़र्ट ***',
                'all_items' => '*** सभी आर्डर व्यंजन ***',
                'order_id' => 'ऑर्डर सं.',
                'table' => 'टेबल',
                'time' => 'समय',
                'item_name' => 'व्यंजन का नाम',
                'qty' => 'मात्रा',
                'instructions' => 'विशेष निर्देश',
                'no_items' => 'इस स्टेशन के लिए कोई व्यंजन नहीं',
                'waiter' => 'वेटर',
                'notes' => 'नोट्स',
                'print_btn_kitchen' => '🖨️ प्रिंट किचन KOT',
                'print_btn_bar' => '🖨️ प्रिंट बार KOT',
                'print_btn_combined' => '🖨️ प्रिंट संयुक्त KOT',
            ]
        ];

        return $dictionaries[$normalized];
    }

    /**
     * Resolve effective language for an order, preserving historical snapshot if set.
     */
    public static function resolveKotLanguage(Order $order): string
    {
        // 1. Check historical order snapshot
        if (!empty($order->kot_language) && in_array(strtolower($order->kot_language), ['en', 'gu', 'hi'])) {
            return strtolower($order->kot_language);
        }

        // 2. Fetch authoritative restaurant setting
        $restaurant = $order->restaurant ?? Restaurant::find($order->restaurant_id);
        if ($restaurant) {
            $settings = $restaurant->settings ?? [];
            if (!empty($settings['language']) && in_array(strtolower($settings['language']), ['en', 'gu', 'hi'])) {
                return strtolower($settings['language']);
            }
            if (!empty($settings['soundbox_lang']) && in_array(strtolower($settings['soundbox_lang']), ['en', 'gu', 'hi'])) {
                return strtolower($settings['soundbox_lang']);
            }
        }

        return 'en';
    }

    /**
     * Get authoritative restaurant language setting.
     */
    public static function getRestaurantLanguage(Restaurant|int $restaurant): string
    {
        $model = is_int($restaurant) ? Restaurant::find($restaurant) : $restaurant;
        if (!$model) return 'en';

        $settings = $model->settings ?? [];
        return strtolower($settings['language'] ?? $settings['soundbox_lang'] ?? 'en');
    }
}
