<?php
// Données de présentation pour la maquette finale de la Personne 1.

$cities = [
    "Paris", "Lyon", "Marseille", "Bordeaux", "Lille", "Nice", "Nantes", "Toulouse",
    "Lisbonne", "Marrakech", "Santorin", "Bali", "Denpasar", "Kyoto", "Chamonix", "Genève", "Athènes", "Rome",
    "Barcelone", "Amsterdam", "Londres", "New York", "Tokyo"
];

$cityCoordinates = [
    "Paris" => ["lat" => 48.8566, "lng" => 2.3522, "x" => 48, "y" => 36],
    "Lyon" => ["lat" => 45.7640, "lng" => 4.8357, "x" => 49, "y" => 38],
    "Marseille" => ["lat" => 43.2965, "lng" => 5.3698, "x" => 50, "y" => 41],
    "Bordeaux" => ["lat" => 44.8378, "lng" => -0.5792, "x" => 47, "y" => 39],
    "Lisbonne" => ["lat" => 38.7223, "lng" => -9.1393, "x" => 44, "y" => 43],
    "Marrakech" => ["lat" => 31.6295, "lng" => -7.9811, "x" => 45, "y" => 50],
    "Santorin" => ["lat" => 36.3932, "lng" => 25.4615, "x" => 56, "y" => 44],
    "Bali" => ["lat" => -8.3405, "lng" => 115.0920, "x" => 80, "y" => 64],
    "Kyoto" => ["lat" => 35.0116, "lng" => 135.7681, "x" => 88, "y" => 44],
    "Chamonix" => ["lat" => 45.9237, "lng" => 6.8694, "x" => 50, "y" => 38],
    "Tokyo" => ["lat" => 35.6762, "lng" => 139.6503, "x" => 89, "y" => 44],
    "New York" => ["lat" => 40.7128, "lng" => -74.0060, "x" => 28, "y" => 39],
    "Athènes" => ["lat" => 37.9838, "lng" => 23.7275, "x" => 55, "y" => 43],
    "Genève" => ["lat" => 46.2044, "lng" => 6.1432, "x" => 50, "y" => 38],
    "Denpasar" => ["lat" => -8.6500, "lng" => 115.2167, "x" => 80, "y" => 64],
    "Lille" => ["lat" => 50.6292, "lng" => 3.0573],
    "Nice" => ["lat" => 43.7102, "lng" => 7.2620],
    "Nantes" => ["lat" => 47.2184, "lng" => -1.5536],
    "Toulouse" => ["lat" => 43.6047, "lng" => 1.4442],
    "Rome" => ["lat" => 41.9028, "lng" => 12.4964],
    "Barcelone" => ["lat" => 41.3874, "lng" => 2.1686],
    "Amsterdam" => ["lat" => 52.3676, "lng" => 4.9041],
    "Londres" => ["lat" => 51.5072, "lng" => -0.1276],
    "Madrid" => ["lat" => 40.4168, "lng" => -3.7038],
    "Berlin" => ["lat" => 52.5200, "lng" => 13.4050],
    "Bruxelles" => ["lat" => 50.8503, "lng" => 4.3517],
    "Zurich" => ["lat" => 47.3769, "lng" => 8.5417],
    "Vienne" => ["lat" => 48.2082, "lng" => 16.3738],
    "Prague" => ["lat" => 50.0755, "lng" => 14.4378],
    "Budapest" => ["lat" => 47.4979, "lng" => 19.0402],
    "Copenhague" => ["lat" => 55.6761, "lng" => 12.5683],
    "Stockholm" => ["lat" => 59.3293, "lng" => 18.0686],
    "Oslo" => ["lat" => 59.9139, "lng" => 10.7522],
    "Helsinki" => ["lat" => 60.1699, "lng" => 24.9384],
    "Dublin" => ["lat" => 53.3498, "lng" => -6.2603],
    "Edimbourg" => ["lat" => 55.9533, "lng" => -3.1883],
    "Milan" => ["lat" => 45.4642, "lng" => 9.1900],
    "Venise" => ["lat" => 45.4408, "lng" => 12.3155],
    "Florence" => ["lat" => 43.7696, "lng" => 11.2558],
    "Naples" => ["lat" => 40.8518, "lng" => 14.2681],
    "Porto" => ["lat" => 41.1579, "lng" => -8.6291],
    "Séville" => ["lat" => 37.3891, "lng" => -5.9845],
    "Valence" => ["lat" => 39.4699, "lng" => -0.3763],
    "Ibiza" => ["lat" => 38.9067, "lng" => 1.4206],
    "Palma" => ["lat" => 39.5696, "lng" => 2.6502],
    "Malte" => ["lat" => 35.9375, "lng" => 14.3754],
    "Istanbul" => ["lat" => 41.0082, "lng" => 28.9784],
    "Le Caire" => ["lat" => 30.0444, "lng" => 31.2357],
    "Dubaï" => ["lat" => 25.2048, "lng" => 55.2708],
    "Doha" => ["lat" => 25.2854, "lng" => 51.5310],
    "Abu Dhabi" => ["lat" => 24.4539, "lng" => 54.3773],
    "Riyad" => ["lat" => 24.7136, "lng" => 46.6753],
    "Amman" => ["lat" => 31.9539, "lng" => 35.9106],
    "Tel Aviv" => ["lat" => 32.0853, "lng" => 34.7818],
    "Casablanca" => ["lat" => 33.5731, "lng" => -7.5898],
    "Rabat" => ["lat" => 34.0209, "lng" => -6.8416],
    "Tanger" => ["lat" => 35.7595, "lng" => -5.8340],
    "Agadir" => ["lat" => 30.4278, "lng" => -9.5981],
    "Tunis" => ["lat" => 36.8065, "lng" => 10.1815],
    "Alger" => ["lat" => 36.7538, "lng" => 3.0588],
    "Dakar" => ["lat" => 14.7167, "lng" => -17.4677],
    "Le Cap" => ["lat" => -33.9249, "lng" => 18.4241],
    "Nairobi" => ["lat" => -1.2921, "lng" => 36.8219],
    "Zanzibar" => ["lat" => -6.1659, "lng" => 39.2026],
    "Johannesburg" => ["lat" => -26.2041, "lng" => 28.0473],
    "Montréal" => ["lat" => 45.5019, "lng" => -73.5674],
    "Toronto" => ["lat" => 43.6532, "lng" => -79.3832],
    "Vancouver" => ["lat" => 49.2827, "lng" => -123.1207],
    "Miami" => ["lat" => 25.7617, "lng" => -80.1918],
    "Los Angeles" => ["lat" => 34.0522, "lng" => -118.2437],
    "San Francisco" => ["lat" => 37.7749, "lng" => -122.4194],
    "Las Vegas" => ["lat" => 36.1716, "lng" => -115.1391],
    "Chicago" => ["lat" => 41.8781, "lng" => -87.6298],
    "Boston" => ["lat" => 42.3601, "lng" => -71.0589],
    "Washington" => ["lat" => 38.9072, "lng" => -77.0369],
    "Mexico" => ["lat" => 19.4326, "lng" => -99.1332],
    "Cancún" => ["lat" => 21.1619, "lng" => -86.8515],
    "Rio de Janeiro" => ["lat" => -22.9068, "lng" => -43.1729],
    "São Paulo" => ["lat" => -23.5505, "lng" => -46.6333],
    "Buenos Aires" => ["lat" => -34.6037, "lng" => -58.3816],
    "Lima" => ["lat" => -12.0464, "lng" => -77.0428],
    "Cusco" => ["lat" => -13.5319, "lng" => -71.9675],
    "Santiago" => ["lat" => -33.4489, "lng" => -70.6693],
    "Bogota" => ["lat" => 4.7110, "lng" => -74.0721],
    "Quito" => ["lat" => -0.1807, "lng" => -78.4678],
    "La Havane" => ["lat" => 23.1136, "lng" => -82.3666],
    "Punta Cana" => ["lat" => 18.5601, "lng" => -68.3725],
    "Bangkok" => ["lat" => 13.7563, "lng" => 100.5018],
    "Phuket" => ["lat" => 7.8804, "lng" => 98.3923],
    "Singapour" => ["lat" => 1.3521, "lng" => 103.8198],
    "Kuala Lumpur" => ["lat" => 3.1390, "lng" => 101.6869],
    "Hanoï" => ["lat" => 21.0278, "lng" => 105.8342],
    "Ho Chi Minh Ville" => ["lat" => 10.8231, "lng" => 106.6297],
    "Hong Kong" => ["lat" => 22.3193, "lng" => 114.1694],
    "Séoul" => ["lat" => 37.5665, "lng" => 126.9780],
    "Pékin" => ["lat" => 39.9042, "lng" => 116.4074],
    "Shanghai" => ["lat" => 31.2304, "lng" => 121.4737],
    "Taipei" => ["lat" => 25.0330, "lng" => 121.5654],
    "Osaka" => ["lat" => 34.6937, "lng" => 135.5023],
    "Sydney" => ["lat" => -33.8688, "lng" => 151.2093],
    "Melbourne" => ["lat" => -37.8136, "lng" => 144.9631],
    "Auckland" => ["lat" => -36.8509, "lng" => 174.7645],
    "Queenstown" => ["lat" => -45.0312, "lng" => 168.6626],
    "Nouméa" => ["lat" => -22.2758, "lng" => 166.4580],
    "Papeete" => ["lat" => -17.5516, "lng" => -149.5585],
    "Maldives" => ["lat" => 3.2028, "lng" => 73.2207],
    "Mahé" => ["lat" => -4.6827, "lng" => 55.4804],
    "Colombo" => ["lat" => 6.9271, "lng" => 79.8612]
];

$cities = array_keys($cityCoordinates);

$cityRoutes = [
    ["from" => "Paris", "to" => "Bali", "destination_id" => 1, "transport_id" => 1],
    ["from" => "Paris", "to" => "Lisbonne", "destination_id" => 2, "transport_id" => 2],
    ["from" => "Bordeaux", "to" => "Lisbonne", "destination_id" => 2, "transport_id" => 3],
    ["from" => "Paris", "to" => "Chamonix", "destination_id" => 3, "transport_id" => 4],
    ["from" => "Paris", "to" => "Marrakech", "destination_id" => 4, "transport_id" => 5],
    ["from" => "Athènes", "to" => "Santorin", "destination_id" => 5, "transport_id" => 6],
    ["from" => "Paris", "to" => "Santorin", "destination_id" => 5, "transport_id" => 7],
    ["from" => "Paris", "to" => "Kyoto", "destination_id" => 6, "transport_id" => 8],
    ["from" => "Genève", "to" => "Chamonix", "destination_id" => 3, "transport_id" => 9]
];

$destinations = [
    [
        "id" => 1, "name" => "Bali", "country" => "Indonésie", "category" => "Plage",
        "stay_type" => "Relaxation", "duration" => "10 jours", "duration_days" => 10,
        "price" => 890, "old_price" => 1120, "discount" => "-21%", "pack" => "Évasion plage",
        "rating" => 4.8, "reviews" => 312, "recommendation" => 98,
        "image" => "https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80",
        "description" => "Rizières, plages, temples et villages tropicaux pour un séjour entre repos et découverte."
    ],
    [
        "id" => 2, "name" => "Lisbonne", "country" => "Portugal", "category" => "Ville",
        "stay_type" => "City break", "duration" => "4 jours", "duration_days" => 4,
        "price" => 390, "old_price" => 470, "discount" => "-17%", "pack" => "City break",
        "rating" => 4.6, "reviews" => 204, "recommendation" => 86,
        "image" => "https://images.unsplash.com/photo-1548707309-dcebeab9ea9b?auto=format&fit=crop&w=1200&q=80",
        "description" => "Quartiers historiques, tramways, belvédères et escapade urbaine au bord de l'Atlantique."
    ],
    [
        "id" => 3, "name" => "Chamonix", "country" => "France", "category" => "Montagne",
        "stay_type" => "Aventure", "duration" => "6 jours", "duration_days" => 6,
        "price" => 620, "old_price" => 760, "discount" => "-18%", "pack" => "Nature active",
        "rating" => 4.7, "reviews" => 184, "recommendation" => 91,
        "image" => "https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80",
        "description" => "Panoramas alpins, randonnées, train de montagne et hébergements proches des pistes."
    ],
    [
        "id" => 4, "name" => "Marrakech", "country" => "Maroc", "category" => "Culture",
        "stay_type" => "Culture", "duration" => "5 jours", "duration_days" => 5,
        "price" => 480, "old_price" => 590, "discount" => "-19%", "pack" => "Culture et soleil",
        "rating" => 4.5, "reviews" => 228, "recommendation" => 84,
        "image" => "https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1200&q=80",
        "description" => "Souks, palais, jardins, riads et excursion dans le désert d'Agafay."
    ],
    [
        "id" => 5, "name" => "Santorin", "country" => "Grèce", "category" => "Plage",
        "stay_type" => "Romantique", "duration" => "7 jours", "duration_days" => 7,
        "price" => 760, "old_price" => 940, "discount" => "-19%", "pack" => "Mer romantique",
        "rating" => 4.7, "reviews" => 396, "recommendation" => 94,
        "image" => "https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=1200&q=80",
        "description" => "Villages blancs, mer Égée, ferry et couchers de soleil pour un voyage lumineux."
    ],
    [
        "id" => 6, "name" => "Kyoto", "country" => "Japon", "category" => "Culture",
        "stay_type" => "Culture", "duration" => "8 jours", "duration_days" => 8,
        "price" => 1180, "old_price" => 1390, "discount" => "-15%", "pack" => "Culture premium",
        "rating" => 4.9, "reviews" => 538, "recommendation" => 96,
        "image" => "https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=80",
        "description" => "Temples, jardins, ruelles traditionnelles et immersion dans la culture japonaise."
    ]
];

$transports = [
    ["id" => 1, "destination_id" => 1, "mode" => "Avion", "company" => "Air Horizon", "from" => "Paris CDG", "to" => "Denpasar", "duration" => "17h45", "duration_minutes" => 1065, "stops" => 0, "depart_hour" => 11, "return_hour" => 5, "price" => 640],
    ["id" => 2, "destination_id" => 2, "mode" => "Avion", "company" => "Lusitania Air", "from" => "Paris Orly", "to" => "Lisbonne", "duration" => "2h30", "duration_minutes" => 150, "stops" => 0, "depart_hour" => 8, "return_hour" => 18, "price" => 150],
    ["id" => 3, "destination_id" => 2, "mode" => "Bus", "company" => "EuroRoute", "from" => "Bordeaux", "to" => "Lisbonne", "duration" => "15h20", "duration_minutes" => 920, "stops" => 1, "depart_hour" => 22, "return_hour" => 12, "price" => 58],
    ["id" => 4, "destination_id" => 3, "mode" => "Train", "company" => "SNCF Connect", "from" => "Paris Gare de Lyon", "to" => "Chamonix", "duration" => "6h10", "duration_minutes" => 370, "stops" => 1, "depart_hour" => 7, "return_hour" => 16, "price" => 94],
    ["id" => 5, "destination_id" => 4, "mode" => "Avion", "company" => "Atlas Wings", "from" => "Paris Orly", "to" => "Marrakech", "duration" => "3h20", "duration_minutes" => 200, "stops" => 0, "depart_hour" => 9, "return_hour" => 20, "price" => 130],
    ["id" => 6, "destination_id" => 5, "mode" => "Ferry", "company" => "Blue Sea Ferries", "from" => "Athènes Pirée", "to" => "Santorin", "duration" => "7h30", "duration_minutes" => 450, "stops" => 0, "depart_hour" => 7, "return_hour" => 15, "price" => 48],
    ["id" => 7, "destination_id" => 5, "mode" => "Avion", "company" => "Aegean Sky", "from" => "Paris CDG", "to" => "Santorin", "duration" => "3h25", "duration_minutes" => 205, "stops" => 0, "depart_hour" => 14, "return_hour" => 19, "price" => 260],
    ["id" => 8, "destination_id" => 6, "mode" => "Avion", "company" => "Japan Lines", "from" => "Paris CDG", "to" => "Osaka Kansai", "duration" => "13h15", "duration_minutes" => 795, "stops" => 0, "depart_hour" => 18, "return_hour" => 16, "price" => 820],
    ["id" => 9, "destination_id" => 3, "mode" => "Voiture", "company" => "Alpine Drive", "from" => "Genève", "to" => "Chamonix", "duration" => "2h10", "duration_minutes" => 130, "stops" => 0, "depart_hour" => 10, "return_hour" => 17, "price" => 82],
    ["id" => 10, "destination_id" => 4, "mode" => "Voiture", "company" => "Atlas Car", "from" => "Aéroport Marrakech", "to" => "Médina", "duration" => "35 min", "duration_minutes" => 35, "stops" => 0, "depart_hour" => 12, "return_hour" => 18, "price" => 44],
    ["id" => 11, "destination_id" => 1, "mode" => "Avion", "company" => "Qatar Airways", "from" => "Paris CDG", "to" => "Denpasar", "duration" => "18h30", "duration_minutes" => 1110, "stops" => 1, "depart_hour" => 15, "return_hour" => 6, "price" => 690]
];

$hotels = [
    ["id" => 1, "destination_id" => 1, "name" => "Ubud Garden Hotel", "type" => "Hôtel", "price" => 92, "rating" => 4.6, "stars" => 4, "rooms" => 2, "airport_distance" => 38, "amenities" => ["wifi", "piscine", "petit_dejeuner", "clim"], "image" => "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 2, "destination_id" => 2, "name" => "Alfama Blue House", "type" => "Maison d'hôtes", "price" => 74, "rating" => 4.4, "stars" => 3, "rooms" => 1, "airport_distance" => 9, "amenities" => ["wifi", "petit_dejeuner", "clim"], "image" => "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 3, "destination_id" => 3, "name" => "Mont Blanc Lodge", "type" => "Lodge", "price" => 128, "rating" => 4.7, "stars" => 4, "rooms" => 2, "airport_distance" => 88, "amenities" => ["wifi", "piscine", "parking"], "image" => "https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 4, "destination_id" => 4, "name" => "Riad Atlas Medina", "type" => "Riad", "price" => 66, "rating" => 4.5, "stars" => 4, "rooms" => 1, "airport_distance" => 6, "amenities" => ["wifi", "petit_dejeuner", "clim"], "image" => "https://images.unsplash.com/photo-1602002418679-43121356bf41?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 5, "destination_id" => 5, "name" => "Aegean View Hotel", "type" => "Hôtel", "price" => 154, "rating" => 4.8, "stars" => 5, "rooms" => 2, "airport_distance" => 12, "amenities" => ["wifi", "piscine", "petit_dejeuner", "clim"], "image" => "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 6, "destination_id" => 6, "name" => "Kyoto Maple Inn", "type" => "Maison d'hôtes", "price" => 118, "rating" => 4.7, "stars" => 4, "rooms" => 1, "airport_distance" => 44, "amenities" => ["wifi", "petit_dejeuner", "clim"], "image" => "https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 7, "destination_id" => 1, "name" => "Bali Beach Resort", "type" => "Hôtel", "price" => 135, "rating" => 4.8, "stars" => 5, "rooms" => 3, "airport_distance" => 18, "amenities" => ["wifi", "piscine", "petit_dejeuner", "clim", "spa"], "image" => "https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?auto=format&fit=crop&w=1200&q=80"]
];

$hotelDetails = [
    1 => [
        "description" => "Adresse calme au milieu des jardins d'Ubud, avec chambres lumineuses, navette locale et espaces communs ouverts sur la végétation.",
        "official_url" => "https://www.ubudgardenhotel.com",
        "photos" => [
            "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Nora", "rating" => 5, "text" => "Très bon accueil, piscine propre et accès rapide aux rizières."],
            ["author" => "Yanis", "rating" => 4, "text" => "Chambre confortable, petit-déjeuner varié et Wi-Fi stable."],
            ["author" => "Léa", "rating" => 5, "text" => "Bon équilibre entre calme, prix et proximité du centre d'Ubud."]
        ]
    ],
    2 => [
        "description" => "Maison d'hôtes dans l'Alfama, idéale pour un séjour à pied entre belvédères, restaurants et tramways historiques.",
        "official_url" => "https://www.alfamabluehouse.com",
        "photos" => [
            "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1568495248636-6432b97bd949?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Sofia", "rating" => 4, "text" => "Quartier parfait pour découvrir Lisbonne sans voiture."],
            ["author" => "Maxime", "rating" => 4, "text" => "Accueil simple, chambre propre et climatisation appréciable."],
            ["author" => "Inès", "rating" => 5, "text" => "Très bon rapport qualité-prix pour un city break."]
        ]
    ],
    3 => [
        "description" => "Lodge alpin avec salons boisés, vue montagne, parking et accès pratique aux sentiers autour de Chamonix.",
        "official_url" => "https://www.montblanclodge.com",
        "photos" => [
            "https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1518733057094-95b53143d2a7?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Arthur", "rating" => 5, "text" => "Très pratique pour rayonner dans la vallée."],
            ["author" => "Emma", "rating" => 4, "text" => "Ambiance chaleureuse et parking facile."],
            ["author" => "Noé", "rating" => 5, "text" => "Vue superbe le matin depuis la terrasse."]
        ]
    ],
    4 => [
        "description" => "Riad central près de la médina, patio intérieur, chambres climatisées et petit-déjeuner marocain inclus.",
        "official_url" => "https://www.riadatlasmedina.com",
        "photos" => [
            "https://images.unsplash.com/photo-1602002418679-43121356bf41?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1561501900-3701fa6a0864?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Imane", "rating" => 5, "text" => "Patio très agréable après les visites."],
            ["author" => "Paul", "rating" => 4, "text" => "Personnel disponible et emplacement pratique."],
            ["author" => "Sarah", "rating" => 4, "text" => "Bonne adresse pour découvrir la médina."]
        ]
    ],
    5 => [
        "description" => "Hôtel avec vue mer, piscine et chambres lumineuses pour profiter de Santorin sans multiplier les trajets.",
        "official_url" => "https://www.aegeanviewhotel.com",
        "photos" => [
            "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Clara", "rating" => 5, "text" => "Vue superbe et service très régulier."],
            ["author" => "Hugo", "rating" => 5, "text" => "Piscine calme, chambre impeccable."],
            ["author" => "Mila", "rating" => 4, "text" => "Très bien situé pour les couchers de soleil."]
        ]
    ],
    6 => [
        "description" => "Maison d'hôtes japonaise, chambres sobres, accès rapide aux temples et petit-déjeuner local.",
        "official_url" => "https://www.kyotomapleinn.com",
        "photos" => [
            "https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1596178065887-1198b6148b2b?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Aya", "rating" => 5, "text" => "Très calme et facile d'accès en transport."],
            ["author" => "Lucas", "rating" => 4, "text" => "Chambre compacte mais très bien pensée."],
            ["author" => "Mina", "rating" => 5, "text" => "Accueil attentionné et quartier agréable."]
        ]
    ],
    7 => [
        "description" => "Resort balinais près de la plage, spa, piscine, grandes chambres et restauration sur place.",
        "official_url" => "https://www.balibeachresort.com",
        "photos" => [
            "https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80",
            "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80"
        ],
        "recent_reviews" => [
            ["author" => "Jade", "rating" => 5, "text" => "Très belle piscine et accès plage rapide."],
            ["author" => "Ilyes", "rating" => 5, "text" => "Chambres spacieuses, spa agréable."],
            ["author" => "Manon", "rating" => 4, "text" => "Bon choix pour un séjour reposant."]
        ]
    ]
];

foreach ($hotels as &$hotel) {
    $details = $hotelDetails[(int) $hotel["id"]] ?? [];
    $hotel["description"] = $details["description"] ?? "Hébergement confortable avec équipements essentiels pour le séjour.";
    $hotel["official_url"] = $details["official_url"] ?? "#";
    $hotel["photos"] = $details["photos"] ?? [$hotel["image"], $hotel["image"], $hotel["image"]];
    $hotel["recent_reviews"] = $details["recent_reviews"] ?? [];
}
unset($hotel);

$activities = [
    ["id" => 1, "destination_id" => 1, "name" => "Balade dans les rizières", "category" => "Nature", "duration" => "4h", "price" => 38, "rating" => 4.8, "reviews" => 256, "period" => "Matin", "included" => ["Guide local", "Eau minérale"], "image" => "https://images.unsplash.com/photo-1539367628448-4bc5c9d171c8?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 2, "destination_id" => 2, "name" => "Tour des miradouros", "category" => "Culture", "duration" => "3h", "price" => 22, "rating" => 4.6, "reviews" => 118, "period" => "Après-midi", "included" => ["Guide", "Quartiers historiques"], "image" => "https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 3, "destination_id" => 3, "name" => "Randonnée balcon du Mont Blanc", "category" => "Aventure", "duration" => "6h", "price" => 55, "rating" => 4.7, "reviews" => 142, "period" => "Matin", "included" => ["Guide montagne", "Petit groupe"], "image" => "https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 4, "destination_id" => 4, "name" => "Excursion désert Agafay", "category" => "Aventure", "duration" => "6h", "price" => 59, "rating" => 4.6, "reviews" => 156, "period" => "Soirée", "included" => ["Transfert", "Dîner"], "image" => "https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 5, "destination_id" => 5, "name" => "Croisière au coucher du soleil", "category" => "Détente", "duration" => "4h", "price" => 86, "rating" => 4.9, "reviews" => 240, "period" => "Soirée", "included" => ["Boisson", "Transfert"], "image" => "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 6, "destination_id" => 6, "name" => "Visite des temples de l'est", "category" => "Culture", "duration" => "5h", "price" => 48, "rating" => 4.8, "reviews" => 210, "period" => "Matin", "included" => ["Guide", "Entrées"], "image" => "https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 7, "destination_id" => 1, "name" => "Snorkeling à Blue Lagoon", "category" => "Nature", "duration" => "3h", "price" => 52, "rating" => 4.7, "reviews" => 189, "period" => "Après-midi", "included" => ["Équipement", "Guide"], "image" => "https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80"],
    ["id" => 8, "destination_id" => 1, "name" => "Temple de Tanah Lot", "category" => "Culture", "duration" => "4h", "price" => 36, "rating" => 4.9, "reviews" => 312, "period" => "Soirée", "included" => ["Guide", "Entrée"], "image" => "https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=1200&q=80"]
];

$notifications = [
    ["category" => "Réservations", "title" => "Réservation confirmée", "message" => "Votre réservation à Ubud Garden Hotel est confirmée.", "date" => "10:30", "icon" => "📅", "color" => "teal"],
    ["category" => "Trajets", "title" => "Départ dans 2 jours", "message" => "Votre vol Paris CDG → Denpasar décolle bientôt.", "date" => "09:15", "icon" => "✈️", "color" => "blue"],
    ["category" => "Hébergements", "title" => "Modification d'hébergement", "message" => "Les dates de votre séjour ont été mises à jour.", "date" => "Hier", "icon" => "🛏️", "color" => "teal"],
    ["category" => "Activités", "title" => "Activité à venir", "message" => "Votre balade dans les rizières est prévue le 17 juin.", "date" => "Hier", "icon" => "🎟️", "color" => "purple"],
    ["category" => "Promotions", "title" => "Offre spéciale pour vous", "message" => "Profitez de 10% de réduction sur certaines activités.", "date" => "12 juin", "icon" => "🏷️", "color" => "orange"],
    ["category" => "Système", "title" => "Mise à jour des conditions", "message" => "Consultez les changements récents.", "date" => "10 juin", "icon" => "ℹ️", "color" => "slate"]
];

$reviews = [
    ["name" => "Camille", "destination" => "Bali", "rating" => 5, "text" => "Le choix par étape rend la préparation très simple."],
    ["name" => "Nassim", "destination" => "Marrakech", "rating" => 4, "text" => "Les filtres budget et activités aident à composer un séjour rapide."],
    ["name" => "Lina", "destination" => "Santorin", "rating" => 5, "text" => "Le pack mer avec ferry et hôtel vue mer est très clair."]
];

$packs = [
    ["id" => 1, "destination_id" => 1, "name" => "Pack plage essentiel", "label" => "Vol + hôtel + activité", "discount" => "-21%"],
    ["id" => 2, "destination_id" => 4, "name" => "Pack culture soleil", "label" => "Vol + riad + excursion", "discount" => "-19%"],
    ["id" => 3, "destination_id" => 5, "name" => "Pack mer Égée", "label" => "Ferry + hôtel + croisière", "discount" => "-19%"]
];

function money($value)
{
    return number_format((float) $value, 0, ',', ' ') . " €";
}

function findById($items, $id)
{
    foreach ($items as $item) {
        if ((int) $item["id"] === (int) $id) {
            return $item;
        }
    }

    return $items[0] ?? null;
}

function byDestination($items, $destinationId)
{
    return array_values(array_filter($items, function ($item) use ($destinationId) {
        return (int) $item["destination_id"] === (int) $destinationId;
    }));
}

function uniqueValues($items, $key)
{
    $values = [];

    foreach ($items as $item) {
        if (isset($item[$key]) && !in_array($item[$key], $values, true)) {
            $values[] = $item[$key];
        }
    }

    sort($values);
    return $values;
}

function minItem($items, $key = "price")
{
    if (empty($items)) {
        return null;
    }

    $lowest = $items[0];

    foreach ($items as $item) {
        if (($item[$key] ?? PHP_INT_MAX) < ($lowest[$key] ?? PHP_INT_MAX)) {
            $lowest = $item;
        }
    }

    return $lowest;
}

function cityPoint($city)
{
    global $cityCoordinates;

    $city = trim((string) $city);
    $point = $cityCoordinates[$city] ?? null;

    if (!$point) {
        foreach ($cityCoordinates as $name => $candidate) {
            if (stripos($city, $name) !== false || stripos($name, $city) !== false) {
                $point = $candidate;
                break;
            }
        }
    }

    $point = $point ?? $cityCoordinates["Paris"];

    if (isset($point["lat"], $point["lng"])) {
        $point["x"] = round((($point["lng"] + 180) / 360) * 100, 2);
        $point["y"] = round(((90 - $point["lat"]) / 180) * 100, 2);
    }

    return $point;
}

function routeDestinationName($transport)
{
    global $destinations;
    $destination = findById($destinations, $transport["destination_id"]);
    return $destination["name"] ?? $transport["to"];
}

function vvAdultChildCounts()
{
    $adults = max(1, (int) ($_GET["adults"] ?? ($_GET["persons"] ?? 2)));
    $children = max(0, (int) ($_GET["children"] ?? 0));
    return [$adults, $children, max(1, $adults + $children)];
}

function transportOptionLabels($bags = "", $ticket = "", $seat = "")
{
    $bagLabels = [
        "" => "Aucune option bagage",
        "0" => "Aucun bagage",
        "cabine" => "Cabine incluse",
        "soute" => "1 bagage soute"
    ];

    return array_values(array_filter([
        $bags !== "" ? ($bagLabels[$bags] ?? $bags) : "",
        $ticket !== "" ? "Billet " . $ticket : "",
        $seat !== "" ? "Siège " . $seat : ""
    ]));
}

function calculateTransportFare($transport, $adults = 1, $children = 0, $bags = "0", $ticket = "Basic", $seat = "Standard")
{
    $base = (float) ($transport["price"] ?? 0);
    $adults = max(1, (int) $adults);
    $children = max(0, (int) $children);

    $childBase = round($base * 0.70);
    $bagFee = match ($bags) {
        "cabine" => 0,
        "soute" => 40,
        default => 0,
    };
    $ticketAdultFee = match ($ticket) {
        "Flex" => 35,
        "Premium" => 80,
        default => 0,
    };
    $ticketChildFee = match ($ticket) {
        "Flex" => 20,
        "Premium" => 50,
        default => 0,
    };
    $seatFee = match ($seat) {
        "Fenêtre" => 12,
        "Couloir" => 10,
        "Espace+" => 25,
        default => 0,
    };

    $adultUnit = $base + $bagFee + $ticketAdultFee + $seatFee;
    $childUnit = $childBase + $bagFee + $ticketChildFee + $seatFee;

    return [
        "adult_base" => $base,
        "child_base" => $childBase,
        "adult_unit" => $adultUnit,
        "child_unit" => $childUnit,
        "adults_total" => $adultUnit * $adults,
        "children_total" => $childUnit * $children,
        "options_per_adult" => $bagFee + $ticketAdultFee + $seatFee,
        "options_per_child" => $bagFee + $ticketChildFee + $seatFee,
        "total" => ($adultUnit * $adults) + ($childUnit * $children),
    ];
}

function hotelOptionLabels($options)
{
    if (!is_array($options)) {
        $options = $options !== "" ? [$options] : [];
    }

    $labels = [
        "breakfast" => "Petit-déjeuner",
        "cancel" => "Annulation flexible",
        "parking" => "Parking",
        "spa" => "Accès spa"
    ];

    return array_map(fn($option) => $labels[$option] ?? $option, $options);
}

function calculateHotelFare($hotel, $nights = 1, $adults = 1, $children = 0, $roomType = "standard", $options = [])
{
    if (!is_array($options)) {
        $options = $options !== "" ? [$options] : [];
    }

    $base = (float) ($hotel["price"] ?? 0);
    $nights = max(1, (int) $nights);
    $adults = max(1, (int) $adults);
    $children = max(0, (int) $children);

    $roomExtra = match ($roomType) {
        "family" => 35,
        "view" => 55,
        "suite" => 90,
        default => 0,
    };

    $optionNight = 0;
    $optionStay = 0;

    foreach ($options as $option) {
        if ($option === "breakfast") {
            $optionNight += ($adults * 12) + ($children * 7);
        } elseif ($option === "cancel") {
            $optionNight += 8;
        } elseif ($option === "parking") {
            $optionNight += 10;
        } elseif ($option === "spa") {
            $optionStay += $adults * 25;
        }
    }

    $nightUnit = $base + $roomExtra + $optionNight;
    $total = ($nightUnit * $nights) + $optionStay;

    return [
        "base" => $base,
        "room_extra" => $roomExtra,
        "option_night" => $optionNight,
        "option_stay" => $optionStay,
        "night_unit" => $nightUnit,
        "total" => $total,
    ];
}

function calculateActivityFare($activity, $adults = 1, $children = 0)
{
    $base = (float) ($activity["price"] ?? 0);
    $adults = max(1, (int) $adults);
    $children = max(0, (int) $children);
    $childBase = round($base * 0.70);

    return [
        "adult_unit" => $base,
        "child_unit" => $childBase,
        "adults_total" => $base * $adults,
        "children_total" => $childBase * $children,
        "total" => ($base * $adults) + ($childBase * $children),
    ];
}

function roomTypeLabel($roomType)
{
    return [
        "standard" => "Chambre standard",
        "family" => "Chambre familiale",
        "view" => "Chambre avec vue",
        "suite" => "Suite"
    ][$roomType] ?? "Chambre standard";
}


// Données de popularité complémentaires pour homogénéiser les pages catalogue.
// Les transports et hôtels n'avaient pas tous un compteur d'avis dans les données de départ :
// on ajoute une valeur cohérente pour pouvoir trier et afficher les éléments les plus consultés.
foreach ($hotels as &$hotel) {
    if (!isset($hotel["reviews"])) {
        $hotel["reviews"] = max(35, (count($hotel["recent_reviews"] ?? []) * 52) + (int) round(($hotel["rating"] ?? 4) * 25) + ((int) $hotel["stars"] * 8) + ((int) $hotel["id"] * 9));
    }
}
unset($hotel);

foreach ($transports as &$transport) {
    if (!isset($transport["reviews"])) {
        $destination = findById($destinations, $transport["destination_id"]);
        $destinationReviews = (int) ($destination["reviews"] ?? 120);
        $directBonus = ((int) ($transport["stops"] ?? 0) === 0) ? 38 : 16;
        $transport["reviews"] = max(28, (int) round($destinationReviews * 0.42) + $directBonus + ((int) $transport["id"] * 3));
    }
    if (!isset($transport["rating"])) {
        $transport["rating"] = min(4.9, 4.2 + (((int) $transport["reviews"] % 7) / 10));
    }
}
unset($transport);

?>
