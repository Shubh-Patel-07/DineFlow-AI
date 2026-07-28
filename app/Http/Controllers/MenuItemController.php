<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request, $restaurantId = null)
    {
        $restaurantId = (string)($restaurantId ?? $request->query('restaurantId', '1'));

        $items = MenuItem::where('restaurant_id', $restaurantId)->get();

        if ($items->isEmpty()) {
            // Auto-seed default menu items if DB is empty
            $defaultItems = [
                ['name' => 'Paneer Butter Masala', 'category' => 'Mains',     'price' => 240.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=500&auto=format&fit=crop&q=60', 'description' => 'Rich cottage cheese cubes cooked in butter tomato gravy.'],
                ['name' => 'Chicken Tikka Masala',  'category' => 'Mains',     'price' => 320.00, 'is_veg' => false, 'image' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500&auto=format&fit=crop&q=60', 'description' => 'Tender chicken pieces simmered in spiced tikka gravy.'],
                ['name' => 'Garlic Naan',           'category' => 'Breads',    'price' => 60.00,  'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=500&auto=format&fit=crop&q=60', 'description' => 'Clay oven baked flatbread brushed with butter and minced garlic.'],
                ['name' => 'Veg Spring Rolls',      'category' => 'Starters',  'price' => 180.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500&auto=format&fit=crop&q=60', 'description' => 'Crispy fried rolls filled with seasoned vegetables.'],
                ['name' => 'Chicken Biryani',       'category' => 'Rice',      'price' => 380.00, 'is_veg' => false, 'image' => 'https://images.unsplash.com/photo-1633945274405-b6c8069047b0?w=500&auto=format&fit=crop&q=60', 'description' => 'Fragrant basmati rice layered with spiced marinated chicken.'],
                ['name' => 'Mango Lassi',           'category' => 'Drinks',    'price' => 120.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=60', 'description' => 'Creamy yogurt drink blended with fresh sweet Alphonso mango pulp.'],
                ['name' => 'Gulab Jamun',           'category' => 'Desserts',  'price' => 80.00,  'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=500&auto=format&fit=crop&q=60', 'description' => 'Soft milk dumplings soaked in cardamom sugar syrup.'],
                ['name' => 'Dal Tadka',             'category' => 'Mains',     'price' => 180.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500&auto=format&fit=crop&q=60', 'description' => 'Yellow lentils tempered with ghee, cumin seeds, garlic and chili.'],
                ['name' => 'Margherita Pizza',      'category' => 'Pizza',     'price' => 299.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500&auto=format&fit=crop&q=60', 'description' => 'Classic Italian pizza topped with mozzarella and basil.'],
                ['name' => 'Cheese Burger',         'category' => 'Burger',    'price' => 199.00, 'is_veg' => true,  'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&auto=format&fit=crop&q=60', 'description' => 'Juicy patty filled with melted cheese, lettuce and signature sauce.']
            ];

            foreach ($defaultItems as $dItem) {
                MenuItem::create([
                    'restaurant_id' => $restaurantId,
                    'name' => $dItem['name'],
                    'description' => $dItem['description'],
                    'price' => $dItem['price'],
                    'category' => $dItem['category'],
                    'image' => $dItem['image'],
                    'is_available' => true,
                    'is_veg' => $dItem['is_veg']
                ]);
            }
            $items = MenuItem::where('restaurant_id', $restaurantId)->get();
        }

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $item = MenuItem::create([
            'restaurant_id' => (string)($request->restaurant_id ?? 1),
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
        $item = MenuItem::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found'], 404);
        }

        $data = $request->only([
            'name', 'description', 'price', 'category', 'image', 'is_available', 'is_veg'
        ]);

        if (array_key_exists('is_available', $data)) {
            $data['is_available'] = filter_var($data['is_available'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('is_veg', $data)) {
            $data['is_veg'] = filter_var($data['is_veg'], FILTER_VALIDATE_BOOLEAN);
        }

        $item->update($data);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = MenuItem::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Menu item not found'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Menu item deleted']);
    }
}

