<?php
/**
 * Attraction Tickets v2 — adds 27 more UAE attractions & combo passes to the "Tickets & Attractions" category.
 * Organised in 8 ticket groups (see getTicketGroups() in functions.php). Each attraction gets its own page at
 * /tickets-attractions/<slug>. Photos live in /uploads/attractions/<slug>.jpg (graceful fallback until uploaded).
 *
 * Idempotent: guarded by the `attractions_v2` setting and inserts only slugs that do not exist yet.
 * Prices are indicative "from" rates in AED and can be edited any time in Admin → Activities & Prices.
 */
declare(strict_types=1);

function seedAttractionsV2(PDO $db): void {
    try {
        $chk = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'attractions_v2' LIMIT 1");
        $chk->execute();
        $r = $chk->fetch();
        if ($r && (string)$r['setting_value'] === '1') return;

        $cat = $db->prepare("SELECT id FROM tour_categories WHERE slug = 'tickets-attractions' LIMIT 1");
        $cat->execute();
        $catRow = $cat->fetch();
        if (!$catRow) return;
        $catId = (int)$catRow['id'];
    } catch (Throwable $e) {
        return;
    }

    // [slug, title, group, badge, adult AED, child AED, duration, short, overview, opening hours, included[], notes, extra ticket rows[]]
    $items = [
        // ── Landmarks & Observation Decks ──
        ['the-view-at-the-palm', 'The View at The Palm', 'landmarks', 'Observation Deck', 119, 99, '1 to 1.5 hours',
         'Level 52 observation deck over Palm Jumeirah',
         'Rise to the 52nd floor of Palm Tower for sweeping 360° views of Palm Jumeirah, the Arabian Gulf and the Dubai skyline from an indoor lounge and open-air terrace.',
         'Daily, typically 9:00 AM – 10:00 PM (last entry about 1 hour before closing).',
         ['Timed-entry e-ticket to The View at The Palm', 'Indoor viewing gallery and outdoor terrace', 'Interactive Palm Jumeirah displays'],
         'Sunset slots sell out early, so book ahead for the best light.', []],
        ['sky-views-dubai', 'Sky Views Observatory', 'landmarks', 'Observation Deck', 69, 59, '1 to 1.5 hours',
         'Glass sky bridge and observation deck at Address Sky View',
         'Step onto the glass-floored sky bridge linking the twin Address Sky View towers and enjoy panoramic views of Downtown Dubai and Burj Khalifa from 219 metres up.',
         'Daily, typically 10:00 AM – 12:00 AM (hours vary by season).',
         ['E-ticket to Sky Views Observatory', 'Indoor and outdoor viewing decks', 'Glass sky bridge walk'],
         'Optional thrill add-ons such as the glass slide are available; ask on WhatsApp.',
         [['Observatory + Glass Slide', 'Price on request', 'Price on request']]],

        // ── Theme Parks ──
        ['motiongate-dubai', 'Motiongate Dubai', 'theme-parks', 'Theme Park', 295, 270, 'Full day',
         'Hollywood-themed park with DreamWorks, Lionsgate and Sony Pictures zones',
         'Ride roller coasters and meet your favourite movie characters across Hollywood-inspired zones at Dubai Parks and Resorts, one of the largest movie-themed destinations in the Middle East.',
         'Daily, typically 11:00 AM – 7:00 PM (hours vary by day and season).',
         ['One-day entry to Motiongate Dubai', 'Access to rides, shows and live entertainment', 'Instant e-ticket delivery'],
         'Height restrictions apply to some rides. Wear comfortable shoes and bring sun protection.', []],
        ['legoland-dubai', 'LEGOLAND Dubai', 'theme-parks', 'Theme Park', 295, 270, 'Full day',
         'LEGO-themed rides, Miniland and kids zones',
         'Designed for families with children aged 2–12, LEGOLAND Dubai offers over 40 rides, shows and interactive attractions across six themed lands, including Miniland with famous Dubai landmarks built from LEGO bricks.',
         'Daily, typically 10:00 AM – 6:00 PM (hours vary by season).',
         ['One-day entry to LEGOLAND Dubai', 'Access to rides, shows and Miniland', 'Instant e-ticket delivery'],
         'Best suited to families with children aged 2–12. An adult must accompany young children.', []],
        ['bollywood-parks-dubai', 'Bollywood Parks Dubai', 'theme-parks', 'Theme Park', 295, 270, 'Full day',
         'Bollywood-inspired rides, live shows and dance performances',
         'Experience the glamour of Indian cinema with Bollywood-themed rides, live stage shows, colourful streets and dance performances inspired by iconic film scenes.',
         'Daily, typically 4:00 PM – 10:00 PM (hours vary by season).',
         ['One-day entry to Bollywood Parks Dubai', 'Access to rides and live shows', 'Instant e-ticket delivery'],
         'Evening-focused park. Check show timings on arrival.', []],
        ['seaworld-abu-dhabi', 'SeaWorld Abu Dhabi', 'theme-parks', 'Theme Park', 355, 330, 'Full day',
         'Marine-life theme park on Yas Island',
         'Explore one of the world\'s largest indoor marine-life parks on Yas Island with immersive ocean realms, rides and animal encounters focused on marine conservation.',
         'Daily, typically 10:00 AM – 7:00 PM (hours vary by season).',
         ['One-day entry to SeaWorld Abu Dhabi', 'Access to marine habitats and rides', 'Instant e-ticket delivery'],
         'Combine with Yas Island parks using a multi-park pass for better value.',
         [['SeaWorld Abu Dhabi + Return Transfer from Dubai', 'Price on request', 'Price on request']]],

        // ── Water Parks ──
        ['wild-wadi-waterpark', 'Wild Wadi Waterpark', 'water-parks', 'Water Park', 299, 249, 'Half to full day',
         'Slides, wave pools and lazy river beside Burj Al Arab',
         'Beat the heat at this Arabian-themed water park next to Burj Al Arab, with record-setting slides, wave pools, a lazy river and gentle areas for young children.',
         'Daily, typically 10:00 AM – 6:00 PM (hours vary by season).',
         ['One-day entry to Wild Wadi Waterpark', 'Access to slides, wave pools and lazy river', 'Instant e-ticket delivery'],
         'Bring swimwear, towel and sunscreen. Height limits apply to some slides.', []],
        ['yas-waterworld', 'Yas Waterworld Abu Dhabi', 'water-parks', 'Water Park', 295, 270, 'Half to full day',
         'Emirati pearl-diving themed water park with 40+ rides',
         'Ride more than 40 slides and attractions at Abu Dhabi\'s Emirati-themed water park on Yas Island, with family areas, thrill slides and the story of the legendary pearl Dana.',
         'Daily, typically 10:00 AM – 6:00 PM (hours vary by season).',
         ['One-day entry to Yas Waterworld', 'Access to all slides and attractions', 'Instant e-ticket delivery'],
         'Bring swimwear and sunscreen. Lockers are available on site.',
         [['Yas Waterworld + Return Transfer from Dubai', 'Price on request', 'Price on request']]],
        ['legoland-water-park', 'LEGOLAND Water Park Dubai', 'water-parks', 'Water Park', 255, 235, 'Half to full day',
         'LEGO-themed slides, wave pool and lazy river for families',
         'A family water park with LEGO-themed slides, a wave pool, a lazy river and a splash zone for toddlers, located beside LEGOLAND Dubai.',
         'Daily, typically 10:00 AM – 6:00 PM (hours vary by season).',
         ['One-day entry to LEGOLAND Water Park', 'Access to slides, wave pool and lazy river', 'Instant e-ticket delivery'],
         'Combine with LEGOLAND Dubai in a bundle ticket. Ask on WhatsApp.', []],

        // ── Aquariums & Wildlife ──
        ['lost-chambers-aquarium', 'The Lost Chambers Aquarium', 'aquariums-wildlife', 'Aquarium', 179, 145, '1.5 to 2 hours',
         'Atlantis The Palm aquarium with ancient-ruins theme',
         'Wander through the mythical ruins of Atlantis at one of the region\'s largest aquariums, home to thousands of marine animals across tunnels and chambers at Atlantis The Palm.',
         'Daily, typically 10:00 AM – 11:00 PM (hours vary by season).',
         ['Entry to The Lost Chambers Aquarium', 'Underwater tunnels and viewing chambers', 'Instant e-ticket delivery'],
         'Optional animal encounters and dives are available; ask on WhatsApp.', []],
        ['dubai-safari-park', 'Dubai Safari Park', 'aquariums-wildlife', 'Wildlife Park', 50, 30, '3 to 4 hours',
         'Open-air safari with giraffes, lions and 3,000+ animals',
         'Visit one of the largest wildlife parks in the region with themed zones from Africa to Asia, animal shows and a safari-style journey through large natural-style habitats.',
         'Seasonal, usually October to May, typically 9:30 AM – 5:30 PM.',
         ['Entry to Dubai Safari Park', 'Access to themed zones and animal shows', 'Instant e-ticket delivery'],
         'The park is closed during the hottest summer months. Check seasonal dates on WhatsApp.', []],
        ['green-planet-dubai', 'The Green Planet Dubai', 'aquariums-wildlife', 'Indoor Rainforest', 120, 100, '1.5 to 2 hours',
         'Indoor tropical rainforest biodome at City Walk',
         'Step into a four-storey tropical rainforest in the heart of the city with over 3,000 plants and animals including sloths, birds and reptiles, all under a climate-controlled biodome.',
         'Daily, typically 10:00 AM – 6:00 PM.',
         ['Entry to The Green Planet Dubai', 'Four-level rainforest experience', 'Instant e-ticket delivery'],
         'Great for families and rainy or hot days.', []],

        // ── Museums & Culture ──
        ['etihad-museum', 'Etihad Museum', 'museums-culture', 'Museum', 25, 0, '1.5 to 2 hours',
         'Museum telling the story of the founding of the UAE',
         'Discover the story of the union of the seven emirates at Dubai\'s Etihad Museum through immersive galleries, historic documents and the Union House where the UAE Constitution was signed.',
         'Daily, typically 10:00 AM – 8:00 PM (hours vary by day).',
         ['Entry to Etihad Museum', 'Permanent galleries and Union House', 'Instant e-ticket delivery'],
         'Free entry for children under a certain age; confirm on WhatsApp.', []],
        ['teamlab-phenomena-abu-dhabi', 'teamLab Phenomena Abu Dhabi', 'museums-culture', 'Immersive Art', 100, 75, '1.5 to 2 hours',
         'Immersive digital art museum in Abu Dhabi',
         'Walk, float and play through large-scale immersive digital artworks that respond to your movement and touch at teamLab Phenomena in Abu Dhabi.',
         'Daily, typically 10:00 AM – 7:00 PM (hours vary by day).',
         ['Entry to teamLab Phenomena Abu Dhabi', 'Immersive art spaces', 'Instant e-ticket delivery'],
         'Wear comfortable clothing; some spaces are best without shoes.',
         [['teamLab + Return Transfer from Dubai', 'Price on request', 'Price on request']]],
        ['qasr-al-watan', 'Qasr Al Watan Presidential Palace', 'museums-culture', 'Palace Visit', 65, 0, '2 to 3 hours',
         'Abu Dhabi presidential palace with Great Hall and gardens',
         'Visit the working presidential palace of the UAE in Abu Dhabi with its Great Hall, grand domes, intricate Arabesque architecture and landscaped gardens.',
         'Daily, typically 10:00 AM – 5:00 PM (closed on selected days).',
         ['Entry to Qasr Al Watan', 'Great Hall and Presidential Gift Exhibition', 'Instant e-ticket delivery'],
         'Modest dress is required. Photography is restricted in some areas.',
         [['Qasr Al Watan + Return Transfer from Dubai', 'Price on request', 'Price on request']]],

        // ── Gardens & Events ──
        ['dubai-miracle-garden', 'Dubai Miracle Garden', 'gardens-events', 'Garden', 100, 85, '2 to 3 hours',
         'World\'s largest natural flower garden',
         'Walk among millions of blooming flowers shaped into giant arches, hearts and sculptures at one of the world\'s largest natural flower gardens, a favourite for family photos.',
         'Seasonal, usually October to May, typically 9:00 AM – 9:00 PM.',
         ['Entry to Dubai Miracle Garden', 'Floral displays and photo spots', 'Instant e-ticket delivery'],
         'The garden is closed during summer. Visit between October and May.', []],
        ['global-village-dubai', 'Global Village Dubai', 'gardens-events', 'Seasonal Event', 25, 0, '3 to 5 hours',
         'Multicultural shopping, food and entertainment park',
         'Shop for handicrafts, taste international street food, watch live shows and enjoy rides at this seasonal multicultural festival park with pavilions from dozens of countries.',
         'Seasonal, usually from October to April, typically 4:00 PM – 12:00 AM.',
         ['Entry ticket to Global Village', 'Access to pavilions and shows', 'Instant e-ticket delivery'],
         'Open seasonally. Rides and special shows are charged separately.', []],

        // ── Indoor Fun & Adventure ──
        ['ski-dubai', 'Ski Dubai Snow Park', 'indoor-adventure', 'Indoor Snow', 195, 195, '2 to 3 hours',
         'Indoor ski slope and snow park at Mall of the Emirates',
         'Ski, snowboard, sled and play in real snow inside Mall of the Emirates, with a snow park, ice cave and encounters with penguins available in selected packages.',
         'Daily, typically 10:00 AM – 11:00 PM.',
         ['Snow Park admission', 'Jackets, boots and equipment hire', 'Instant e-ticket delivery'],
         'Ski slope passes and penguin encounters are separate options; ask on WhatsApp.',
         [['Ski Slope Pass', 'Price on request', 'Price on request']]],
        ['ifly-dubai', 'iFLY Dubai Indoor Skydiving', 'indoor-adventure', 'Indoor Skydiving', 299, 299, '1 hour',
         'Indoor skydiving in a vertical wind tunnel',
         'Experience the thrill of freefall without a plane at iFLY Dubai, with a safety briefing and instructor-led flights in a vertical wind tunnel suitable for first-timers and all ages.',
         'Daily, typically 10:00 AM – 10:00 PM (by appointment).',
         ['Safety briefing and instructor-led flights', 'Flight suit, helmet and goggles', 'Instant e-ticket delivery'],
         'Not recommended for guests with back or shoulder issues. Minimum age applies.', []],
        ['skydive-dubai', 'Skydive Dubai Tandem Jump', 'indoor-adventure', 'Skydiving', 2199, 0, 'Half day',
         'Tandem skydive over Palm Jumeirah',
         'Jump from 13,000 feet and freefall above Palm Jumeirah and the Dubai coast with an experienced instructor in one of the world\'s most scenic skydives.',
         'Daily, subject to weather conditions (morning and afternoon flights).',
         ['Tandem skydive with certified instructor', 'Equipment and safety briefing', 'Flight certificate'],
         'Weight and health restrictions apply. Photo and video packages are optional extras.', []],
        ['kidzania-dubai', 'KidZania Dubai', 'indoor-adventure', 'Kids Edutainment', 185, 185, '3 to 4 hours',
         'Child-sized city where kids try real jobs',
         'Children role-play real jobs from firefighter to pilot in a child-sized city at The Dubai Mall, earning KidZos and learning teamwork and money skills.',
         'Daily, typically 10:00 AM – 10:00 PM (sessions vary).',
         ['Entry to KidZania Dubai', 'Access to all career activities', 'Instant e-ticket delivery'],
         'For children aged 4–14. Accompanying adults can enter at the adult rate.', []],
        ['play-dxb', 'Play DXB', 'indoor-adventure', 'Indoor Entertainment', 60, 60, '2 to 4 hours',
         'Indoor entertainment hub with VR, go-karts and arcade games',
         'A large indoor playground at City Walk with virtual reality, go-karting, escape rooms and arcade games for friends and families.',
         'Daily, typically 10:00 AM – 12:00 AM.',
         ['Entry to Play DXB', 'Selected game and experience credit', 'Instant e-ticket delivery'],
         'Individual experiences can be pre-booked; ask on WhatsApp.', []],
        ['snow-abu-dhabi', 'Snow Abu Dhabi', 'indoor-adventure', 'Indoor Snow', 195, 195, '2 to 3 hours',
         'Largest indoor snow park at Reem Mall',
         'Enjoy sledding, snowball play and winter fun at one of the world\'s largest indoor snow parks at Reem Mall in Abu Dhabi, perfect for families all year round.',
         'Daily, typically 10:00 AM – 10:00 PM.',
         ['Snow park admission', 'Jackets and gloves', 'Instant e-ticket delivery'],
         'Closed-toe shoes are required. Socks are recommended.', []],
        ['clymb-abu-dhabi', 'CLYMB Abu Dhabi', 'indoor-adventure', 'Indoor Adventure', 199, 199, '2 to 3 hours',
         'World\'s tallest indoor climbing wall and skydive tunnel',
         'Climb the tallest indoor climbing wall and try skydive-style flight in the world\'s largest indoor skydiving chamber at CLYMB Yas Island.',
         'Daily, typically 10:00 AM – 10:00 PM.',
         ['Entry to CLYMB Abu Dhabi', 'Safety briefing and equipment', 'Instant e-ticket delivery'],
         'Closed-toe shoes required. Minimum age and height rules apply.', []],

        // ── Combo Tickets & Passes ──
        ['burj-khalifa-and-dubai-aquarium-combo', 'Burj Khalifa & Dubai Aquarium Combo', 'combo-passes', 'Combo Ticket', 285, 235, '4 hours',
         'Burj Khalifa observation deck plus Dubai Aquarium & Underwater Zoo',
         'See Dubai from the top of the world, then walk through the giant underwater tunnel at Dubai Aquarium & Underwater Zoo in The Dubai Mall in one discounted combo ticket.',
         'Daily, subject to selected time slots.',
         ['Burj Khalifa At the Top ticket (Level 124/125)', 'Dubai Aquarium & Underwater Zoo entry', 'Instant e-ticket delivery'],
         'Time slots are confirmed on WhatsApp.', []],
        ['yas-island-multi-park-pass', 'Yas Island Multi-Park Pass', 'combo-passes', 'Multi-Park Pass', 549, 499, '2 to 3 days',
         'Choose from Ferrari World, Warner Bros., SeaWorld and Yas Waterworld',
         'Save with a multi-park pass covering your pick of Yas Island parks such as Ferrari World Abu Dhabi, Warner Bros. World, SeaWorld Abu Dhabi and Yas Waterworld.',
         'Pass validity and park hours vary; confirmed on WhatsApp.',
         ['Entry to selected Yas Island parks', 'Flexible dates within pass validity', 'Instant e-ticket delivery'],
         'Pass options (2, 3 or 4 parks) vary; ask on WhatsApp for the best combination.', []],
        ['dubai-parks-and-resorts-multi-park-pass', 'Dubai Parks & Resorts Multi-Park Pass', 'combo-passes', 'Multi-Park Pass', 399, 365, '2 to 3 days',
         'Motiongate, Bollywood Parks and LEGOLAND in one pass',
         'Choose a multi-park pass to visit Motiongate Dubai, Bollywood Parks Dubai and LEGOLAND Dubai across several days at a lower per-park price.',
         'Pass validity and park hours vary; confirmed on WhatsApp.',
         ['Entry to selected Dubai Parks & Resorts parks', 'Flexible dates within pass validity', 'Instant e-ticket delivery'],
         'Pass options (2 or 3 parks) vary; ask on WhatsApp for the best combination.', []],
    ];

    // Next sort order in the category
    $maxSort = 0;
    try {
        $m = $db->prepare('SELECT MAX(sort_order) FROM tours WHERE category_id = ?');
        $m->execute([$catId]);
        $maxSort = (int)$m->fetchColumn();
    } catch (Throwable $e) {}

    $selTour = $db->prepare('SELECT id FROM tours WHERE slug = ? LIMIT 1');
    $insTour = $db->prepare('
        INSERT INTO tours (category_id, title, slug, badge, short_desc, long_desc, duration, pickup_line, has_pickup,
            price_aed, child_price_aed, original_price_aed, age_policy, rating, reviews_count, featured_image, gallery_json,
            itinerary_json, inclusions_json, exclusions_json, details_json, pickup_details, availability, faq_json,
            is_featured, is_combo, sort_order, is_active, seo_title, seo_desc)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, NULL, ?, 4.8, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, 1, ?, ?)
    ');
    $json = static fn($v): string => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    foreach ($items as $i => [$slug, $title, $group, $badge, $price, $child, $duration, $short, $overview, $hours, $included, $notes, $extra]) {
        $selTour->execute([$slug]);
        if ($selTour->fetch()) continue;

        $cover = '/uploads/attractions/' . $slug . '.jpg';
        $tickets = [['name' => 'Standard Admission', 'adult' => 'AED ' . $price, 'child' => $child > 0 ? 'AED ' . $child : 'Free / see age policy']];
        foreach ($extra as $row) {
            $tickets[] = ['name' => $row[0], 'adult' => $row[1], 'child' => $row[2]];
        }
        $isCombo = $group === 'combo-passes' ? 1 : 0;
        $details = [
            'attraction_type'   => $badge,
            'ticket_group'      => $group,
            'overview'          => $overview,
            'opening_hours'     => $hours,
            'ticket_types'      => $tickets,
            'date_time_note'    => 'Choose your preferred date in the booking form. Time slots are confirmed on WhatsApp.',
            'delivery_method'   => 'Official e-ticket (QR code) delivered via WhatsApp and Email after confirmation.',
            'important_notes'   => $notes,
            'terms_rules'       => 'Indicative "from" prices; the final rate is confirmed on WhatsApp before payment. Tickets are date-specific, and date changes or refunds follow the venue policy.',
            'package_discounts' => 'Ask on WhatsApp for combo tickets, group pricing and private hotel transfers.',
        ];
        $faqs = [
            ['q' => 'How do I book ' . $title . '?', 'a' => 'Tap "Book on WhatsApp" or send the enquiry form on this page. Our UAE team confirms availability and sends your e-ticket quickly on WhatsApp (+971 52 537 6838).'],
            ['q' => 'Is hotel transfer available?', 'a' => 'Yes. Private hotel transfers can be added to any attraction ticket on request. Message us on WhatsApp for pricing.'],
            ['q' => 'Can I change the date after booking?', 'a' => 'Date changes depend on the venue policy. Contact us at least 24 hours before your visit and we will help wherever possible.'],
        ];
        $insTour->execute([
            $catId, $title, $slug, $badge, $short, $overview, $duration, 'Venue entry, e-ticket',
            (float)$price, (float)$child, $child > 0 ? 'Adults | Children: see ticket rates | Infants usually free' : 'Check venue age policy; infants usually free',
            40 + (abs(crc32($slug)) % 80), $cover, $json([$cover]),
            '[]', $json($included), $json(['Hotel transfers (available on request)', 'Food, drinks and personal expenses']),
            $json($details), $hours, 'Daily', $json($faqs),
            $isCombo, $maxSort + $i + 1,
            $title . ' Tickets Dubai & UAE | Book Online — Anchor Point',
            $short . '. From AED ' . (int)$price . ' per person. Instant e-ticket confirmation on WhatsApp.',
        ]);
    }

    // Richer category copy for the tickets landing page
    try {
        $db->prepare('UPDATE tour_categories SET description = ?, cta_text = ?, seo_keywords = ?, seo_title = ?, seo_desc = ? WHERE slug = ?')->execute([
            "Skip the queue with official e-tickets for the UAE's top attractions: observation decks, theme parks, water parks, aquariums, museums, gardens, indoor adventure and money-saving combo passes, all with instant WhatsApp confirmation.",
            'Want combo tickets or hotel transfers included? Ask us on WhatsApp.',
            'Dubai attraction tickets, Burj Khalifa tickets, theme park tickets Dubai, Ferrari World tickets, Aquaventure tickets, Abu Dhabi attraction tickets, multi-park pass UAE',
            'UAE Attraction Tickets | Theme Parks, Burj Khalifa, Water Parks & More',
            'Book Dubai and Abu Dhabi attraction tickets online: Burj Khalifa, theme parks, water parks, aquariums, museums, Global Village and combo passes with instant e-ticket confirmation.',
            'tickets-attractions',
        ]);
    } catch (Throwable $e) {}

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('attractions_v2', '1')")->execute();
    } else {
        $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('attractions_v2', '1') ON DUPLICATE KEY UPDATE setting_value = '1'")->execute();
    }
}
