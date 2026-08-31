<?php

return [
    'provider' => env('VR_DESIGN_PROVIDER', 'mock'),
    'styles' => [
        'modern' => ['name' => 'Modern', 'description' => 'Clean lines and contemporary furniture', 'color_palette' => ['#FFFFFF', '#000000', '#808080', '#C0C0C0']],
        'traditional' => ['name' => 'Traditional', 'description' => 'Classic furniture and warm colors', 'color_palette' => ['#8B4513', '#DEB887', '#F5DEB3', '#CD853F']],
        'minimalist' => ['name' => 'Minimalist', 'description' => 'Sparse furniture and simple decor', 'color_palette' => ['#FFFFFF', '#F5F5F5', '#E8E8E8', '#D3D3D3']],
        'luxury' => ['name' => 'Luxury', 'description' => 'Premium materials and elegant details', 'color_palette' => ['#FFD700', '#FFFFFF', '#000000', '#8B0000']],
        'industrial' => ['name' => 'Industrial', 'description' => 'Exposed elements, metal, and brick', 'color_palette' => ['#696969', '#A9A9A9', '#8B4513', '#000000']],
        'scandinavian' => ['name' => 'Scandinavian', 'description' => 'Light wood and functional minimalism', 'color_palette' => ['#FFFFFF', '#F5F5DC', '#D2B48C', '#87CEEB']],
        'contemporary' => ['name' => 'Contemporary', 'description' => 'Current trends and bold accents', 'color_palette' => ['#FFFFFF', '#000000', '#FF6B6B', '#4ECDC4']],
        'rustic' => ['name' => 'Rustic', 'description' => 'Natural materials and country charm', 'color_palette' => ['#8B4513', '#D2691E', '#F4A460', '#DEB887']],
    ],
    'furniture_categories' => [
        'seating' => ['Sofa', 'Armchair', 'Dining Chair', 'Bench', 'Ottoman'],
        'tables' => ['Dining Table', 'Coffee Table', 'Side Table', 'Desk', 'Console Table'],
        'storage' => ['Bookshelf', 'Cabinet', 'Wardrobe', 'Dresser', 'TV Stand'],
        'beds' => ['King Bed', 'Queen Bed', 'Single Bed', 'Bunk Bed'],
        'decor' => ['Rug', 'Artwork', 'Plant', 'Lamp', 'Mirror', 'Curtains'],
        'lighting' => ['Ceiling Light', 'Floor Lamp', 'Table Lamp', 'Wall Sconce'],
    ],
    'room_types' => ['living_room' => 'Living Room', 'bedroom' => 'Bedroom', 'kitchen' => 'Kitchen', 'bathroom' => 'Bathroom', 'dining_room' => 'Dining Room', 'office' => 'Office', 'hallway' => 'Hallway', 'balcony' => 'Balcony'],
    'supported_devices' => ['oculus_quest' => 'Meta Quest (Quest 2, Quest 3)', 'htc_vive' => 'HTC Vive', 'valve_index' => 'Valve Index', 'psvr' => 'PlayStation VR', 'windows_mr' => 'Windows Mixed Reality', 'cardboard' => 'Google Cardboard', 'browser' => 'WebXR-compatible browsers'],
    'storage' => ['disk' => env('VR_DESIGN_STORAGE_DISK', 'public'), 'path' => 'vr-designs', 'thumbnail_path' => 'vr-designs/thumbnails'],
    'cache' => ['enabled' => env('VR_DESIGN_CACHE_ENABLED', true), 'ttl' => env('VR_DESIGN_CACHE_TTL', 3600), 'prefix' => 'vr_design_'],
];
