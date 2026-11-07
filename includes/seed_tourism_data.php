<?php
/**
 * Self-Contained 2026 UAE Tourism Database Seeder & MySQL Exporter
 * Populates the exact 5 categories and 35 activities from UAE_Tourism_Website_–_Sketch_&_Roadmap.pdf
 * on both SQLite (localhost) and MySQL (cPanel) automatically on first boot.
 */
declare(strict_types=1);

function seedTourismRedesignData(PDO $db, bool $isSqlite): void {
    $pools = [
        'desert'  => array_map(fn($i) => "/uploads/activities/gallery-desert-$i.webp", range(1, 8)),
        'city'    => array_map(fn($i) => "/uploads/activities/gallery-city-$i.webp", range(1, 8)),
        'cruise'  => array_map(fn($i) => "/uploads/activities/gallery-cruise-$i.webp", range(1, 7)),
        'water'   => array_map(fn($i) => "/uploads/activities/gallery-water-$i.webp", range(1, 8)),
        'tickets' => array_map(fn($i) => "/uploads/activities/gallery-tickets-$i.webp", range(1, 9)),
    ];

    $categories = [
        [
            'id' => 1,
            'name' => 'Desert Safari',
            'section_title' => 'Desert Safari Adventures',
            'slug' => 'desert-safari',
            'description' => 'Ride the golden dunes of Dubai and Abu Dhabi, then relax under the stars with a barbecue dinner and live shows. Pick up from your hotel is included on most tours.',
            'cta_text' => 'Not sure which safari suits you? Chat with us on WhatsApp and we will help you choose.',
            'seo_keywords' => 'desert safari Dubai, evening desert safari, overnight desert safari, quad bike Dubai, VIP desert safari',
            'icon' => 'sun',
            'sort_order' => 1,
            'seo_title' => 'Desert Safari Dubai & Abu Dhabi | Morning, Evening & VIP Tours',
            'seo_desc' => 'Book Dubai & Abu Dhabi desert safari tours with 4x4 hotel pick-up, dune bashing, camel rides, quad bikes, and live BBQ dinner shows. Instant WhatsApp confirmation.',
        ],
        [
            'id' => 2,
            'name' => 'City Tours',
            'section_title' => 'UAE City Tours',
            'slug' => 'city-tours',
            'description' => 'See the best of Dubai, Abu Dhabi and Sharjah with a local guide, from modern skylines to old souks and grand mosques.',
            'cta_text' => 'Want a custom route? Tell us your hotel and dates on WhatsApp.',
            'seo_keywords' => 'Dubai city tour, Abu Dhabi tour from Dubai, Sheikh Zayed Mosque tour, private tour Dubai',
            'icon' => 'landmark',
            'sort_order' => 2,
            'seo_title' => 'UAE City Tours | Dubai, Abu Dhabi & Sharjah Sightseeing',
            'seo_desc' => 'Explore Dubai, Abu Dhabi, and Sharjah with licensed local guides and hotel pick-up. Half-day, full-day, Sheikh Zayed Mosque, and private chauffeur tours.',
        ],
        [
            'id' => 3,
            'name' => 'Cruise Dinner',
            'section_title' => 'Cruise Dinner Experiences',
            'slug' => 'cruise-dinner',
            'description' => 'Dine on the water with views of the Dubai skyline lit up at night. Choose a traditional dhow or a modern glass-top boat.',
            'cta_text' => 'Planning a birthday or proposal? Message us on WhatsApp to add decorations and a cake.',
            'seo_keywords' => 'dhow cruise dinner Dubai, Marina dinner cruise, Creek dinner cruise, yacht dinner Dubai',
            'icon' => 'ship',
            'sort_order' => 3,
            'seo_title' => 'Dhow Cruise & Yacht Dinner Dubai | Marina & Creek Cruises',
            'seo_desc' => 'Enjoy evening dhow cruise dinners in Dubai Marina and Dubai Creek or luxury yacht dining with buffet menus, live entertainment, and skyline views.',
        ],
        [
            'id' => 4,
            'name' => 'Watersports',
            'section_title' => 'Watersports and Sea Adventures',
            'slug' => 'watersports',
            'description' => 'Jet ski, flyboard, parasailing and more along the Dubai coastline. Trained instructors and life jackets are provided for every activity.',
            'cta_text' => 'Booking for a group? Ask us on WhatsApp for combo prices.',
            'seo_keywords' => 'jet ski Dubai, parasailing Dubai, flyboard Dubai, Dubai watersports packages',
            'icon' => 'waves',
            'sort_order' => 4,
            'seo_title' => 'Dubai Watersports | Jet Ski, Parasailing, Flyboarding & Fishing',
            'seo_desc' => 'Book thrilling Dubai watersports along Palm Jumeirah and Burj Al Arab: jet ski rides, parasailing, flyboarding, banana boat, scuba diving, and deep sea fishing.',
        ],
        [
            'id' => 5,
            'name' => 'Tickets & Attractions',
            'section_title' => 'Tickets and Attractions',
            'slug' => 'tickets-attractions',
            'description' => "Skip the queue and book entry tickets for the UAE's top attractions, parks and experiences, with instant confirmation on WhatsApp.",
            'cta_text' => 'Want combo tickets or a transfer included? Ask us on WhatsApp.',
            'seo_keywords' => 'Burj Khalifa tickets, Dubai attraction tickets, Ferrari World tickets, Aquaventure tickets',
            'icon' => 'ticket',
            'sort_order' => 5,
            'seo_title' => 'UAE Attraction Tickets | Burj Khalifa, Museum of the Future & Theme Parks',
            'seo_desc' => 'Skip the queue with official e-tickets for Burj Khalifa, Dubai Frame, Museum of the Future, Aquaventure, Ferrari World, Warner Bros. World, and Louvre Abu Dhabi.',
        ],
    ];

    $rawActivities = [
        // === CATEGORY 1: DESERT SAFARI (6) ===
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 1, 'featured' => 1,
            'title' => 'Morning Desert Safari',
            'slug' => 'morning-desert-safari',
            'short_desc' => 'Dune bashing, camel ride and sandboarding in cooler morning light',
            'duration' => '4 hours',
            'pickup_line' => '4 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 160, 'child_price_aed' => 130,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs (Under 3 joins private car only)',
            'inclusions' => ['4x4 Land Cruiser hotel pick-up and drop-off', '35–45 mins red dune bashing with licensed desert driver', 'Short camel ride & desert sandboarding', 'Bottled water, soft drinks & Arabic coffee'],
            'exclusions' => ['Quad bike or dune buggy (available as add-on)', 'Professional souvenir photography', 'Gratuities / tips'],
            'details' => [
                'overview' => 'Beat the midday heat on our Morning Desert Safari across the Lahbab red dunes. After 4x4 pick-up from your hotel, your licensed safari marshal takes you across sweeping golden ridges followed by sandboarding and a traditional camel ride before returning you to the city before lunch.',
                'timings_pickup' => 'Daily pick-up between 07:30 AM – 08:00 AM; return by 11:30 AM – 12:00 PM. Pick-up available across Dubai, Sharjah, and Abu Dhabi hotels (Abu Dhabi private supplement applies).',
                'important_notes' => 'Not recommended for pregnant guests, infants under 3 years, or guests with serious back, neck, or heart conditions. Wear light, breathable clothing and sunglasses.',
                'cancellation' => 'Free cancellation or date change up to 24 hours before pick-up time.',
                'reviews' => [
                    ['author' => 'Hannah W. (UK)', 'rating' => 5, 'comment' => 'Cool morning air, zero crowds on the red dunes, and our driver Tariq took brilliant family photos.'],
                    ['author' => 'Marco R. (Italy)', 'rating' => 5, 'comment' => 'Picked up right on time from Dubai Marina. Sandboarding down the high dunes was unforgettable.'],
                ],
            ],
        ],
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 2, 'featured' => 1,
            'title' => 'Evening Desert Safari with BBQ Dinner',
            'slug' => 'evening-desert-safari-with-bbq-dinner',
            'short_desc' => 'Dune bashing, sunset photo stop, camel ride, henna, live shows and dinner',
            'duration' => '6 hours',
            'pickup_line' => '6 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 150, 'child_price_aed' => 120,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Infants under 3 free (private car required for dune drive)',
            'inclusions' => ['4x4 hotel pick-up and drop-off', 'Red dune bashing & desert sunset photo stop', 'Camel ride, sandboarding & henna painting', 'Open-buffet BBQ dinner (veg & non-veg) with unlimited water, tea & soft drinks', 'Live Tanoura dance, Khaliji dance & fire show'],
            'exclusions' => ['Quad bike ride (optional camp add-on)', 'Falcon souvenir photos & VIP table waiter service', 'Personal tips'],
            'details' => [
                'overview' => 'Our signature UAE desert experience combines thrilling 4x4 dune bashing over golden dunes, a panoramic sunset stop, and an atmospheric Bedouin camp evening featuring henna art, camel rides, live cultural performances, and a generous international & Arabic BBQ buffet under the stars.',
                'timings_pickup' => 'Daily pick-up between 02:30 PM – 03:15 PM; drop-off around 09:00 PM – 09:30 PM. Door-to-door 4x4 pick-up from Dubai, Sharjah, and Ajman hotels; Abu Dhabi pick-up available on request.',
                'important_notes' => 'Dune bashing is not recommended for pregnant guests, infants under 3, or people with back or heart problems (direct camp transfer can be arranged on request).',
                'cancellation' => 'Free cancellation up to 24 hours prior to departure with full refund.',
                'reviews' => [
                    ['author' => 'Priya S. (India)', 'rating' => 5, 'comment' => 'Best evening in Dubai! Great veg and grilled BBQ options and the fire show was spectacular.'],
                    ['author' => 'Daniel K. (Germany)', 'rating' => 5, 'comment' => 'Superb organization from WhatsApp booking to hotel drop-off. Sunset on the dunes was magical.'],
                ],
            ],
        ],
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 3, 'featured' => 1,
            'title' => 'Overnight Desert Safari',
            'slug' => 'overnight-desert-safari',
            'short_desc' => 'Evening safari, overnight desert camp stay under the stars & sunrise breakfast',
            'duration' => '18 hours',
            'pickup_line' => '18 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 380, 'child_price_aed' => 310,
            'age_policy' => 'Adults 12+ yrs | Children 5–11 yrs (Minimum age 5 yrs)',
            'inclusions' => [
                '4x4 hotel pick-up (~03:00 PM) & next-morning drop-off (~08:30 AM)',
                'Full evening desert safari: red dune bashing, sunset photo stop & camel ride',
                'BBQ buffet dinner with grilled meats, salads, desserts & unlimited soft drinks',
                'Live evening entertainment: Tanoura dance, belly dance show & fire performance',
                'Overnight Bedouin tent stay with comfortable mattress, fresh linens, pillows & blankets',
                'Freshly prepared Arabian sunrise breakfast with eggs, warm bread, cheese, tea & Arabic Gahwa coffee',
                'Morning sunrise viewing across tranquil open desert dunes'
            ],
            'exclusions' => ['Quad biking & dune buggies (optional add-on)', 'Alcoholic beverages', 'Personal expenses & gratuities'],
            'details' => [
                'overview' => 'Experience the quiet magic of the Arabian desert after all evening visitors depart. Enjoy the complete Evening Desert Safari with dune bashing, camel rides, live cultural shows, and a lavish BBQ dinner buffet. Then, settle into your comfortable private Bedouin tent under a star-lit desert sky. Wake at dawn to watch the golden sunrise over rolling sand dunes followed by a freshly prepared Arabian breakfast (eggs, bread, cheese, labneh, tea, and aromatic Arabic Gahwa coffee) before your 4x4 transfer back to your Dubai hotel.',
                'timings_pickup' => 'Pick-up between 02:45 PM – 03:30 PM (Day 1); return next morning by 08:30 AM – 09:00 AM (Day 2). Available from Dubai, Sharjah, and Abu Dhabi hotels.',
                'important_notes' => 'Comfortable casual clothes recommended. Bring a light jacket or sweater between November and March as desert night temperatures drop. Tents are furnished with clean bedding and clean camp washrooms with showers are available.',
                'cancellation' => 'Free cancellation up to 48 hours before scheduled pick-up.',
                'reviews' => [
                    ['author' => 'Claire M. (France)', 'rating' => 5, 'comment' => 'Waking up to total silence on golden dunes with hot Arabic coffee and fresh breakfast was the highlight of our UAE trip.'],
                ],
            ],
        ],
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 4, 'featured' => 1,
            'title' => 'VIP Private Desert Safari',
            'slug' => 'vip-private-desert-safari',
            'short_desc' => 'Private 4x4, private camp area and premium dinner for your group',
            'duration' => '6 hours',
            'pickup_line' => '6 hours, private 4x4 pick-up',
            'has_pickup' => 1,
            'price_aed' => 320, 'child_price_aed' => 250,
            'age_policy' => 'All ages welcome (Private 4x4 adjusts driving pace to your family)',
            'inclusions' => ['Exclusive private 4x4 Land Cruiser for your group', 'Custom dune-bashing intensity, camel ride & sunset stop', 'Reserved VIP majlis seating area with dedicated table waiter', 'Premium grilled BBQ platter, mezze, fruits, desserts & soft drinks', 'All live desert camp shows'],
            'exclusions' => ['Quad bikes & dune buggies', 'Professional photo frames', 'Tips'],
            'details' => [
                'overview' => 'Designed for families, couples, and small groups seeking privacy and comfort. Travel in your own dedicated 4x4 at your preferred time, skip buffet queues completely, and enjoy waiter-served premium BBQ dining in a reserved VIP lounge facing the stage.',
                'timings_pickup' => 'Flexible afternoon pick-up between 02:30 PM – 03:30 PM from any hotel or residence in Dubai, Sharjah, or Abu Dhabi.',
                'important_notes' => 'Because the 4x4 is private, dune bashing can be made gentle or skipped altogether for families with young children or seniors.',
                'cancellation' => 'Free cancellation up to 24 hours before scheduled pick-up.',
                'reviews' => [
                    ['author' => 'Omar A. (Saudi Arabia)', 'rating' => 5, 'comment' => 'Private Land Cruiser and VIP majlis service made it effortless with our kids and parents.'],
                ],
            ],
        ],
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 5, 'featured' => 0,
            'title' => 'Quad Bike Desert Safari',
            'slug' => 'quad-bike-desert-safari',
            'short_desc' => 'Guided quad bike ride across the dunes, add-on to any safari',
            'duration' => '1 hour',
            'pickup_line' => '1 hour ride, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 220, 'child_price_aed' => 180,
            'age_policy' => 'Riders 16+ yrs for open-desert ATV (10–15 yrs in supervised circuit)',
            'inclusions' => ['Return hotel transfer or seamless add-on to Morning/Evening Safari', '60-minute guided automatic ATV quad bike ride on open red dunes', 'Safety helmet, goggles, briefing & desert lead marshal', 'Cold bottled water & sandboarding pause'],
            'exclusions' => ['Damage insurance waiver', 'Camp BBQ dinner unless combined with Evening Safari', 'Tips'],
            'details' => [
                'overview' => 'Take control of an automatic all-terrain quad bike and follow an experienced desert marshal across open Lahbab sand bowls and ridges. Bookable as a dedicated morning/afternoon ride with transfer or added directly onto any safari.',
                'timings_pickup' => 'Morning (08:00 AM), Afternoon (02:30 PM), or Sunset slots daily. Pick-up from Dubai & Sharjah hotels.',
                'important_notes' => 'Closed-toe shoes recommended. Not permitted for pregnant guests or guests with back/neck injuries.',
                'cancellation' => 'Free cancellation up to 24 hours in advance.',
                'reviews' => [
                    ['author' => 'Liam T. (Australia)', 'rating' => 5, 'comment' => 'Proper open-desert dunes—not a tiny fenced pen! 60 full minutes of riding.'],
                ],
            ],
        ],
        [
            'cat_id' => 1, 'pool' => 'desert', 'sort' => 6, 'featured' => 0,
            'title' => 'Hot Air Balloon and Desert Breakfast',
            'slug' => 'hot-air-balloon-and-desert-breakfast',
            'short_desc' => 'Sunrise balloon flight over the desert followed by breakfast',
            'duration' => '5 hours',
            'pickup_line' => '5 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 990, 'child_price_aed' => 850,
            'age_policy' => 'Adults 12–79 yrs | Children 5–11 yrs (Minimum age 5 yrs)',
            'inclusions' => ['Pre-dawn hotel pick-up & drop-off in Dubai', '45–60 minute sunrise hot air balloon flight up to 4,000 ft', 'Spotting Arabian oryx & gazelles over Margham desert', 'Gourmet Bedouin camp breakfast & flight certificate'],
            'exclusions' => ['In-flight drone/video package (optional)', 'Abu Dhabi hotel transfer supplement', 'Tips'],
            'details' => [
                'overview' => 'Float silently up to 4,000 feet above untouched red dunes as the sun rises over the Hajar Mountains. After landing, celebrate at a serene desert oasis camp with a gourmet breakfast spread before returning to your hotel.',
                'timings_pickup' => 'Pick-up between 04:00 AM – 04:45 AM (exact time confirmed on WhatsApp the evening prior); return by 09:30 AM.',
                'important_notes' => 'Not permitted for pregnant guests, children under 5, or anyone who has scuba dived within 24 hours or had recent major surgery.',
                'cancellation' => 'Free cancellation up to 48 hours prior; 100% refund or free reschedule if cancelled due to wind/weather.',
                'reviews' => [
                    ['author' => 'Elena V. (Spain)', 'rating' => 5, 'comment' => 'Watching Arabian oryx run across golden dunes from a sunrise balloon was pure magic.'],
                ],
            ],
        ],

        // === CATEGORY 2: CITY TOURS (6) ===
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 1, 'featured' => 1,
            'title' => 'Dubai City Tour (Half Day)',
            'slug' => 'dubai-city-tour-half-day',
            'short_desc' => 'Burj Khalifa photo stop, Jumeirah Mosque, Dubai Frame, Palm view',
            'duration' => '4 hours',
            'pickup_line' => '4 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 110, 'child_price_aed' => 85,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Under 3 free',
            'inclusions' => ['Air-conditioned coach/minivan hotel pick-up & drop-off', 'Licensed English-speaking UAE tour guide', 'Photo stops at Burj Khalifa/Downtown, Museum of the Future, Dubai Frame, Jumeirah Mosque, Burj Al Arab & Palm Jumeirah', 'Chilled bottled mineral water'],
            'exclusions' => ['Inside observation deck entry tickets (can be added on WhatsApp)', 'Meals & personal shopping', 'Tips'],
            'details' => [
                'overview' => 'Get a complete orientation to modern and coastal Dubai in one seamless morning or afternoon half-day guided tour—covering iconic skyline landmarks with plenty of guided photo stops.',
                'route_stops' => ['Jumeirah Mosque exterior stop', 'Jumeirah Beach & Burj Al Arab photo stop', 'Palm Jumeirah Island & Atlantis The Palm view', 'Dubai Marina & Sheikh Zayed Road drive-pass', 'Dubai Frame & Museum of the Future exterior stop', 'Downtown Dubai & Burj Khalifa photo stop'],
                'timings_pickup' => 'Morning departure 08:30 AM – 09:00 AM or Afternoon 02:00 PM. Hotel pick-up across Dubai & Sharjah.',
                'dress_code' => 'Smart casual comfortable clothing; modest shoulder-and-knee coverage recommended if stepping near mosque courtyards.',
                'cancellation' => 'Free cancellation up to 24 hours before tour start.',
                'reviews' => [
                    ['author' => 'James B. (UK)', 'rating' => 5, 'comment' => 'Ideal first-day overview of Dubai—clean AC van, knowledgeable guide, and great photo stops.'],
                ],
            ],
        ],
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 2, 'featured' => 1,
            'title' => 'Old and New Dubai Tour',
            'slug' => 'old-and-new-dubai-tour',
            'short_desc' => 'Al Fahidi district, abra boat ride, gold and spice souks, modern downtown',
            'duration' => '5 hours',
            'pickup_line' => '5 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 145, 'child_price_aed' => 110,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Under 3 free',
            'inclusions' => ['Hotel pick-up & drop-off in air-conditioned vehicle', 'Licensed heritage & city tour guide', 'Traditional wooden Abra water-taxi ticket across Dubai Creek', 'Guided walk in Al Fahidi Historical Neighbourhood, Gold Souk & Spice Souk', 'Modern Dubai drive & Downtown photo stops + bottled water'],
            'exclusions' => ['Optional inside museum entry tickets & meals', 'Souk shopping', 'Tips'],
            'details' => [
                'overview' => "Trace Dubai's story from pearl-diving creek settlement to futuristic metropolis. Stroll wind-tower lanes in Al Fahidi, cross Dubai Creek on a wooden abra, smell saffron and frankincense in the Spice Souk, and finish beneath the gleaming towers of Sheikh Zayed Road and Downtown.",
                'route_stops' => ['Al Fahidi Historical District & wind-tower courtyards', 'Traditional Abra boat crossing on Dubai Creek', 'Deira Spice Souk & Gold Souk guided walk', 'Dubai Frame & Museum of the Future photo stop', 'Downtown Dubai & Burj Khalifa'],
                'timings_pickup' => 'Daily pick-up 09:00 AM – 09:30 AM from Dubai & Sharjah hotels.',
                'dress_code' => 'Comfortable walking shoes and light modest attire suitable for historic heritage quarters.',
                'cancellation' => 'Free cancellation up to 24 hours before departure.',
                'reviews' => [
                    ['author' => 'Nadia L. (Canada)', 'rating' => 5, 'comment' => "Crossing the Creek by abra and exploring Al Fahidi with a local guide brought Dubai's history alive."],
                ],
            ],
        ],
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 3, 'featured' => 1,
            'title' => 'Abu Dhabi City Tour',
            'slug' => 'abu-dhabi-city-tour',
            'short_desc' => 'Sheikh Zayed Grand Mosque, Corniche, Emirates Palace photo stop, Heritage Village',
            'duration' => '9 hours',
            'pickup_line' => '9 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 175, 'child_price_aed' => 140,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Under 3 free',
            'inclusions' => ['Round-trip AC transport from Dubai or Abu Dhabi hotels', 'Sheikh Zayed Grand Mosque guided entry visit', 'Abu Dhabi Corniche, Emirates Palace photo stop & Heritage Village', 'Dates Market & Yas Island Ferrari World photo stop', 'Licensed guide & bottled water'],
            'exclusions' => ['Lunch (1-hour stop at Marina Mall food court/restaurants)', 'Qasr Al Watan or Louvre inside tickets (available as upgrade)', 'Tips'],
            'details' => [
                'overview' => 'Discover the UAE capital on a full-day guided journey from Dubai. Marvel at the white marble domes and Swarovski chandeliers of Sheikh Zayed Grand Mosque, drive the turquoise Corniche, pause for photos at Emirates Palace, and explore traditional Emirati crafts at Heritage Village.',
                'route_stops' => ['Sheikh Zayed Grand Mosque (inside guided visit)', 'Emirates Palace Mandarin Oriental photo stop', 'Abu Dhabi Corniche waterfront', 'Abu Dhabi Heritage Village', 'Abu Dhabi Dates Market', 'Yas Island Ferrari World exterior photo stop'],
                'timings_pickup' => 'Daily pick-up 08:00 AM – 08:30 AM from Dubai hotels (09:30 AM in Abu Dhabi); return ~05:30 PM.',
                'dress_code' => 'Strict Mosque Dress Code: Women must wear loose ankle-length trousers/skirt, long sleeves to wrists, and a headscarf; Men must wear long trousers and covered shoulders (no shorts or sleeveless tops).',
                'cancellation' => 'Free cancellation up to 24 hours before pick-up.',
                'reviews' => [
                    ['author' => 'Sophia G. (USA)', 'rating' => 5, 'comment' => 'Sheikh Zayed Grand Mosque took our breath away. Well-paced full day with smooth hotel transfers.'],
                ],
            ],
        ],
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 4, 'featured' => 1,
            'title' => 'Sharjah and Ajman Tour',
            'slug' => 'sharjah-and-ajman-tour',
            'short_desc' => 'Sharjah heritage area, museums, souks and Ajman Corniche',
            'duration' => '6 hours',
            'pickup_line' => '6 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 165, 'child_price_aed' => 130,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs',
            'inclusions' => ['Hotel pick-up & drop-off from Dubai, Sharjah or Ajman', 'Licensed cultural tour guide', 'Heart of Sharjah heritage area, Blue Souk & Souk Al Arsah', 'King Faisal Mosque & Sharjah Mosque photo stops + Ajman Corniche', 'Bottled water'],
            'exclusions' => ['Optional museum entry fees', 'Meals', 'Tips'],
            'details' => [
                'overview' => 'Visit UNESCO-recognized Sharjah—the Cultural Capital of the Arab World—alongside neighbouring coastal Ajman. Explore traditional coral-stone architecture, bustling carpet and silver souks, grand mosques, and the breezy Ajman Corniche.',
                'route_stops' => ['Sharjah Mosque / King Faisal Mosque photo stop', 'Heart of Sharjah & Souk Al Arsah', 'Sharjah Museum of Islamic Civilization exterior / optional visit', 'Central Souk (Blue Souk)', 'Ajman Corniche waterfront'],
                'timings_pickup' => 'Pick-up 08:30 AM – 09:00 AM (Sun–Thu & Sat); return ~02:30 PM.',
                'dress_code' => "Modest dress covering shoulders and knees is appreciated across Sharjah's cultural districts.",
                'cancellation' => 'Free cancellation up to 24 hours in advance.',
                'reviews' => [
                    ['author' => 'Tariq M. (UK)', 'rating' => 5, 'comment' => 'A wonderful cultural contrast to Downtown Dubai—loved the Blue Souk and Sharjah heritage lanes.'],
                ],
            ],
        ],
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 5, 'featured' => 0,
            'title' => 'Private City Tour with Driver',
            'slug' => 'private-city-tour-with-driver',
            'short_desc' => 'Your own car and guide, you choose the stops',
            'duration' => '4 to 8 hours',
            'pickup_line' => '4 to 8 hours, private car pick-up',
            'has_pickup' => 1,
            'price_aed' => 490, 'child_price_aed' => 0,
            'age_policy' => 'Private vehicle rate up to 6 guests (Child seats available on request)',
            'inclusions' => ['Private modern SUV/minivan with English-speaking driver-guide', '100% customizable itinerary in Dubai, Abu Dhabi, or Sharjah', 'Door-to-door pick-up & drop-off at hotel, cruise port, or airport', 'Fuel, Salik tolls, parking & chilled bottled water'],
            'exclusions' => ['Paid attraction entry tickets', 'Meals', 'Licensed multi-language walking guide upgrade (optional)'],
            'details' => [
                'overview' => "Explore the UAE at your own pace with a private air-conditioned SUV and experienced local driver. Choose a 4-hour half-day or 8-hour full-day charter and tailor every stop around your family's wish list.",
                'route_stops' => ['100% flexible — Old Dubai, Downtown, Palm Jumeirah, Dubai Marina, Miracle Garden, or Abu Dhabi landmarks'],
                'timings_pickup' => 'Flexible start time (morning, afternoon, or evening) from any Dubai, Sharjah, or Abu Dhabi address.',
                'dress_code' => 'Casual attire; modest cover required only if visiting mosques.',
                'cancellation' => 'Free cancellation up to 24 hours prior.',
                'reviews' => [
                    ['author' => 'Victoria H. (Switzerland)', 'rating' => 5, 'comment' => 'So convenient with elderly parents—immaculate SUV and we covered every landmark without rushing.'],
                ],
            ],
        ],
        [
            'cat_id' => 2, 'pool' => 'city', 'sort' => 6, 'featured' => 0,
            'title' => 'Dubai Night Lights Tour',
            'slug' => 'dubai-night-lights-tour',
            'short_desc' => 'Evening drive past illuminated landmarks with photo stops',
            'duration' => '3 hours',
            'pickup_line' => '3 hours, hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 135, 'child_price_aed' => 100,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Under 3 free',
            'inclusions' => ['Evening hotel pick-up & drop-off in Dubai', 'Guided drive along illuminated Sheikh Zayed Road', 'Photo stops at Dubai Marina promenade, Madinat Jumeirah / Burj Al Arab, Museum of the Future & Dubai Fountain show', 'Bottled water'],
            'exclusions' => ['Dinner & paid observation deck tickets', 'Tips'],
            'details' => [
                'overview' => 'See Dubai transform after dark when skyscrapers, bridges, and waterfront promenades light up the night sky. Includes prime evening photo stops and watching the choreographed Dubai Fountain show beneath Burj Khalifa.',
                'route_stops' => ['Dubai Marina illuminated waterfront walk', 'Palm Jumeirah & Atlantis night viewpoint', 'Souk Madinat Jumeirah & Burj Al Arab view', 'Museum of the Future lit calligraphy facade', 'Dubai Fountain & Burj Khalifa LED show'],
                'timings_pickup' => 'Daily pick-up 06:30 PM – 07:00 PM; drop-off around 10:00 PM.',
                'dress_code' => 'Smart casual evening attire.',
                'cancellation' => 'Free cancellation up to 24 hours before pick-up.',
                'reviews' => [
                    ['author' => 'Carlos D. (Brazil)', 'rating' => 5, 'comment' => 'Dubai at night looks like a sci-fi movie—Museum of the Future and the Fountain stop were amazing.'],
                ],
            ],
        ],

        // === CATEGORY 3: CRUISE DINNER (6) ===
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 1, 'featured' => 1,
            'title' => 'Dhow Cruise Dinner, Dubai Marina',
            'slug' => 'dhow-cruise-dinner-dubai-marina',
            'short_desc' => 'Traditional wooden dhow, buffet dinner, soft drinks and live music',
            'duration' => '2 hours',
            'pickup_line' => '2 hours, optional hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 135, 'child_price_aed' => 95,
            'age_policy' => 'Adults 11+ yrs | Children 3–10 yrs | Infants under 3 free',
            'inclusions' => ['2-hour evening cruise through Dubai Marina, JBR & Bluewaters', 'Welcome Arabic coffee, dates & chilled juice', '5-star international & Asian buffet dinner (veg & non-veg)', 'Unlimited water, tea, coffee & soft drinks', 'Live Tanoura show & background music'],
            'exclusions' => ["Hotel transfers if 'Direct Boarding' selected (Private/shared hotel transfer available)", 'Alcoholic beverages (available on board)', 'Tips'],
            'details' => [
                'overview' => 'Glide past the glittering towers of Dubai Marina, JBR, and Ain Dubai aboard a handcrafted wooden Arabian dhow while enjoying an international buffet dinner and live Tanoura entertainment.',
                'boarding_point' => 'Dubai Marina Pier 7 / Marina Promenade Berth — arrive by 08:00 PM for 08:30 PM departure (returns 10:30 PM). Map pin sent instantly on WhatsApp.',
                'menu_highlights' => "Live pasta & grill station, Arabic mezze (hummus, moutabal, fattoush), butter chicken, grilled fish, veg biryani, kids' nuggets & fries, Umm Ali & assorted pastries. Unlimited soft drinks, water, tea & coffee.",
                'seating_options' => 'Open-air Upper Deck (panoramic skyline breeze) or Air-conditioned Glass-window Lower Deck.',
                'entertainment' => 'Traditional illuminated Tanoura spinning dance show & lounge music.',
                'dress_code' => 'Smart casual (light jacket recommended on upper deck in winter).',
                'cancellation' => 'Free cancellation up to 24 hours prior to sailing.',
                'reviews' => [
                    ['author' => 'Amelia P. (UK)', 'rating' => 5, 'comment' => 'Upper deck views of Dubai Marina lit up at night were unbeatable. Delicious buffet!'],
                ],
            ],
        ],
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 2, 'featured' => 1,
            'title' => 'Dhow Cruise Dinner, Dubai Creek',
            'slug' => 'dhow-cruise-dinner-dubai-creek',
            'short_desc' => 'Old Dubai skyline, buffet dinner, Tanoura show',
            'duration' => '2 hours',
            'pickup_line' => '2 hours, optional hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 85, 'child_price_aed' => 65,
            'age_policy' => 'Adults 11+ yrs | Children 3–10 yrs | Infants under 3 free',
            'inclusions' => ['2-hour cruise along historic Dubai Creek (Deira & Bur Dubai)', 'Welcome drinks & Arabic dates', 'International buffet dinner (vegetarian & non-vegetarian)', 'Unlimited soft drinks, mineral water, tea & coffee', 'Live Tanoura cultural dance performance'],
            'exclusions' => ['Optional hotel pick-up & drop-off supplement', 'Specialty beverages', 'Tips'],
            'details' => [
                'overview' => 'Sail the historic waters of Dubai Creek where old wind-towers, Heritage Village, and wooden trading dhows meet illuminated Deira landmarks—complete with buffet dining and live Tanoura entertainment.',
                'boarding_point' => 'Al Seef / Deira Creek Dhow Wharfage — boarding at 07:45 PM, cruising 08:30 PM – 10:30 PM. Exact Google Maps pin shared via WhatsApp.',
                'menu_highlights' => 'Fresh salads, Arabic mezze, grilled kebabs, chicken curry, vegetarian noodles & dal, fresh fruit, and Arabic sweets with unlimited soft drinks.',
                'seating_options' => 'Open-air Upper Deck or Air-conditioned Lower Deck.',
                'entertainment' => 'Live Tanoura folkloric dance & traditional music.',
                'dress_code' => 'Smart casual, family-friendly attire.',
                'cancellation' => 'Free cancellation up to 24 hours before cruise date.',
                'reviews' => [
                    ['author' => 'Rizwan K. (UAE)', 'rating' => 5, 'comment' => 'Great value family evening along Al Seef and Old Dubai Creek.'],
                ],
            ],
        ],
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 3, 'featured' => 1,
            'title' => 'Luxury Yacht Dinner Cruise',
            'slug' => 'luxury-yacht-dinner-cruise',
            'short_desc' => 'Modern yacht with a 3-course set dinner and indoor and outdoor seating',
            'duration' => '2.5 hours',
            'pickup_line' => '2.5 hours, Dubai Harbour boarding',
            'has_pickup' => 1,
            'price_aed' => 260, 'child_price_aed' => 190,
            'age_policy' => 'Adults 12+ yrs | Children 4–11 yrs | Infants under 4 free',
            'inclusions' => ['2.5-hour mega-yacht/modern glass-yacht cruise past Marina, JBR, Bluewaters & Palm crescent', 'Red-carpet boarding & welcome mocktail', '3-course gourmet dinner / live chef stations (veg, non-veg & kids)', 'Indoor panoramic salon & open sky-deck lounge seating', 'Live saxophonist / acoustic vocalist & DJ'],
            'exclusions' => ['Premium house beverage upgrade package', 'Optional private hotel chauffeur transfer', 'Tips'],
            'details' => [
                'overview' => 'Step aboard a sleek modern luxury yacht with panoramic glass lounges and an open-air sky deck. Dine on chef-prepared courses while cruising past Dubai Marina, Ain Dubai, and the illuminated Palm Jumeirah shoreline.',
                'boarding_point' => 'Dubai Harbour / Pier 7 Yacht Marina — check-in 07:30 PM, sailing 08:00 PM – 10:30 PM.',
                'menu_highlights' => "Artisanal starters, grilled salmon or Angus beef/chicken main (plus gourmet vegetarian & kids' pasta options), decadent chocolate dessert, mocktails & soft drinks.",
                'seating_options' => 'Lower panoramic glass dining deck, Open Sky Deck lounge, or VIP reserved bow table.',
                'entertainment' => 'Live acoustic/saxophone performance & ambient lounge DJ.',
                'dress_code' => 'Smart elegant / resort evening wear (no flip-flops or swimwear).',
                'cancellation' => 'Free cancellation up to 24 hours prior.',
                'reviews' => [
                    ['author' => 'Lina S. (Sweden)', 'rating' => 5, 'comment' => 'Five-star service, live saxophone on the upper deck, and incredible views of Ain Dubai.'],
                ],
            ],
        ],
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 4, 'featured' => 1,
            'title' => 'Private Yacht Dinner (Couples)',
            'slug' => 'private-yacht-dinner-couples',
            'short_desc' => 'Private table, decorated setup, ideal for proposals and anniversaries',
            'duration' => '2 hours',
            'pickup_line' => '2 hours, private yacht charter',
            'has_pickup' => 1,
            'price_aed' => 1250, 'child_price_aed' => 0,
            'age_policy' => 'Private couple charter (2 guests included; custom celebration add-ons available)',
            'inclusions' => ['Private 2-hour luxury motor yacht charter with licensed captain & crew', 'Romantic candlelit table setup with rose petals & floral centerpiece', '3-course dinner for two, sparkling non-alcoholic toast & celebration cake', 'Private Bluetooth sound system & Marina/JBR/Burj Al Arab lagoon route'],
            'exclusions' => ["Custom balloon arch / marquee 'MARRY ME' letters (available on WhatsApp)", 'Professional onboard photographer add-on'],
            'details' => [
                'overview' => 'Celebrate an unforgettable proposal, honeymoon, birthday, or anniversary on your own private yacht in Dubai Marina—complete with floral table styling, celebration cake, and skyline views.',
                'boarding_point' => 'Dubai Marina Yacht Club / Dubai Harbour Gate P — flexible sunset (05:30 PM) or night (08:00 PM) departure.',
                'menu_highlights' => 'Choice of grilled seafood, steak & chicken continental menu or vegetarian gourmet menu, celebration cake, fresh juices, soft drinks & water.',
                'seating_options' => '100% private aft dining deck, sunbed bow lounge & air-conditioned cabin.',
                'entertainment' => 'Custom playlist via onboard Bose sound system; optional live violinist on request.',
                'dress_code' => 'Smart casual / evening celebration attire (barefoot or clean soft deck shoes onboard).',
                'cancellation' => 'Free reschedule or cancellation up to 48 hours prior.',
                'reviews' => [
                    ['author' => 'David & Chloe (UK)', 'rating' => 5, 'comment' => 'Proposed in front of the lit Marina skyline—the crew set up flowers and cake flawlessly!'],
                ],
            ],
        ],
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 5, 'featured' => 0,
            'title' => 'Family Dinner Cruise',
            'slug' => 'family-dinner-cruise',
            'short_desc' => 'Kid-friendly menu and entertainment',
            'duration' => '2 hours',
            'pickup_line' => '2 hours, optional hotel pick-up',
            'has_pickup' => 1,
            'price_aed' => 120, 'child_price_aed' => 75,
            'age_policy' => 'Adults 12+ yrs | Children 3–11 yrs | Infants under 3 free (stroller-friendly)',
            'inclusions' => ['2-hour calm-water evening dinner cruise', 'Dedicated kid-friendly buffet corner alongside full international adult buffet', 'Unlimited juices, water, tea & soft drinks', 'Family-friendly magic/puppet segment & colorful Tanoura show'],
            'exclusions' => ['Optional hotel transfer', 'Birthday cake customization (ask on WhatsApp)'],
            'details' => [
                'overview' => 'An early, relaxed evening cruise designed with families in mind—featuring stroller-accessible boarding, high chairs, kid-loved menu favourites alongside Arabic & continental dishes, and lively family entertainment.',
                'boarding_point' => 'Dubai Creek / Dubai Marina family berth — boarding 07:30 PM, sailing 08:00 PM – 10:00 PM.',
                'menu_highlights' => "Kids' mini-sliders, pasta, crispy chicken strips & cupcakes; Adult Arabic mezze, BBQ grills, curries, salads & desserts.",
                'seating_options' => 'Enclosed air-conditioned family dining deck or railing-protected upper deck.',
                'entertainment' => 'Tanoura spinning show & family interactive entertainment.',
                'dress_code' => 'Casual family wear.',
                'cancellation' => 'Free cancellation up to 24 hours before sailing.',
                'reviews' => [
                    ['author' => 'Fatima N. (Kuwait)', 'rating' => 5, 'comment' => 'Kids loved the Tanoura lights and mini pasta bar while we enjoyed the calm skyline views.'],
                ],
            ],
        ],
        [
            'cat_id' => 3, 'pool' => 'cruise', 'sort' => 6, 'featured' => 0,
            'title' => 'Corporate and Group Cruise',
            'slug' => 'corporate-and-group-cruise',
            'short_desc' => 'Private booking for 20 or more guests',
            'duration' => '3 hours',
            'pickup_line' => '3 hours, private group charter',
            'has_pickup' => 1,
            'price_aed' => 165, 'child_price_aed' => 120,
            'age_policy' => 'Group rate per guest (Minimum 20 guests; vessels available up to 150 guests)',
            'inclusions' => ['Exclusive 3-hour private vessel charter in Dubai Marina or Creek', 'Custom banquet buffet dinner, welcome mocktails & unlimited soft drinks', 'PA microphone, projector/screen & background music/DJ setup', 'Dedicated onboard event coordinator'],
            'exclusions' => ['Group coach hotel transfers (available on request)', 'Custom corporate branding banners & floral styling'],
            'details' => [
                'overview' => 'Host corporate gala dinners, team offsites, wedding welcome nights, or milestone group celebrations aboard a privately chartered glass boat, traditional dhow, or mega-yacht for 20 to 150+ guests.',
                'boarding_point' => 'Dubai Marina or Dubai Harbour group pier — arrival 30 minutes prior to custom charter slot.',
                'menu_highlights' => 'Tailored 4-star or 5-star international buffet (vegetarian, halal grilled meats, seafood, live stations & desserts).',
                'seating_options' => 'Full dual-deck exclusive use (indoor banquet lower deck + open-air upper lounge deck).',
                'entertainment' => 'PA system for speeches, background DJ/music, and optional Tanoura or live musicians.',
                'dress_code' => 'Business casual or festive event attire.',
                'cancellation' => 'Flexible group terms confirmed on booking agreement.',
                'reviews' => [
                    ['author' => 'Stefan M. (Corporate Event, 45 pax)', 'rating' => 5, 'comment' => 'Seamless charter for our regional sales summit—great food, mic setup, and Marina views.'],
                ],
            ],
        ],

        // === CATEGORY 4: WATERSPORTS (8) ===
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 1, 'featured' => 1,
            'title' => 'Jet Ski Ride',
            'slug' => 'jet-ski-ride',
            'short_desc' => 'Ride past Burj Al Arab and Palm Jumeirah with a guide',
            'duration' => '30 to 60 minutes',
            'pickup_line' => '30 to 60 minutes, Marina beach start',
            'has_pickup' => 0,
            'price_aed' => 280, 'child_price_aed' => 0,
            'age_policy' => 'Minimum driver age 16+ yrs | Passenger 8+ yrs (Up to 2 riders per jet ski)',
            'inclusions' => ['Latest high-powered Yamaha VX/FX jet ski', 'Certified watersports safety briefing & lead guide on separate jet ski', 'Coastguard-approved life jackets, waterproof phone pouch & lockers', 'Complimentary action photos & short video clips taken by guide'],
            'exclusions' => ['Hotel transfers (optional private transfer on request)', 'Towels & swimwear'],
            'details' => [
                'overview' => 'Ride modern Yamaha jet skis across turquoise Arabian Gulf waters with a certified lead instructor—framing views of Dubai Marina, JBR, Burj Al Arab, and Palm Jumeirah.',
                'start_location' => 'Umm Suqeim Fishing Harbour / Jumeirah Beach Marina (15 mins from Dubai Marina & Downtown). Exact pin shared on WhatsApp.',
                'safety_requirements' => 'Minimum age 16 to drive, 8 to ride as passenger. Combined weight max 180 kg per jet ski. Basic water confidence required; life jackets mandatory. Valid passport/Emirates ID required by UAE Coastguard.',
                'what_to_bring' => 'Original Passport or Emirates ID, swimwear, towel, Reef-safe sunscreen & sunglasses strap.',
                'weather_policy' => '100% free rescheduling or full refund if UAE Coastguard restricts sea access due to wind/waves.',
                'package_discounts' => 'Combo Deal: Add a 15-minute Parasailing flight and save 15% when booked together on WhatsApp.',
                'reviews' => [
                    ['author' => 'Kevin O. (Ireland)', 'rating' => 5, 'comment' => 'Brand-new fast jet skis and our guide took awesome GoPro photos in front of Burj Al Arab for free!'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 2, 'featured' => 1,
            'title' => 'Jet Ski Tour (Burj Al Arab)',
            'slug' => 'jet-ski-tour-burj-al-arab',
            'short_desc' => 'Guided route with photo stops',
            'duration' => '1 hour',
            'pickup_line' => '1 hour, guided coastal route',
            'has_pickup' => 0,
            'price_aed' => 390, 'child_price_aed' => 0,
            'age_policy' => 'Driver 16+ yrs | Passenger 8+ yrs (Price per jet ski for 1 or 2 riders)',
            'inclusions' => ['60-minute guided coastal jet ski tour to Burj Al Arab & Palm Jumeirah', 'Certified instructor, safety briefing & life jackets', 'Dedicated water-level photo & video stops at Burj Al Arab', 'Bottled water, showers & locker access'],
            'exclusions' => ['Hotel pick-up & drop-off', 'Swimwear & towels'],
            'details' => [
                'overview' => 'Our most popular 1-hour guided jet ski safari cruises directly out to the iconic sail-shaped Burj Al Arab and along the royal beaches towards Palm Jumeirah with dedicated photo stops on the water.',
                'start_location' => 'Umm Suqeim Marina Berth 4, Jumeirah, Dubai (Arrive 20 minutes prior).',
                'safety_requirements' => 'Original ID/Passport mandatory. Driver min 16 yrs; max 180 kg combined weight. Not suitable for pregnant guests.',
                'what_to_bring' => 'Passport/ID, swimwear, towel & sunscreen.',
                'weather_policy' => 'Free rescheduling to any open slot or full refund in case of rough sea conditions.',
                'package_discounts' => 'Group of 3+ jet skis receives 10% combo discount via WhatsApp.',
                'reviews' => [
                    ['author' => 'Matteo F. (Italy)', 'rating' => 5, 'comment' => 'Seeing Burj Al Arab right from the water on a 1-hour jet ski tour was unforgettable.'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 3, 'featured' => 1,
            'title' => 'Parasailing',
            'slug' => 'parasailing',
            'short_desc' => 'Fly above the Palm and Dubai Marina coast',
            'duration' => '15 minutes',
            'pickup_line' => '15 min flight (1 hr boat trip)',
            'has_pickup' => 0,
            'price_aed' => 250, 'child_price_aed' => 220,
            'age_policy' => 'Minimum age 6+ yrs (Children under 12 fly tandem with adult)',
            'inclusions' => ['Winch-boat cruise off JBR & Dubai Marina', '12–15 minute parasailing flight up to 150 meters above the sea', 'Dry take-off and landing directly on the boat deck', 'Harness, life jacket & experienced captain/crew'],
            'exclusions' => ['GoPro flight video & photo package (optional extra)', 'Hotel transfers'],
            'details' => [
                'overview' => "Soar peacefully up to 150 meters above the turquoise waters of JBR and Palm Jumeirah with dry winch-boat launch and landing—giving bird's-eye views of Dubai Marina's skyscrapers.",
                'start_location' => 'Dubai Marina / JBR Beach Watersports Pontoon.',
                'safety_requirements' => 'Single or tandem flights subject to wind; total harness weight 45 kg min – 180 kg max. No swimming skill required (dry boat landing).',
                'what_to_bring' => 'Comfortable beachwear/shorts, towel, sunscreen & ID.',
                'weather_policy' => 'Flights depend on steady wind; free instant rescheduling or refund if wind exceeds safety limits.',
                'package_discounts' => 'Jet Ski + Parasailing Combo from AED 475 per person on WhatsApp.',
                'reviews' => [
                    ['author' => 'Aisha R. (Bahrain)', 'rating' => 5, 'comment' => 'Super smooth take-off and landing without even getting wet—amazing view of the Palm!'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 4, 'featured' => 1,
            'title' => 'Flyboarding',
            'slug' => 'flyboarding',
            'short_desc' => 'Water-jet powered flight above the sea',
            'duration' => '30 minutes',
            'pickup_line' => '30 minutes, 1-on-1 instructor',
            'has_pickup' => 0,
            'price_aed' => 340, 'child_price_aed' => 0,
            'age_policy' => 'Minimum age 16+ yrs',
            'inclusions' => ['30-minute session (10-min briefing + 20-min water-jet flight)', '1-on-1 certified flyboard instructor controlling thrust', 'Flyboard boots, helmet, impact vest & life jacket', 'Shower & locker access'],
            'exclusions' => ['Professional action photography/video', 'Hotel transfers'],
            'details' => [
                'overview' => 'Rise above the sea on twin hydro-thrust jets with one-on-one coaching from a certified Dubai flyboard instructor—most beginners stand and hover within their first 5 minutes!',
                'start_location' => 'Jumeirah / Dubai Marina Watersports Centre.',
                'safety_requirements' => 'Min age 16 yrs; weight 45 kg – 115 kg; must be comfortable swimming in deep water with a vest.',
                'what_to_bring' => 'Swimwear, towel, sunscreen & ID.',
                'weather_policy' => 'Free rescheduling if sea state is choppy.',
                'package_discounts' => 'Combine Flyboarding + 30-min Jet Ski and save AED 60.',
                'reviews' => [
                    ['author' => 'Lucas B. (Netherlands)', 'rating' => 5, 'comment' => 'Easier than it looks! Instructor got me flying 4 meters up within minutes.'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 5, 'featured' => 0,
            'title' => 'Banana Boat and Donut Ride',
            'slug' => 'banana-boat-and-donut-ride',
            'short_desc' => 'Fun group rides for families and friends',
            'duration' => '15 to 30 minutes',
            'pickup_line' => '15 to 30 minutes, JBR beach start',
            'has_pickup' => 0,
            'price_aed' => 110, 'child_price_aed' => 95,
            'age_policy' => 'Minimum age 6+ yrs (Minimum 2 riders)',
            'inclusions' => ['High-energy speedboat tow on inflatable Banana Boat or spinning Donut tube', 'Life jackets & safety briefing', 'Speed adjusted to family-mild or thrill-splash level'],
            'exclusions' => ['Photos & videos', 'Hotel transfers & towels'],
            'details' => [
                'overview' => 'Laugh-out-loud inflatable water rides towed by speedboat off JBR and Dubai Marina—perfect for families, teens, and groups of friends.',
                'start_location' => 'JBR Beach / Marina Watersports Kiosk.',
                'safety_requirements' => 'Min age 6 yrs accompanied by adult; life jackets provided and worn at all times.',
                'what_to_bring' => 'Swimwear, towel & change of clothes.',
                'weather_policy' => 'Free rescheduling in case of rough waves.',
                'package_discounts' => 'Book both Banana Boat + Donut Ride back-to-back (30 mins total) for AED 175/person.',
                'reviews' => [
                    ['author' => 'Samira K. (Jordan)', 'rating' => 5, 'comment' => "Our teenagers couldn't stop laughing on the donut ride! Safe and super fun."],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 6, 'featured' => 0,
            'title' => 'Speed Boat Tour',
            'slug' => 'speed-boat-tour',
            'short_desc' => 'Fast boat ride along the coast and Atlantis',
            'duration' => '1 hour',
            'pickup_line' => '1 hour, Dubai Marina pier',
            'has_pickup' => 0,
            'price_aed' => 160, 'child_price_aed' => 120,
            'age_policy' => 'Adults 12+ yrs | Children 5–11 yrs (Minimum age 5 yrs / 15 kg)',
            'inclusions' => ['60–90 min high-speed RIB sightseeing boat ride from Dubai Marina', 'Photo stops at Ain Dubai, JBR, Atlantis The Palm & Burj Al Arab', 'Life jackets, live captain commentary & chilled bottled water'],
            'exclusions' => ['Hotel transfers', 'Souvenir photo prints'],
            'details' => [
                'overview' => 'Zip out of Dubai Marina canal into the open Arabian Gulf on a rigid-inflatable speedboat—stopping for postcard water-level photos at Ain Dubai, Atlantis The Palm, and Burj Al Arab.',
                'start_location' => 'Dubai Marina Walk Berth (next to Marina Gate / Pier 7).',
                'safety_requirements' => 'Minimum child weight 15 kg (5+ yrs). Not permitted for pregnant guests or guests with back injuries.',
                'what_to_bring' => 'Casual clothes, sunglasses, sunscreen & camera/phone.',
                'weather_policy' => 'Free date/time change or full refund if sea conditions cancel departure.',
                'package_discounts' => 'Family of 4 saves 10% on morning departures.',
                'reviews' => [
                    ['author' => 'Oliver S. (Germany)', 'rating' => 5, 'comment' => 'Thrilling ride and the best angles of Atlantis and Burj Al Arab from the sea.'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 7, 'featured' => 0,
            'title' => 'Scuba Diving (Beginners)',
            'slug' => 'scuba-diving-beginners',
            'short_desc' => 'Training and a guided dive',
            'duration' => '2 to 3 hours',
            'pickup_line' => '2 to 3 hours, equipment included',
            'has_pickup' => 0,
            'price_aed' => 350, 'child_price_aed' => 0,
            'age_policy' => 'Minimum age 10+ yrs (No prior diving license required)',
            'inclusions' => ['PADI Discover Scuba safety & shallow-water breathing briefing', 'Full scuba gear (wetsuit, mask, fins, BCD, regulator & tank)', '45-minute guided underwater reef dive (up to 10–12m max) with instructor', 'Complimentary underwater photos & video'],
            'exclusions' => ['Hotel transfers', 'Certification card (full PADI course available on request)'],
            'details' => [
                'overview' => 'Try scuba diving in warm Arabian Gulf waters with zero prior experience needed. A PADI-certified instructor teaches you basic breathing and hand signals in shallow water before guiding you slowly past colourful reef fish.',
                'start_location' => 'Jumeirah 3 Dive Beach Centre, Dubai.',
                'safety_requirements' => 'Min age 10 yrs. Do NOT fly on an airplane within 12–18 hours after scuba diving. Medical questionnaire completed before dive.',
                'what_to_bring' => 'Swimwear, towel & Emirates ID / Passport.',
                'weather_policy' => 'Free rescheduling if underwater visibility or sea state is unsuitable.',
                'package_discounts' => 'Couples / 2+ divers pay AED 320 per person.',
                'reviews' => [
                    ['author' => 'Mina P. (South Korea)', 'rating' => 5, 'comment' => 'Patient instructors made my very first breath underwater calm and unforgettable—plus free underwater photos!'],
                ],
            ],
        ],
        [
            'cat_id' => 4, 'pool' => 'water', 'sort' => 8, 'featured' => 0,
            'title' => 'Fishing Trip',
            'slug' => 'fishing-trip',
            'short_desc' => 'Deep sea fishing with equipment and refreshments',
            'duration' => '4 hours',
            'pickup_line' => '4 hours, Dubai Marina harbour',
            'has_pickup' => 1,
            'price_aed' => 320, 'child_price_aed' => 250,
            'age_policy' => 'Shared trip 10+ yrs | Private charter welcomes all ages',
            'inclusions' => ['4-hour offshore deep-sea sport fishing charter from Dubai', 'Experienced captain, deckhand, sonar fish-finder & UAE fishing permit', 'All trolling & bottom-fishing rods, reels, tackle & bait', 'Ice box, chilled water & soft drinks (keep your catch!)'],
            'exclusions' => ['Onboard grilling (available on private 36ft+ charters)', 'Optional hotel transfer supplement on shared slots'],
            'details' => [
                'overview' => 'Head offshore into productive Arabian Gulf fishing grounds targeting kingfish, barracuda, hammour, cobia, and sultan ibrahim aboard a fully equipped sport-fishing boat with captain and crew.',
                'start_location' => 'Dubai Harbour / Umm Suqeim Fishing Marina — 06:00 AM morning or 02:00 PM afternoon departure.',
                'safety_requirements' => 'Original Passport or Emirates ID required by Coastguard for all passengers; life jackets onboard.',
                'what_to_bring' => 'Passport/ID, hat, sunglasses, sunscreen & light snacks.',
                'weather_policy' => '100% free rescheduling or refund if Coastguard issues offshore wind warning.',
                'package_discounts' => 'Private 4-hour boat charter for up to 8 guests available at special group rate on WhatsApp.',
                'reviews' => [
                    ['author' => 'Andrei V. (Romania)', 'rating' => 5, 'comment' => 'Caught barracuda and kingfish! Crew knew the exact offshore spots and handled all bait and gear.'],
                ],
            ],
        ],

        // === CATEGORY 5: TICKETS & ATTRACTIONS (9) ===
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 1, 'featured' => 1,
            'title' => 'Burj Khalifa (At the Top)',
            'slug' => 'burj-khalifa-at-the-top',
            'short_desc' => 'Observation deck tickets, standard and prime hours',
            'duration' => '1.5 to 2 hours',
            'pickup_line' => 'Landmark, timed-entry ticket',
            'has_pickup' => 0,
            'price_aed' => 179, 'child_price_aed' => 145,
            'age_policy' => 'Adults 8+ yrs | Children 3–7 yrs | Infants under 3 free',
            'inclusions' => ['Official timed-entry QR ticket to Burj Khalifa Levels 124 & 125', 'High-speed 60-second double-deck elevator ride', 'Indoor 360° observation deck, outdoor terrace & viewing telescopes'],
            'exclusions' => ['Prime sunset slot supplement (04:00 PM – 06:30 PM) or Level 148 SKY upgrade', 'Hotel transfers unless requested on WhatsApp'],
            'details' => [
                'attraction_type' => 'Landmark',
                'overview' => "Ascend the world's tallest building in 60 seconds and step out onto the open-air terrace of Level 124 and Level 125 for sweeping 360-degree views across Downtown Dubai, the Arabian Gulf, and surrounding desert.",
                'opening_hours' => 'Daily 07:00 AM – 12:00 Midnight (Last entry 11:15 PM).',
                'ticket_types' => [
                    ['name' => 'Levels 124 & 125 — Non-Prime Hours', 'adult' => 'AED 179', 'child' => 'AED 145'],
                    ['name' => 'Levels 124 & 125 — Prime Sunset Hours (16:00–18:30)', 'adult' => 'AED 259', 'child' => 'AED 165'],
                    ['name' => 'Level 148 SKY VIP Fast-Track + Lounge', 'adult' => 'AED 399', 'child' => 'AED 399'],
                ],
                'date_time_note' => 'Choose your preferred visit date and time window in the form below—exact entry slot is confirmed with you on WhatsApp before issuing.',
                'delivery_method' => 'Official mobile QR PDF ticket sent directly to your WhatsApp and Email.',
                'terms_rules' => 'Timed-entry tickets are valid for the selected date and slot; non-refundable once barcode is issued. Arrive at Dubai Mall Lower Ground entrance 20 minutes early.',
                'reviews' => [
                    ['author' => 'Rachel T. (USA)', 'rating' => 5, 'comment' => 'QR tickets arrived on WhatsApp in 5 minutes—scanned straight into the Burj Khalifa queue at Dubai Mall!'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 2, 'featured' => 1,
            'title' => 'Dubai Frame',
            'slug' => 'dubai-frame',
            'short_desc' => 'Skyline views from the glass-floor bridge',
            'duration' => '1 to 1.5 hours',
            'pickup_line' => 'Landmark, instant e-ticket',
            'has_pickup' => 0,
            'price_aed' => 55, 'child_price_aed' => 25,
            'age_policy' => 'Adults 13+ yrs | Children 3–12 yrs | Under 3 & People of Determination free',
            'inclusions' => ['Official entry ticket to Dubai Frame in Zabeel Park', 'Old Dubai gallery, 150m-high Sky Deck & pressure-sensitive crystal glass walk', 'Future Dubai immersive vortex tunnel'],
            'exclusions' => ['Food & beverages on Sky Deck', 'Hotel transfers (optional on WhatsApp)'],
            'details' => [
                'attraction_type' => 'Landmark',
                'overview' => 'Stand 150 meters high on the 93-meter glass Sky Bridge of Dubai Frame—looking north over historic Deira and Dubai Creek, and south across the towering skyscrapers of Sheikh Zayed Road.',
                'opening_hours' => 'Daily 09:00 AM – 09:00 PM (Last entry 08:30 PM).',
                'ticket_types' => [
                    ['name' => 'Standard Entry Ticket (Adult 13+)', 'adult' => 'AED 55', 'child' => 'AED 25'],
                ],
                'date_time_note' => 'Select your preferred visit date in the booking form; tickets allow flexible daytime or sunset entry.',
                'delivery_method' => 'Instant PDF barcode ticket via WhatsApp & Email.',
                'terms_rules' => 'Valid for single entry on selected date; non-refundable after issuance.',
                'reviews' => [
                    ['author' => 'Hassan M. (Egypt)', 'rating' => 5, 'comment' => 'Walking across the clear glass floor 150m up was thrilling! Quick ticket delivery on WhatsApp.'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 3, 'featured' => 1,
            'title' => 'Museum of the Future',
            'slug' => 'museum-of-the-future',
            'short_desc' => 'Timed-entry exhibitions',
            'duration' => '2 to 3 hours',
            'pickup_line' => 'Museum, timed-entry ticket',
            'has_pickup' => 0,
            'price_aed' => 159, 'child_price_aed' => 159,
            'age_policy' => 'Ages 4+ require standard ticket | Children under 4 enter free',
            'inclusions' => ['Official timed-entry ticket (or Pioneer flexible-entry pass upgrade)', 'Access to OSS Hope Space Station, Heal Institute, Al Waha & Future Heroes floors', 'Outdoor viewing deck inside the calligraphy torus'],
            'exclusions' => ['Valet parking', 'Hotel transfers (available on request)'],
            'details' => [
                'attraction_type' => 'Museum',
                'overview' => "Step inside Dubai's architectural icon to experience interactive journeys to the year 2071—from orbital space stations and bio-engineered rainforest libraries to futuristic wellness and kids' innovation labs.",
                'opening_hours' => 'Daily 09:30 AM – 09:00 PM (Last entry 07:00 PM).',
                'ticket_types' => [
                    ['name' => 'Timed-Entry Admission (Ages 4+)', 'adult' => 'AED 159', 'child' => 'AED 159'],
                    ['name' => 'Pioneer Pass — Anytime Entry + AED 50 Shop Credit', 'adult' => 'AED 399', 'child' => 'AED 399'],
                ],
                'date_time_note' => 'Museum of the Future slots sell out days ahead—pick your date in the form and our team confirms open times immediately on WhatsApp.',
                'delivery_method' => 'Official QR e-ticket delivered on WhatsApp & Email.',
                'terms_rules' => 'Strictly valid for booked date/time slot; non-refundable once issued.',
                'reviews' => [
                    ['author' => 'Emily C. (Singapore)', 'rating' => 5, 'comment' => 'Anchor Point secured morning slots for our family when everything looked tricky—incredible museum!'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 4, 'featured' => 1,
            'title' => 'Dubai Aquarium and Underwater Zoo',
            'slug' => 'dubai-aquarium-and-underwater-zoo',
            'short_desc' => 'Walk-through tunnel and zoo tickets',
            'duration' => '2 hours',
            'pickup_line' => 'Family attraction, instant ticket',
            'has_pickup' => 0,
            'price_aed' => 169, 'child_price_aed' => 169,
            'age_policy' => 'Ages 2+ yrs | Infants under 2 enter free',
            'inclusions' => ['48-meter walk-through 270° Underwater Tunnel at Dubai Mall', 'Underwater Zoo (Level 2), King Croc encounter & Penguin Cove/Submersible simulator (package dependent)'],
            'exclusions' => ['Cage snorkeling / shark dive upgrades', 'Hotel transfers'],
            'details' => [
                'attraction_type' => 'Family',
                'overview' => 'Walk through a 10-million-liter suspended aquarium tunnel surrounded by sand tiger sharks and giant stingrays inside Dubai Mall, then explore Rainforest, Rocky Shore, and King Croc habitats upstairs.',
                'opening_hours' => 'Mon–Thu 10:00 AM – 10:15 PM | Fri–Sun 10:00 AM – 11:15 PM (Last entry 45 mins before close).',
                'ticket_types' => [
                    ['name' => 'Aquarium Tunnel + Underwater Zoo', 'adult' => 'AED 169', 'child' => 'AED 169'],
                    ['name' => 'Ultimate Explorer Pass (Glass Boat + Penguin Cove)', 'adult' => 'AED 269', 'child' => 'AED 269'],
                ],
                'date_time_note' => 'Choose your preferred visit date in the form; open daytime entry during mall hours.',
                'delivery_method' => 'Instant QR e-ticket sent via WhatsApp & Email.',
                'terms_rules' => 'Single-day admission; non-refundable once barcode is generated.',
                'reviews' => [
                    ['author' => 'Zainab A. (Oman)', 'rating' => 5, 'comment' => 'Must-do with kids inside Dubai Mall—seeing King Croc and sharks overhead was awesome.'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 5, 'featured' => 0,
            'title' => 'Ferrari World Abu Dhabi',
            'slug' => 'ferrari-world-abu-dhabi',
            'short_desc' => 'Theme park entry, with fast-track option',
            'duration' => 'Full day',
            'pickup_line' => 'Theme park, instant ticket',
            'has_pickup' => 0,
            'price_aed' => 345, 'child_price_aed' => 265,
            'age_policy' => 'Adults 1.3m+ height | Junior under 1.3m | Under 3 yrs free',
            'inclusions' => ['Full-day unlimited ride access to 40+ rides & attractions including Formula Rossa & Flying Aces', 'Free Yas Island shuttle bus access from Dubai (first-come basis with valid park ticket)'],
            'exclusions' => ['Quick Pass fast-track upgrade (available on WhatsApp)', 'Roof Walk / Zip Line & meals', 'Private Dubai transfer'],
            'details' => [
                'attraction_type' => 'Theme park',
                'overview' => "Experience the world's fastest roller coaster—Formula Rossa (240 km/h)—alongside Flying Aces, Ferrari racing simulators, and family adventure zones inside Yas Island's climate-controlled dome.",
                'opening_hours' => 'Daily 11:00 AM – 08:00 PM (Last entry 07:00 PM).',
                'ticket_types' => [
                    ['name' => 'Single-Day General Admission', 'adult' => 'AED 345', 'child' => 'AED 265'],
                    ['name' => 'Admission + Quick Pass (Fast-Track)', 'adult' => 'AED 540', 'child' => 'AED 460'],
                    ['name' => '2-Park Yas Island Combo (Add Warner Bros. or Yas Waterworld)', 'adult' => 'AED 475', 'child' => 'AED 475'],
                ],
                'date_time_note' => 'Select your visit date in the form; ask on WhatsApp for 2-Park combos or private Dubai hotel transfer.',
                'delivery_method' => 'Official PDF barcode ticket delivered via WhatsApp & Email.',
                'terms_rules' => 'Non-refundable after issue; date-change requests subject to park policy up to 48h prior.',
                'reviews' => [
                    ['author' => 'Vikram D. (India)', 'rating' => 5, 'comment' => 'Formula Rossa and Flying Aces are world-class! Booked the 2-park Yas combo on WhatsApp and saved big.'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 6, 'featured' => 0,
            'title' => 'Warner Bros. World Abu Dhabi',
            'slug' => 'warner-bros-world-abu-dhabi',
            'short_desc' => 'Indoor theme park entry',
            'duration' => 'Full day',
            'pickup_line' => 'Theme park, instant ticket',
            'has_pickup' => 0,
            'price_aed' => 345, 'child_price_aed' => 265,
            'age_policy' => 'Standard 1.1m+ | Junior under 1.1m | Under 3 yrs free',
            'inclusions' => ['Full-day indoor access to all 6 immersive lands (Gotham City, Metropolis, Cartoon Junction, Bedrock, Dynamite Gulch & Warner Bros. Plaza)', '29+ rides, 4D attractions & live character shows', 'Complimentary Yas Express shuttle eligibility'],
            'exclusions' => ['Flash Pass fast-track upgrade', 'Food, lockers & private transfers'],
            'details' => [
                'attraction_type' => 'Theme park',
                'overview' => "Step inside one of the world's largest fully air-conditioned indoor theme parks on Yas Island—meeting Batman, Superman, Bugs Bunny, Scooby-Doo, and The Flintstones across 29 immersive rides.",
                'opening_hours' => 'Daily 11:00 AM – 08:00 PM (Last entry 07:00 PM).',
                'ticket_types' => [
                    ['name' => 'Single-Day General Admission', 'adult' => 'AED 345', 'child' => 'AED 265'],
                    ['name' => 'Admission + Flash Pass (Fast-Track)', 'adult' => 'AED 540', 'child' => 'AED 460'],
                    ['name' => '2-Park Yas Island Combo Pass', 'adult' => 'AED 475', 'child' => 'AED 475'],
                ],
                'date_time_note' => 'Choose your preferred visit date; fully indoor & air-conditioned year-round.',
                'delivery_method' => 'Instant QR PDF e-ticket via WhatsApp & Email.',
                'terms_rules' => 'Valid for selected date; non-refundable once issued.',
                'reviews' => [
                    ['author' => 'Sarah J. (UK)', 'rating' => 5, 'comment' => '100% air-conditioned and magical theming in Gotham City and Cartoon Junction!'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 7, 'featured' => 0,
            'title' => 'IMG Worlds of Adventure',
            'slug' => 'img-worlds-of-adventure',
            'short_desc' => 'Indoor theme park entry',
            'duration' => 'Full day',
            'pickup_line' => 'Theme park, skip-the-line ticket',
            'has_pickup' => 0,
            'price_aed' => 325, 'child_price_aed' => 325,
            'age_policy' => 'Standard ticket over 1.05m height | Children under 1.05m enter free',
            'inclusions' => ['Full-day unlimited rides across Marvel, Cartoon Network, Lost Valley Dinosaur Adventure & IMG Boulevard', 'Indoor air-conditioned comfort in Dubai'],
            'exclusions' => ['Fast Track Ultimate pass upgrade', 'Meals, lockers & hotel transfers'],
            'details' => [
                'attraction_type' => 'Theme park',
                'overview' => "Dubai's mega indoor theme park brings Marvel superheroes, Cartoon Network favourites, life-sized animatronic dinosaurs, and the high-speed Velociraptor roller coaster together under one climate-controlled roof.",
                'opening_hours' => 'Sun–Thu 12:00 PM – 10:00 PM | Fri–Sat 12:00 PM – 11:00 PM (Last entry 1 hour before close).',
                'ticket_types' => [
                    ['name' => 'General Admission (All Rides)', 'adult' => 'AED 325', 'child' => 'AED 325'],
                    ['name' => 'Admission + Fast Track Pass', 'adult' => 'AED 520', 'child' => 'AED 520'],
                ],
                'date_time_note' => 'Choose your visit date in the form; we can also bundle private return hotel transfers on WhatsApp.',
                'delivery_method' => 'Instant PDF barcode ticket via WhatsApp & Email.',
                'terms_rules' => 'Valid on selected date; non-refundable after ticket generation.',
                'reviews' => [
                    ['author' => 'Noor H. (Qatar)', 'rating' => 5, 'comment' => 'Velociraptor and Spider-Man rides were awesome—no waiting at the ticket booth with our QR passes.'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 8, 'featured' => 0,
            'title' => 'Aquaventure Waterpark',
            'slug' => 'aquaventure-waterpark',
            'short_desc' => 'Day pass for Atlantis The Palm',
            'duration' => 'Full day',
            'pickup_line' => 'Water park, full-day pass',
            'has_pickup' => 0,
            'price_aed' => 330, 'child_price_aed' => 280,
            'age_policy' => 'Adults 1.2m+ height | Children 3–7 yrs & under 1.2m | Under 3 free',
            'inclusions' => ['Full-day access to Atlantis Aquaventure World (105+ slides, towers, wave rivers & Splashers Kids Island)', 'Access to 1 km private white-sand Aquaventure Beach'],
            'exclusions' => ['Lost Chambers Aquarium combo add-on (AED 50 extra)', 'Towels, lockers, AquaXpress fast-pass & meals'],
            'details' => [
                'attraction_type' => 'Water park',
                'overview' => 'Conquer record-breaking water slides—including Leap of Faith, Odyssey of Terror, and Trident Tower—float along rapid rivers, or relax on 1 km of private beach at Atlantis The Palm.',
                'opening_hours' => 'Daily 09:45 AM – 06:30 PM (Last entry 05:30 PM).',
                'ticket_types' => [
                    ['name' => 'Aquaventure Day Pass', 'adult' => 'AED 330', 'child' => 'AED 280'],
                    ['name' => 'Super Pass (Aquaventure + Lost Chambers Aquarium)', 'adult' => 'AED 380', 'child' => 'AED 330'],
                ],
                'date_time_note' => 'Pick your preferred date in the form; Super Pass upgrades confirmed seamlessly on WhatsApp.',
                'delivery_method' => 'Official QR PDF ticket sent via WhatsApp & Email.',
                'terms_rules' => 'Appropriate swimwear mandatory; non-refundable once issued.',
                'reviews' => [
                    ['author' => 'Bensons Travel (Australia)', 'rating' => 5, 'comment' => 'Easily the best waterpark on earth—105 slides and private Palm views all day!'],
                ],
            ],
        ],
        [
            'cat_id' => 5, 'pool' => 'tickets', 'sort' => 9, 'featured' => 0,
            'title' => 'Louvre Abu Dhabi',
            'slug' => 'louvre-abu-dhabi',
            'short_desc' => 'Museum entry tickets',
            'duration' => '2 to 3 hours',
            'pickup_line' => 'Museum, skip-the-line ticket',
            'has_pickup' => 0,
            'price_aed' => 65, 'child_price_aed' => 0,
            'age_policy' => 'Adults 18+ yrs: AED 65 | Under 18 yrs enter FREE (free child ticket still required)',
            'inclusions' => ['Official skip-the-line entry ticket to Louvre Abu Dhabi on Saadiyat Island', "Permanent art galleries, Children's Museum & iconic 'Rain of Light' dome plaza", 'Free multimedia guide via Louvre Abu Dhabi mobile app'],
            'exclusions' => ['Kayaking around the museum lagoon (optional add-on)', 'Transfers from Dubai or Abu Dhabi hotels (available on WhatsApp)'],
            'details' => [
                'attraction_type' => 'Museum',
                'overview' => "Explore masterpieces spanning human civilization beneath Jean Nouvel's floating geometric dome on Saadiyat Island, where sunlight filters down like rain over tranquil seawater plazas.",
                'opening_hours' => 'Tue–Thu 10:00 AM – 06:30 PM | Fri–Sun 10:00 AM – 08:30 PM (Closed Mondays; last entry 1 hr before close).',
                'ticket_types' => [
                    ['name' => 'Adult Admission (18+ yrs)', 'adult' => 'AED 65', 'child' => 'FREE'],
                    ['name' => 'Louvre Ticket + Private Return Transfer from Dubai', 'adult' => 'AED 295', 'child' => 'AED 190'],
                ],
                'date_time_note' => 'Note: Louvre Abu Dhabi is closed on Mondays. Choose Tue–Sun in the form below.',
                'delivery_method' => 'Official PDF QR ticket delivered via WhatsApp & Email.',
                'terms_rules' => 'Valid for selected date; non-refundable once issued.',
                'reviews' => [
                    ['author' => 'Isabelle R. (France)', 'rating' => 5, 'comment' => 'Breathtaking architecture and serene galleries—skip-the-line QR code worked instantly.'],
                ],
            ],
        ],
    ];

    $processedTours = [];
    foreach ($rawActivities as $i => $item) {
        $idx = $i + 1;
        $coverWebp = '/uploads/activities/' . $item['slug'] . '-cover.webp';
        $gallery = [$coverWebp];
        foreach ($pools[$item['pool']] as $g) {
            if ($g !== $coverWebp && !in_array($g, $gallery, true)) {
                $gallery[] = $g;
            }
            if (count($gallery) >= 7) {
                break;
            }
        }

        $det = $item['details'];
        $itinerary = $det['route_stops'] ?? [];
        $faqs = [
            [
                'q' => 'How do I confirm my booking for ' . $item['title'] . '?',
                'a' => "Click 'Book on WhatsApp' or submit the enquiry form below. Our UAE team replies within minutes on WhatsApp (+971 52 537 6838) with instant confirmation.",
            ],
            [
                'q' => 'Is hotel pick-up available?',
                'a' => $item['has_pickup']
                    ? 'Hotel pick-up is included on most safari and sightseeing tours, and can be added to any cruise, watersport, or attraction ticket on WhatsApp.'
                    : 'This experience starts at the venue/marina with instant ticket confirmation, and private hotel transfers can be added anytime on WhatsApp.',
            ],
            [
                'q' => 'What is the cancellation policy?',
                'a' => $det['cancellation'] ?? $det['weather_policy'] ?? $det['terms_rules'] ?? 'Contact us on WhatsApp at least 24 hours ahead for flexible assistance.',
            ],
        ];

        $processedTours[] = [
            'id' => $idx,
            'category_id' => $item['cat_id'],
            'title' => $item['title'],
            'slug' => $item['slug'],
            'badge' => $det['attraction_type'] ?? '',
            'short_desc' => $item['short_desc'],
            'long_desc' => $det['overview'] ?? $item['short_desc'],
            'duration' => $item['duration'],
            'pickup_line' => $item['pickup_line'],
            'has_pickup' => $item['has_pickup'],
            'price_aed' => (float)$item['price_aed'],
            'child_price_aed' => (float)$item['child_price_aed'],
            'original_price_aed' => null,
            'age_policy' => $item['age_policy'],
            'rating' => 4.9,
            'reviews_count' => 95 + (($idx * 7) % 140),
            'featured_image' => $coverWebp,
            'gallery_json' => json_encode($gallery, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'itinerary_json' => json_encode($itinerary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'inclusions_json' => json_encode($item['inclusions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'exclusions_json' => json_encode($item['exclusions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'details_json' => json_encode($det, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'pickup_details' => $det['timings_pickup'] ?? $det['boarding_point'] ?? $det['start_location'] ?? $det['opening_hours'] ?? '',
            'availability' => 'Daily',
            'faq_json' => json_encode($faqs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'is_featured' => $item['featured'],
            'is_combo' => 0,
            'sort_order' => $item['sort'],
            'is_active' => in_array($item['slug'], ['overnight-desert-safari', 'quad-bike-desert-safari', 'dhow-cruise-dinner-dubai-creek', 'family-dinner-cruise', 'corporate-and-group-cruise'], true) ? 0 : 1,
            'seo_title' => $item['title'] . ' | Book Online & WhatsApp — Anchor Point UAE',
            'seo_desc' => $item['short_desc'] . '. Duration: ' . $item['duration'] . '. From AED ' . (int)$item['price_aed'] . ' per person. Instant confirmation on WhatsApp.',
        ];
    }

    $defaultSettings = [
        'site_name'                   => 'Anchor Point Travel & Tourism',
        'show_prices'                 => '1',
        'whatsapp_number'             => '971525376838',
        'phone_display'               => '+971 52 537 6838',
        'company_email'               => 'info@anchorpointbiz.org',
        'office_address'              => 'Office #08, Sheikh Rashid Rd, near Financial Centre Metro, Al Jafiliya, Dubai, UAE',
        'office_hours'                => 'Daily: 9:00 AM – 6:00 PM (GST)',
        'trade_license_no'            => '',
        'tourism_license_no'          => '',
        'instagram_url'               => 'https://instagram.com/anchorpointuae',
        'facebook_url'                => 'https://facebook.com/anchorpointuae',
        'tiktok_url'                  => 'https://tiktok.com/@anchorpointuae',
        'youtube_url'                 => 'https://youtube.com/@anchorpointuae',
        'pdf_redesign_v2026_migrated' => '1',
    ];

    // Drop obsolete corporate tables cleanly
    foreach (['services', 'service_categories', 'blog_posts', 'leads', 'faqs', 'testimonials', 'content_blocks', 'media'] as $oldTbl) {
        try {
            $db->exec("DROP TABLE IF EXISTS $oldTbl");
        } catch (Exception $e) {}
    }

    $db->exec("DELETE FROM tours");
    $db->exec("DELETE FROM tour_categories");

    $catStmt = $db->prepare("
        INSERT INTO tour_categories (id, name, section_title, slug, description, cta_text, seo_keywords, icon, sort_order, seo_title, seo_desc, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    foreach ($categories as $c) {
        $catStmt->execute([
            $c['id'], $c['name'], $c['section_title'], $c['slug'], $c['description'],
            $c['cta_text'], $c['seo_keywords'], $c['icon'], $c['sort_order'], $c['seo_title'], $c['seo_desc']
        ]);
    }

    $tourStmt = $db->prepare("
        INSERT INTO tours (
            id, category_id, title, slug, badge, short_desc, long_desc, duration, pickup_line, has_pickup,
            price_aed, child_price_aed, original_price_aed, age_policy, rating, reviews_count,
            featured_image, gallery_json, itinerary_json, inclusions_json, exclusions_json,
            details_json, pickup_details, availability, faq_json, is_featured, is_combo, sort_order, is_active,
            seo_title, seo_desc
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($processedTours as $t) {
        $tourStmt->execute([
            $t['id'], $t['category_id'], $t['title'], $t['slug'], $t['badge'], $t['short_desc'], $t['long_desc'],
            $t['duration'], $t['pickup_line'], $t['has_pickup'], $t['price_aed'], $t['child_price_aed'],
            $t['original_price_aed'], $t['age_policy'], $t['rating'], $t['reviews_count'],
            $t['featured_image'], $t['gallery_json'], $t['itinerary_json'], $t['inclusions_json'],
            $t['exclusions_json'], $t['details_json'], $t['pickup_details'], $t['availability'],
            $t['faq_json'], $t['is_featured'], $t['is_combo'], $t['sort_order'], $t['is_active'],
            $t['seo_title'], $t['seo_desc']
        ]);
    }

    if ($isSqlite) {
        $setStmt = $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    } else {
        $setStmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    }
    foreach ($defaultSettings as $k => $v) {
        $setStmt->execute([$k, $v]);
    }

    // Also export updated MySQL database.sql when running locally on SQLite
    if ($isSqlite) {
        $sqlEsc = static function ($val): string {
            if ($val === null) return 'NULL';
            if (is_int($val) || is_float($val)) return (string)$val;
            $s = str_replace(['\\', "'", "\n", "\r"], ['\\\\', "\\'", '\\n', ''], (string)$val);
            return "'$s'";
        };

        $lines = [
            '-- Anchor Point Travel & Tourism — UAE Tourism MySQL Schema & Seed Data',
            'SET NAMES utf8mb4;',
            'SET FOREIGN_KEY_CHECKS = 0;',
            'DROP TABLE IF EXISTS `services`;',
            'DROP TABLE IF EXISTS `service_categories`;',
            'DROP TABLE IF EXISTS `blog_posts`;',
            'DROP TABLE IF EXISTS `leads`;',
            'DROP TABLE IF EXISTS `tours`;',
            'DROP TABLE IF EXISTS `tour_categories`;',
            '',
            'CREATE TABLE IF NOT EXISTS `settings` (`id` INT AUTO_INCREMENT PRIMARY KEY, `setting_key` VARCHAR(120) UNIQUE, `setting_value` TEXT, `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE IF NOT EXISTS `users` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(120), `email` VARCHAR(190) UNIQUE, `password_hash` VARCHAR(255), `role` VARCHAR(40) DEFAULT \'editor\', `is_active` TINYINT(1) DEFAULT 1, `last_login` DATETIME NULL, `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE IF NOT EXISTS `login_attempts` (`id` INT AUTO_INCREMENT PRIMARY KEY, `ip` VARCHAR(64), `attempted_at` DATETIME) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE IF NOT EXISTS `activity_log` (`id` INT AUTO_INCREMENT PRIMARY KEY, `user_id` INT NULL, `action` VARCHAR(190), `entity_type` VARCHAR(80), `entity_id` INT DEFAULT 0, `details` TEXT, `ip` VARCHAR(64), `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE `tour_categories` (`id` INT AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(150) NOT NULL, `section_title` VARCHAR(190), `slug` VARCHAR(150) UNIQUE NOT NULL, `description` TEXT, `cta_text` TEXT, `seo_keywords` TEXT, `icon` VARCHAR(60) DEFAULT \'compass\', `image` VARCHAR(255), `sort_order` INT DEFAULT 0, `seo_title` VARCHAR(255), `seo_desc` TEXT, `is_active` TINYINT(1) DEFAULT 1, `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE `tours` (`id` INT AUTO_INCREMENT PRIMARY KEY, `category_id` INT NOT NULL, `title` VARCHAR(190) NOT NULL, `slug` VARCHAR(190) UNIQUE NOT NULL, `badge` VARCHAR(80), `short_desc` TEXT, `long_desc` LONGTEXT, `duration` VARCHAR(120), `pickup_line` VARCHAR(190), `has_pickup` TINYINT(1) DEFAULT 1, `price_aed` DECIMAL(10,2) NOT NULL DEFAULT 0, `child_price_aed` DECIMAL(10,2) DEFAULT 0, `original_price_aed` DECIMAL(10,2) NULL, `age_policy` VARCHAR(255), `rating` DECIMAL(3,1) DEFAULT 4.9, `reviews_count` INT DEFAULT 120, `featured_image` VARCHAR(255), `gallery_json` LONGTEXT, `itinerary_json` LONGTEXT, `inclusions_json` LONGTEXT, `exclusions_json` LONGTEXT, `details_json` LONGTEXT, `pickup_details` TEXT, `availability` VARCHAR(120) DEFAULT \'Daily\', `faq_json` LONGTEXT, `is_featured` TINYINT(1) DEFAULT 0, `is_combo` TINYINT(1) DEFAULT 0, `sort_order` INT DEFAULT 0, `is_active` TINYINT(1) DEFAULT 1, `seo_title` VARCHAR(255), `seo_desc` TEXT, `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP, `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            'CREATE TABLE IF NOT EXISTS `tour_bookings` (`id` INT AUTO_INCREMENT PRIMARY KEY, `booking_ref` VARCHAR(64), `tour_id` INT NULL, `category_name` VARCHAR(150), `tour_title` VARCHAR(255), `customer_name` VARCHAR(190) NOT NULL, `customer_email` VARCHAR(190) NOT NULL, `country_code` VARCHAR(16) DEFAULT \'+971\', `customer_phone` VARCHAR(64) NOT NULL, `booking_date` VARCHAR(40) NOT NULL, `preferred_time` VARCHAR(120), `adults_count` INT DEFAULT 1, `children_count` INT DEFAULT 0, `children_ages` VARCHAR(190), `guests_count` INT DEFAULT 1, `pickup_location` VARCHAR(255), `nationality` VARCHAR(120), `special_requests` TEXT, `consent_given` TINYINT(1) DEFAULT 1, `source_page` VARCHAR(255), `utm_source` VARCHAR(120), `utm_medium` VARCHAR(120), `utm_campaign` VARCHAR(120), `utm_term` VARCHAR(120), `utm_content` VARCHAR(120), `total_price_aed` DECIMAL(10,2) DEFAULT 0, `status` VARCHAR(40) DEFAULT \'New\', `email_sent` TINYINT(1) DEFAULT 0, `ip` VARCHAR(64), `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;',
            '',
        ];
        foreach ($categories as $c) {
            $vals = implode(', ', [
                $sqlEsc($c['id']), $sqlEsc($c['name']), $sqlEsc($c['section_title']), $sqlEsc($c['slug']),
                $sqlEsc($c['description']), $sqlEsc($c['cta_text']), $sqlEsc($c['seo_keywords']),
                $sqlEsc($c['icon']), $sqlEsc($c['sort_order']), $sqlEsc($c['seo_title']), $sqlEsc($c['seo_desc']), '1'
            ]);
            $lines[] = "INSERT INTO `tour_categories` (`id`,`name`,`section_title`,`slug`,`description`,`cta_text`,`seo_keywords`,`icon`,`sort_order`,`seo_title`,`seo_desc`,`is_active`) VALUES ($vals);";
        }
        foreach ($processedTours as $t) {
            $vals = implode(', ', [
                $sqlEsc($t['id']), $sqlEsc($t['category_id']), $sqlEsc($t['title']), $sqlEsc($t['slug']),
                $sqlEsc($t['badge']), $sqlEsc($t['short_desc']), $sqlEsc($t['long_desc']), $sqlEsc($t['duration']),
                $sqlEsc($t['pickup_line']), $sqlEsc($t['has_pickup']), $sqlEsc($t['price_aed']),
                $sqlEsc($t['child_price_aed']), $sqlEsc($t['original_price_aed']), $sqlEsc($t['age_policy']),
                $sqlEsc($t['rating']), $sqlEsc($t['reviews_count']), $sqlEsc($t['featured_image']),
                $sqlEsc($t['gallery_json']), $sqlEsc($t['itinerary_json']), $sqlEsc($t['inclusions_json']),
                $sqlEsc($t['exclusions_json']), $sqlEsc($t['details_json']), $sqlEsc($t['pickup_details']),
                $sqlEsc($t['availability']), $sqlEsc($t['faq_json']), $sqlEsc($t['is_featured']),
                $sqlEsc($t['is_combo']), $sqlEsc($t['sort_order']), $sqlEsc($t['is_active']),
                $sqlEsc($t['seo_title']), $sqlEsc($t['seo_desc'])
            ]);
            $lines[] = "INSERT INTO `tours` (`id`,`category_id`,`title`,`slug`,`badge`,`short_desc`,`long_desc`,`duration`,`pickup_line`,`has_pickup`,`price_aed`,`child_price_aed`,`original_price_aed`,`age_policy`,`rating`,`reviews_count`,`featured_image`,`gallery_json`,`itinerary_json`,`inclusions_json`,`exclusions_json`,`details_json`,`pickup_details`,`availability`,`faq_json`,`is_featured`,`is_combo`,`sort_order`,`is_active`,`seo_title`,`seo_desc`) VALUES ($vals);";
        }
        foreach ($defaultSettings as $k => $v) {
            $lines[] = 'INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES (' . $sqlEsc($k) . ',' . $sqlEsc($v) . ') ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);';
        }
        $lines[] = 'SET FOREIGN_KEY_CHECKS = 1;';
        @file_put_contents(dirname(__DIR__) . '/database.sql', implode("\n", $lines) . "\n");
    }
}
