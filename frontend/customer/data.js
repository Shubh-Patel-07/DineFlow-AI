const restaurant = {
    name: "DineFlow Restaurant",
    rating: 4.8,
    status: "Open Now",
    location: "Patan, Gujarat",
    logo: "assets/images/p1.png"
};


const foodItems = [

{
    id:1,
    name:"Margherita Pizza",
    category:"Pizza",
    price:299,
    rating:4.8,
    badge:"Best Seller",
    image:"https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800",
    description:"Classic Italian pizza topped with mozzarella cheese, fresh tomatoes and basil leaves.",
    available:true
},

{
    id:2,
    name:"Veg Supreme Pizza",
    category:"Pizza",
    price:399,
    rating:4.9,
    badge:"Popular",
    image:"https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=800",
    description:"Loaded with fresh vegetables, olives, capsicum, onions and extra cheese.",
    available:true
},

{
    id:3,
    name:"Cheese Burger",
    category:"Burger",
    price:199,
    rating:4.7,
    badge:"Hot",
    image:"https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800",
    description:"Juicy burger filled with cheese, lettuce, tomato and signature sauce.",
    available:true,
    isVeg:false
},

{
    id:4,
    name:"Double Patty Burger",
    category:"Burger",
    price:279,
    rating:4.9,
    badge:"Trending",
    image:"https://images.unsplash.com/photo-1550547660-d9450f859349?w=800",
    description:"Double grilled patties with melted cheese and crispy vegetables.",
    available:true,
    isVeg:false
},

{
    id:5,
    name:"White Sauce Pasta",
    category:"Pasta",
    price:249,
    rating:4.6,
    badge:"Chef Choice",
    image:"https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800",
    description:"Creamy white sauce pasta with herbs and parmesan cheese.",
    available:true
},

{
    id:6,
    name:"Red Sauce Pasta",
    category:"Pasta",
    price:239,
    rating:4.5,
    badge:"Fresh",
    image:"https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?w=800",
    description:"Italian tomato sauce pasta served with garlic bread.",
    available:true
},

{
    id:7,
    name:"Cold Coffee",
    category:"Drinks",
    price:149,
    rating:4.8,
    badge:"Cool",
    image:"https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=800",
    description:"Refreshing cold coffee topped with whipped cream.",
    available:true
},

{
    id:8,
    name:"Mango Smoothie",
    category:"Drinks",
    price:169,
    rating:4.7,
    badge:"Summer",
    image:"https://images.unsplash.com/photo-1623065422902-30a2d299bbe4?w=800",
    description:"Fresh mango smoothie prepared with natural fruits.",
    available:true
},

{
    id:9,
    name:"Chocolate Cake",
    category:"Dessert",
    price:189,
    rating:4.9,
    badge:"Sweet",
    image:"https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800",
    description:"Soft chocolate cake with rich chocolate cream.",
    available:true
},

{
    id:10,
    name:"Ice Cream Sundae",
    category:"Dessert",
    price:179,
    rating:4.8,
    badge:"Favorite",
    image:"https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800",
    description:"Vanilla ice cream served with chocolate syrup and nuts.",
    available:true
},

{ id:11, name:"Paneer Masala", category:"Main Course", price:269, rating:4.8, badge:"Chef Special", image:"https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=800", description:"Soft paneer cubes cooked in a rich, aromatic tomato and onion masala gravy.", available:true, isVeg:true },

{ id:12, name:"Special DineFlow Veg", category:"Main Course", price:299, rating:4.9, badge:"Signature", image:"https://images.unsplash.com/photo-1645177628172-a94c1f96e6db?w=800", description:"A special DineFlow mixed vegetable curry prepared with fresh seasonal vegetables and house spices.", available:true, isVeg:true },

{ id:13, name:"Veg Kofta", category:"Main Course", price:249, rating:4.7, badge:"Popular", image:"https://images.unsplash.com/photo-1596797038530-2c107229654b?w=800", description:"Crispy vegetable koftas served in a smooth and flavourful creamy gravy.", available:true, isVeg:true },

{ id:14, name:"Chapati Roti (Oil/Butter)", category:"Breads", price:30, rating:4.6, badge:"Fresh", image:"https://unsplash.com/photos/EGNB5qZfscw/download?force=true&w=800", description:"Fresh soft chapati served with your choice of oil or butter.", available:true, isVeg:true },

{ id:15, name:"Tomato Soup", category:"Soup", price:119, rating:4.7, badge:"Hot", image:"https://unsplash.com/photos/hWDYfmyAz4g/download?force=true&w=800", description:"Warm, creamy tomato soup made with ripe tomatoes and mild herbs.", available:true, isVeg:true },

{ id:16, name:"Chinese Soup", category:"Soup", price:129, rating:4.6, badge:"Hot", image:"https://unsplash.com/photos/ZejuCsw8Ofw/download?force=true&w=800", description:"Chinese-style vegetable soup with fresh vegetables and aromatic seasoning.", available:true, isVeg:true },

{ id:17, name:"Roasted Papad", category:"Starters", price:35, rating:4.6, badge:"Crispy", image:"assests/images/roasted-papad.svg", description:"Light and crispy roasted papad served with traditional seasoning.", available:true, isVeg:true },

{ id:18, name:"Chhas", category:"Beverage", price:30, rating:4.8, badge:"Popular", image:"assests/images/chhas.svg", description:"Fresh traditional Gujarati buttermilk served chilled.", available:true, isVeg:true, popular:true }

];
