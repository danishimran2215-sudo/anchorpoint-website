<?php
/**
 * Anchor Point Business Setup & SEO Blog Seeder
 * Populates corporate services, service categories, and 6 high-ranking SEO blog articles.
 */
declare(strict_types=1);

function seedBusinessAndBlog(PDO $db): void {
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $isSqlite = ($driver === 'sqlite');

    if ($isSqlite) {
        $db->exec("
            CREATE TABLE IF NOT EXISTS service_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1
            );
            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                badge TEXT,
                short_desc TEXT,
                content TEXT,
                featured_image TEXT,
                price_starting_aed REAL DEFAULT 0,
                timeline TEXT,
                deliverables_json TEXT,
                requirements_json TEXT,
                faq_json TEXT,
                sort_order INTEGER DEFAULT 0,
                is_featured INTEGER DEFAULT 1,
                is_active INTEGER DEFAULT 1,
                seo_title TEXT,
                seo_desc TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                category TEXT DEFAULT 'Business Setup',
                excerpt TEXT,
                content TEXT,
                featured_image TEXT,
                author TEXT DEFAULT 'Anchor Point Corporate Desk',
                read_time TEXT DEFAULT '5 min read',
                views INTEGER DEFAULT 350,
                seo_title TEXT,
                seo_desc TEXT,
                is_published INTEGER DEFAULT 1,
                published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS service_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(150) UNIQUE NOT NULL,
                description TEXT,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                title VARCHAR(190) NOT NULL,
                slug VARCHAR(190) UNIQUE NOT NULL,
                badge VARCHAR(100),
                short_desc TEXT,
                content LONGTEXT,
                featured_image VARCHAR(255),
                price_starting_aed DECIMAL(10,2) DEFAULT 0,
                timeline VARCHAR(100),
                deliverables_json LONGTEXT,
                requirements_json LONGTEXT,
                faq_json LONGTEXT,
                sort_order INT DEFAULT 0,
                is_featured TINYINT(1) DEFAULT 1,
                is_active TINYINT(1) DEFAULT 1,
                seo_title VARCHAR(255),
                seo_desc TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS blog_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(190) UNIQUE NOT NULL,
                category VARCHAR(100) DEFAULT 'Business Setup',
                excerpt TEXT,
                content LONGTEXT,
                featured_image VARCHAR(255),
                author VARCHAR(120) DEFAULT 'Anchor Point Corporate Desk',
                read_time VARCHAR(50) DEFAULT '5 min read',
                views INT DEFAULT 350,
                seo_title VARCHAR(255),
                seo_desc TEXT,
                is_published TINYINT(1) DEFAULT 1,
                published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    // Check if services are already populated
    $servCount = (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($servCount === 0) {
        $categories = [
            ['id' => 1, 'name' => 'Company Formation & Licensing', 'slug' => 'company-formation', 'description' => 'Mainland, Free Zone, and Offshore company registration across Dubai and all UAE Emirates.'],
            ['id' => 2, 'name' => 'Visa & Residency Services', 'slug' => 'visa-services', 'description' => 'Golden Visas, Investor Visas, Employment Visas, and complete Family Sponsorship.'],
            ['id' => 3, 'name' => 'PRO & Government Approvals', 'slug' => 'pro-services', 'description' => 'Document clearance, ministry approvals, establishment card registration, and municipality compliance.'],
            ['id' => 4, 'name' => 'Corporate Banking & Legal', 'slug' => 'banking-legal', 'description' => 'Assistance with UAE top-tier business bank account opening, VAT, and corporate compliance.'],
        ];

        $insCat = $db->prepare("INSERT INTO service_categories (id, name, slug, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        foreach ($categories as $i => $cat) {
            $insCat->execute([$cat['id'], $cat['name'], $cat['slug'], $cat['description'], $i + 1]);
        }

        $servicesList = [
            [
                'cat_id' => 1,
                'title' => 'Dubai Mainland Company Formation',
                'slug' => 'new-trade-license',
                'badge' => '100% Foreign Ownership',
                'short_desc' => 'Set up your commercial, professional, or industrial company in Dubai Mainland with 100% foreign ownership and zero corporate red tape.',
                'content' => '<p>Establishing a mainland company in Dubai under the Department of Economy and Tourism (DET) offers the freedom to trade directly within the UAE local market and globally without restrictions. With recent commercial law reforms, 100% foreign ownership is available across over 1,000 commercial and industrial activities.</p><h3>Why Choose Dubai Mainland?</h3><ul><li><strong>Direct UAE Market Access:</strong> Trade anywhere in the UAE and take on government and semi-government contracts.</li><li><strong>No Minimum Share Capital:</strong> Flexible capital structure for most business activities.</li><li><strong>Multiple Visa Allocation:</strong> Obtain as many employee residency visas as your office space allows.</li><li><strong>Prestigious Dubai Commercial Address:</strong> Establish offices anywhere in prime locations across Business Bay, Downtown, or Deira.</li></ul><p>Anchor Point manages the entire formation journey: initial approval, trade name reservation, drafting Memorandum of Association (MOA), Ejari office leasing assistance, and immediate issuance of your DED Commercial License.</p>',
                'image' => '/uploads/services/new-trade-license.jpg',
                'price' => 12500,
                'timeline' => '3–5 business days',
                'deliverables' => ['Trade Name Reservation Certificate', 'Initial Approval Certificate', 'Notarized Memorandum of Association (MOA)', 'Dubai DET Commercial Trade License', 'Corporate Chamber of Commerce Certificate'],
                'requirements' => ['Passport copy of shareholders & managers', 'UAE Residence Visa or Tourist Visa copy', '3 proposed company trade names', 'Preferred business activity selection'],
                'faqs' => [
                    ['q' => 'Can an expat own 100% of a Dubai Mainland company?', 'a' => 'Yes! Under UAE Commercial Companies Law, foreign investors can own 100% of mainland companies for most commercial, industrial, and trading activities without requiring an Emirati partner.'],
                    ['q' => 'How long does mainland trade license issuance take?', 'a' => 'With Anchor Point, initial approvals and trade name reservations are completed in 24 hours, and the final trade license is typically issued within 3 to 5 business days.'],
                ],
                'sort' => 1,
                'featured' => 1,
                'seo_title' => 'Dubai Mainland Company Formation | DED License 100% Ownership',
                'seo_desc' => 'Start your Dubai mainland business with 100% foreign ownership. Fast DED trade license registration, initial approvals, and full PRO assistance by Anchor Point.'
            ],
            [
                'cat_id' => 1,
                'title' => 'UAE Free Zone Company Setup',
                'slug' => 'freezone-company-setup',
                'badge' => '0% Tax & 100% Repatriation',
                'short_desc' => 'Incorporate your business in top UAE free zones (IFZA, Meydan, DMCC, DAFZA, RAKEZ) with zero import/export duties and flexible flexi-desk packages.',
                'content' => '<p>UAE Free Zones offer international entrepreneurs the ideal ecosystem for cross-border commerce, consulting, e-commerce, and technology ventures. Enjoy 100% repatriation of capital and profits, exemption from personal income taxes, and minimal corporate compliance requirements.</p><h3>Top Recommended Free Zones</h3><ul><li><strong>IFZA (Dubai Silicon Oasis):</strong> Highly cost-effective with co-working desks and fast digital setup.</li><li><strong>Meydan Free Zone:</strong> Prestigious location near Downtown Dubai with smart digital portal approvals.</li><li><strong>DMCC (JLT Dubai):</strong> Global hub for commodities, trade, crypto, and large corporate enterprises.</li><li><strong>DAFZA & RAKEZ:</strong> Excellent infrastructure for logistics, industrial manufacturing, and regional distribution.</li></ul><p>We analyze your business model to recommend the optimal free zone jurisdiction, maximizing cost efficiency and ensuring smooth corporate bank account opening.</p>',
                'image' => '/uploads/services/new-trade-license.jpg',
                'price' => 5750,
                'timeline' => '2–4 business days',
                'deliverables' => ['Free Zone Trade License', 'Certificate of Incorporation', 'Share Certificates', 'Lease Agreement / Flexi-Desk Allocation', 'Free Zone Establishment Card'],
                'requirements' => ['Color passport copy of each shareholder', 'Passport-size digital photo with white background', 'Brief description of proposed business activities'],
                'faqs' => [
                    ['q' => 'Do I need physical office space in a Free Zone?', 'a' => 'No. Many UAE free zones provide flexi-desk or virtual co-working options that include visa eligibility without requiring physical lease commitments.'],
                ],
                'sort' => 2,
                'featured' => 1,
                'seo_title' => 'Free Zone Business Setup in Dubai & UAE | Cost & Packages',
                'seo_desc' => 'Affordable UAE Free Zone company setup with 0% personal tax and 100% profit repatriation. Packages start from AED 5,750 with IFZA, Meydan, and DMCC.'
            ],
            [
                'cat_id' => 1,
                'title' => 'Trade License Renewal & Compliance',
                'slug' => 'renewal-trade-license',
                'badge' => 'Same-Day Express Renewal',
                'short_desc' => 'Fast, hassle-free renewal of Dubai Mainland and Free Zone trade licenses, tenancy contracts (Ejari), and civil defense clearances without penalties.',
                'content' => '<p>Operating with an expired trade license can lead to heavy government fines, bank account freezing, and cancellation of employee visas. Anchor Point provides express trade license renewal services across Dubai and the Northern Emirates.</p><h3>What Our Renewal Service Covers</h3><ul><li>Reviewing existing license activity codes and removing obsolete requirements.</li><li>Ejari contract renewal and office tenancy coordination.</li><li>External department approvals (DM, RTA, SIRA, Civil Defence).</li><li>Immediate clearance of any pending ministry fines or blocks.</li></ul>',
                'image' => '/uploads/services/renewal-trade-license.jpg',
                'price' => 1500,
                'timeline' => '24–48 hours',
                'deliverables' => ['Renewed DED / Free Zone Commercial License', 'Updated Chamber of Commerce Certificate', 'Renewed Ejari Registration Certificate'],
                'requirements' => ['Copy of existing Trade License', 'Tenancy Contract and Ejari Certificate', 'Valid passports of partners/shareholders'],
                'faqs' => [
                    ['q' => 'What is the grace period for trade license renewal in Dubai?', 'a' => 'Most Dubai authorities offer a 30-day grace period from the expiry date, after which monthly fines of AED 250 to AED 500 accumulate.'],
                ],
                'sort' => 3,
                'featured' => 1,
                'seo_title' => 'Dubai Trade License Renewal Service | Avoid Fines Fast',
                'seo_desc' => 'Renew your Dubai trade license within 24 hours. Full Ejari coordination, fine clearing, and Chamber of Commerce renewal by Anchor Point.'
            ],
            [
                'cat_id' => 2,
                'title' => 'UAE Golden Visa & Investor Residency',
                'slug' => 'golden-visa-services',
                'badge' => '10-Year Renewable Residency',
                'short_desc' => 'Secure the prestigious 10-year UAE Golden Visa for real estate investors, business owners, senior executives, and specialized talents with family sponsorship.',
                'content' => '<p>The UAE Golden Visa is a long-term residency program enabling foreign nationals to live, work, and study in the UAE without a national sponsor. Holders enjoy 100% self-sponsored residency, the ability to stay outside the UAE beyond 6 months without invalidation, and sponsorship of spouses, children, and domestic helpers.</p><h3>Eligible Categories</h3><ul><li><strong>Real Estate Investors:</strong> Property owners with investments worth AED 2 Million or more.</li><li><strong>Entrepreneurs & Business Owners:</strong> Shareholders of UAE enterprises with audited revenues.</li><li><strong>Executive Leaders & Professionals:</strong> High-salary professionals (AED 30,000+ monthly basic salary) with accredited degrees.</li><li><strong>Creative & Specialized Talents:</strong> Artists, researchers, software engineers, and innovators.</li></ul><p>Anchor Point guides you through pre-approval, nomination, document attestation, medical fitness, and Emirates ID biometric processing for your entire family.</p>',
                'image' => '/uploads/services/pro-services.jpg',
                'price' => 4500,
                'timeline' => '5–7 business days',
                'deliverables' => ['10-Year UAE Residency Visa E-Stamping', '10-Year Gold Emirates ID Card', 'Family Sponsorship Clearances'],
                'requirements' => ['Passport copy with 6+ months validity', 'Title Deed (for property investors) or employment contract + attested degree', '6 months personal bank statement'],
                'faqs' => [
                    ['q' => 'Can I sponsor my parents on a Golden Visa?', 'a' => 'Yes, Golden Visa holders can sponsor their parents for a renewable 10-year residency visa.'],
                ],
                'sort' => 4,
                'featured' => 1,
                'seo_title' => 'UAE Golden Visa Services Dubai | 10-Year Investor Residency',
                'seo_desc' => 'Obtain your 10-year UAE Golden Visa smoothly. Full eligibility pre-check, property investor filing, executive approvals, and family sponsorship.'
            ],
            [
                'cat_id' => 4,
                'title' => 'Corporate Bank Account Opening Assistance',
                'slug' => 'corporate-bank-account',
                'badge' => 'Guaranteed Bank Introductions',
                'short_desc' => 'Expert guidance to secure digital and tier-1 corporate bank accounts with leading UAE banks including Emirates NBD, Mashreq, WIO, and FAB.',
                'content' => '<p>Opening a corporate bank account in the UAE requires rigorous adherence to Know-Your-Customer (KYC) and Anti-Money Laundering (AML) standards. Many applicants face prolonged delays or rejections due to incomplete business profiles or misaligned documentation.</p><p>Anchor Point partners directly with leading UAE corporate banking officers to ensure your application is pre-screened, compliant, and approved expeditiously.</p><h3>Supported Financial Institutions</h3><ul><li><strong>WIO Bank:</strong> Modern, 100% digital business banking with fast approvals in 48 hours.</li><li><strong>Mashreq NeoBiz:</strong> Competitive fee structure tailored for SMEs and startups.</li><li><strong>Emirates NBD & Commercial Bank of Dubai (CBD):</strong> Tier-1 legacy banks with international trade finance and multi-currency facilities.</li><li><strong>First Abu Dhabi Bank (FAB) & ADCB:</strong> Robust institutional banking for large corporate groups.</li></ul>',
                'image' => '/uploads/services/company-file-update.jpg',
                'price' => 2500,
                'timeline' => '5–10 business days',
                'deliverables' => ['Complete Business Profile & KYC Dossier', 'Official Banker Introduction Meeting', 'Multi-Currency Corporate Account IBAN & Online Banking Setup'],
                'requirements' => ['Valid UAE Trade License & MOA', 'Shareholders passport, visa, and Emirates ID', '6 months personal bank statements of signatories', 'Corporate profile and business invoices/contracts'],
                'faqs' => [
                    ['q' => 'Can a Free Zone company open a UAE corporate bank account?', 'a' => 'Yes, Free Zone companies can readily open corporate bank accounts with local and digital banks in the UAE subject to standard compliance checks.'],
                ],
                'sort' => 5,
                'featured' => 1,
                'seo_title' => 'Corporate Bank Account Opening in Dubai UAE | Fast Approval',
                'seo_desc' => 'Open a corporate bank account in Dubai with Emirates NBD, Mashreq, Wio, or FAB. Expert KYC pre-screening and guaranteed banker introductions.'
            ],
            [
                'cat_id' => 3,
                'title' => 'Corporate P.R.O. & Government Liaison Services',
                'slug' => 'pro-services',
                'badge' => 'Comprehensive Ministry Support',
                'short_desc' => 'Outsource all your government paperwork: GDRFA visas, MOHRE labour quotas, Tasheel contracts, Dubai Municipality approvals, and document attestations.',
                'content' => '<p>Navigating UAE government departments requires in-depth knowledge of evolving legal procedures and bilingual Arabic documentation. Anchor Point acts as your dedicated corporate Public Relations Officer, eliminating long queues and administrative delays.</p><h3>Our P.R.O. Services Include:</h3><ul><li>MOHRE Work Permit issuance, modifications, and quota increases.</li><li>GDRFA residence visa processing, transfers, and cancellations.</li><li>Emirates ID VIP biometric appointment booking and typing.</li><li>Ministry of Foreign Affairs (MOFA) degree and certificate attestations.</li><li>Dubai Customs client code registration and annual renewals.</li></ul>',
                'image' => '/uploads/services/pro-services.jpg',
                'price' => 800,
                'timeline' => '1–3 business days',
                'deliverables' => ['Approved government transaction receipts', 'Stamped residency visas & electronic work permits', 'Certified MOFA attestations'],
                'requirements' => ['Company trade license copy', 'Authorized signatory card / POA', 'Specific employee or applicant documents'],
                'faqs' => [
                    ['q' => 'Can we hire Anchor Point on an annual PRO retainer?', 'a' => 'Yes, we provide flexible monthly and annual corporate PRO retainer packages tailored to small businesses and growing enterprises.'],
                ],
                'sort' => 6,
                'featured' => 1,
                'seo_title' => 'Corporate PRO Services in Dubai | Government Document Clearing',
                'seo_desc' => 'Professional PRO services in Dubai. Hassle-free handling of GDRFA visas, MOHRE permits, Tasheel, Amer, and MOFA attestations for companies.'
            ],
            [
                'cat_id' => 3,
                'title' => 'Local Service Agent & Corporate Sponsorship',
                'slug' => 'local-agent-service',
                'badge' => '100% Operational Control',
                'short_desc' => 'Reliable, corporate UAE national representation for civil companies and professional licenses with iron-clad side agreements protecting 100% of your earnings.',
                'content' => '<p>For specialized professional activities and branch offices of foreign entities that require an Emirati Local Service Agent (LSA), Anchor Point provides dependable, vetted corporate representation.</p><p>We ensure comprehensive legal agreements through Dubai Courts that safeguard your complete operational independence, intellectual property, and financial returns.</p>',
                'image' => '/uploads/services/local-agent-service.jpg',
                'price' => 3000,
                'timeline' => '2 business days',
                'deliverables' => ['Court-Notarized Local Service Agent Agreement', 'Power of Attorney granting complete managerial authority', 'Updated Commercial Trade License'],
                'requirements' => ['Existing Trade License or Initial Approval', 'Shareholder identification'],
                'faqs' => [
                    ['q' => 'Does a Local Service Agent have shares in my business?', 'a' => 'No. A Local Service Agent has zero shares, zero operational control, and zero claim to company profits. They simply act as the liaison to local ministries.'],
                ],
                'sort' => 7,
                'featured' => 0,
                'seo_title' => 'UAE Local Service Agent & Corporate Nominee | Dubai Legal Support',
                'seo_desc' => 'Secure a trusted Local Service Agent (LSA) in Dubai with 100% beneficial ownership protection and court-notarized investor protection contracts.'
            ],
            [
                'cat_id' => 1,
                'title' => 'Company Liquidation & License Cancellation',
                'slug' => 'company-liquidation',
                'badge' => 'Full Ministry Clearance',
                'short_desc' => 'Officially close and de-register your Dubai Mainland or Free Zone business with full audit reports, tax clearances, and cancellation of government files.',
                'content' => '<p>Proper legal dissolution of a UAE entity is critical to avoid accumulating annual license fines, immigration blacklist records, and travel bans for partners and managers.</p><p>Anchor Point manages the full liquidation lifecycle: appointing an approved liquidator, publishing mandatory newspaper gazette notices, clearing MOHRE and GDRFA establishment cards, and obtaining final cancellation certificates from DED.</p>',
                'image' => '/uploads/services/company-liquidation.jpg',
                'price' => 3500,
                'timeline' => '15–30 business days',
                'deliverables' => ['Official DED License Cancellation Certificate', 'Liquidator Final Audit Report', 'MOHRE & Immigration Establishment File Closure Proofs'],
                'requirements' => ['Original trade license and commercial register', 'Company liquidation board resolution', 'VAT de-registration clearance from FTA'],
                'faqs' => [
                    ['q' => 'Is a newspaper announcement mandatory for Dubai company liquidation?', 'a' => 'Yes, a 30-day (Mainland) or 14-day (Free Zone) public notice in two local daily newspapers is legally required to invite potential creditor claims.'],
                ],
                'sort' => 8,
                'featured' => 0,
                'seo_title' => 'Dubai Company Liquidation & Trade License Cancellation',
                'seo_desc' => 'Close your UAE business legally without lingering liabilities. Full liquidator audit, newspaper notice, tax clearance, and DED de-registration.'
            ],
            [
                'cat_id' => 2,
                'title' => 'Emirates ID & Visa Medical Typing Services',
                'slug' => 'emirates-id-typing',
                'badge' => 'Fast-Track Typing Desk',
                'short_desc' => 'Express government typing for Federal Authority for Identity (ICP) Emirates IDs, DHA Visa Medical Fitness exams, and biometric appointments.',
                'content' => '<p>Whether applying for a new residency visa or renewing an existing permit, swift processing of Emirates ID and Medical Fitness typing is essential. We offer error-free typing, appointment scheduling at VIP fitness centers, and doorstep Emirates ID delivery tracking.</p>',
                'image' => '/uploads/services/emirates-id-typing.jpg',
                'price' => 350,
                'timeline' => 'Same-Day Service',
                'deliverables' => ['Official ICP Emirates ID Application Form', 'DHA / MOHAP Medical Fitness Typing Receipt', 'VIP Biometrics Appointment Confirmation'],
                'requirements' => ['Original passport or copy', 'Entry visa permit or current residence visa page', 'Passport-size digital photograph'],
                'faqs' => [
                    ['q' => 'Can I get 24-hour visa medical test results in Dubai?', 'a' => 'Yes, DHA VIP Medical Fitness centers provide express test results within 4 to 24 hours.'],
                ],
                'sort' => 9,
                'featured' => 0,
                'seo_title' => 'Emirates ID & Visa Medical Typing Center Dubai | Express Booking',
                'seo_desc' => 'Same-day Emirates ID typing and DHA Visa Medical Fitness appointment bookings in Dubai. Zero queues and express delivery support.'
            ],
            [
                'cat_id' => 3,
                'title' => 'Dubai Police Good Conduct Clearance Certificate',
                'slug' => 'police-clearance-certificate',
                'badge' => 'Official CID Verification',
                'short_desc' => 'Fast-track issuance and ministry attestation of Dubai Police Clearance Certificates for immigration, employment, and licensing.',
                'content' => '<p>A Certificate of Good Conduct issued by Dubai Police verifies that an individual has no criminal record in the UAE. It is frequently requested for foreign employment, international migration, and regulatory licensing.</p>',
                'image' => '/uploads/services/dubai-police-clearance.jpg',
                'price' => 450,
                'timeline' => '24 hours',
                'deliverables' => ['Digitally verified Dubai Police Good Conduct Certificate with official QR code'],
                'requirements' => ['Emirates ID copy or passport copy', 'Valid active email and mobile number'],
                'faqs' => [
                    ['q' => 'Can expats who already left the UAE obtain a Dubai Police Clearance?', 'a' => 'Yes, previous UAE residents can obtain a certificate by providing fingerprint cards attested by the UAE Embassy in their home country.'],
                ],
                'sort' => 10,
                'featured' => 0,
                'seo_title' => 'Dubai Police Clearance Certificate Service | Fast CID Certificate',
                'seo_desc' => 'Get your Dubai Police Good Conduct Certificate within 24 hours. Digital processing, MOFA attestation, and international delivery support.'
            ],
        ];

        $insServ = $db->prepare("
            INSERT INTO services (
                category_id, title, slug, badge, short_desc, content, featured_image,
                price_starting_aed, timeline, deliverables_json, requirements_json,
                faq_json, sort_order, is_featured, is_active, seo_title, seo_desc
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)
        ");

        foreach ($servicesList as $s) {
            $insServ->execute([
                $s['cat_id'], $s['title'], $s['slug'], $s['badge'], $s['short_desc'],
                $s['content'], $s['image'], $s['price'], $s['timeline'],
                json_encode($s['deliverables'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($s['requirements'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($s['faqs'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $s['sort'], $s['featured'], $s['seo_title'], $s['seo_desc']
            ]);
        }
    }

    // Check if blog posts are already populated
    $blogCount = (int)$db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    if ($blogCount === 0) {
        $blogPosts = [
            [
                'title' => 'How to Start a Business in Dubai: Mainland vs Free Zone (2026 Complete Guide)',
                'slug' => 'how-to-start-a-business-in-dubai-mainland-vs-freezone',
                'category' => 'Business Setup',
                'author' => 'Anchor Point Corporate Desk',
                'read_time' => '7 min read',
                'image' => '/uploads/services/new-trade-license.jpg',
                'excerpt' => 'Deciding between Dubai Mainland and Free Zone is the most critical decision for any new entrepreneur in the UAE. Explore 100% foreign ownership, costs, visa quotas, and tax implications in this complete 2026 comparison.',
                'content' => '<h2>Understanding the Dubai Business Ecosystem in 2026</h2>
<p>Dubai has firmly established its position as the premier commercial capital of the Middle East and a leading global hub for innovation, trade, and luxury tourism. When planning your company registration, your primary strategic choice lies between establishing a <strong>Dubai Mainland</strong> license under the Department of Economy and Tourism (DET) or setting up within one of the UAE\'s 40+ specialized <strong>Free Zones</strong>.</p>

<h2>Dubai Mainland: Maximum Reach and 100% Foreign Ownership</h2>
<p>Historically, foreign investors in Dubai Mainland required an Emirati local sponsor holding 51% equity. Following the landmark Commercial Companies Law revisions, <strong>100% foreign ownership</strong> is now permitted across more than 1,000 commercial and industrial activities.</p>

<h3>Key Advantages of Dubai Mainland:</h3>
<ul>
  <li><strong>Unrestricted Trading Across the UAE:</strong> Mainland entities can trade directly with consumers and B2B clients anywhere in Dubai, Abu Dhabi, and across all seven Emirates without local distributor intermediaries.</li>
  <li><strong>Government & Municipal Tenders:</strong> Only mainland companies are legally authorized to bid directly on lucrative UAE federal and municipal government contracts.</li>
  <li><strong>Flexible Office Locations:</strong> You can lease physical offices, retail showrooms, or warehouses in any district—from Downtown Dubai and Business Bay to Al Quoz and Jumeirah.</li>
  <li><strong>Scalable Visa Quotas:</strong> Employment visa quotas are linked directly to your leased office square footage, allowing seamless team expansion.</li>
</ul>
<p>Learn more about our streamlined <a href="/services/new-trade-license">Dubai Mainland Company Formation Services</a>, with trade name reservation and initial approvals delivered in 24 hours.</p>

<h2>UAE Free Zones: Tax Efficiency and Global Trade</h2>
<p>Free Zones are designated economic jurisdictions engineered for international trade, digital consulting, e-commerce, and creative enterprises. Popular options include IFZA (Dubai Silicon Oasis), Meydan Free Zone, DMCC, and DAFZA.</p>

<h3>Core Benefits of UAE Free Zones:</h3>
<ul>
  <li><strong>100% Capital & Profit Repatriation:</strong> Seamlessly move funds across international borders without exchange controls.</li>
  <li><strong>Zero Customs Duties:</strong> Re-export goods without customs tariffs within designated free zone customs gates.</li>
  <li><strong>Cost-Effective Flexi-Desks:</strong> Startups can register without leasing expensive physical offices, utilizing shared co-working desks.</li>
</ul>
<p>Explore our cost-effective <a href="/services/freezone-company-setup">Free Zone Business Setup Packages</a> starting from AED 5,750.</p>

<h2>Side-by-Side Comparison: Mainland vs Free Zone</h2>
<table style="width:100%;border-collapse:collapse;margin:1.5rem 0;">
  <thead>
    <tr style="background:#0B2545;color:#fff;">
      <th style="padding:10px;border:1px solid #ddd;">Feature</th>
      <th style="padding:10px;border:1px solid #ddd;">Dubai Mainland</th>
      <th style="padding:10px;border:1px solid #ddd;">UAE Free Zone</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="padding:8px;border:1px solid #ddd;"><strong>Ownership</strong></td>
      <td style="padding:8px;border:1px solid #ddd;">100% Expat Ownership (most activities)</td>
      <td style="padding:8px;border:1px solid #ddd;">100% Foreign Ownership</td>
    </tr>
    <tr>
      <td style="padding:8px;border:1px solid #ddd;"><strong>Local UAE Market Trade</strong></td>
      <td style="padding:8px;border:1px solid #ddd;">Direct and unlimited</td>
      <td style="padding:8px;border:1px solid #ddd;">Via mainland agent / distributor</td>
    </tr>
    <tr>
      <td style="padding:8px;border:1px solid #ddd;"><strong>Physical Office</strong></td>
      <td style="padding:8px;border:1px solid #ddd;">Mandatory Ejari tenancy</td>
      <td style="padding:8px;border:1px solid #ddd;">Flexi-desk / virtual desk allowed</td>
    </tr>
    <tr>
      <td style="padding:8px;border:1px solid #ddd;"><strong>Bank Account Ease</strong></td>
      <td style="padding:8px;border:1px solid #ddd;">Very High</td>
      <td style="padding:8px;border:1px solid #ddd;">High (with proper KYC)</td>
    </tr>
  </tbody>
</table>

<h2>Steps to Register Your Company with Anchor Point</h2>
<ol>
  <li><strong>Activity Selection & Trade Name Reservation:</strong> We help choose your exact commercial activities and register compliant trade names with the authorities.</li>
  <li><strong>Initial Approvals:</strong> Submission of passport and identity documents to DET or your chosen Free Zone authority.</li>
  <li><strong>MOA & Office Lease:</strong> Drafting notarized Memorandum of Association and finalizing lease/Ejari agreements.</li>
  <li><strong>License Issuance & Corporate Banking:</strong> Receive your official commercial license and open your corporate bank account with leading banks like Emirates NBD or WIO through our <a href="/services/corporate-bank-account">Banking Assistance Desk</a>.</li>
</ol>
<p>Ready to establish your enterprise? Contact Anchor Point\'s senior advisory team on WhatsApp for a confidential consultation tailored to your commercial goals.</p>',
                'seo_title' => 'How to Start a Business in Dubai: Mainland vs Free Zone 2026',
                'seo_desc' => 'Complete 2026 guide on Dubai business setup. Compare Mainland vs Free Zone costs, 100% expat ownership rules, visa quotas, and trade license steps.'
            ],
            [
                'title' => 'Dubai Golden Visa: Eligibility, Requirements & Step-by-Step Application Process',
                'slug' => 'dubai-golden-visa-complete-guide-requirements-process',
                'category' => 'Residency & Visas',
                'author' => 'Immigration & PRO Specialist',
                'read_time' => '6 min read',
                'image' => '/uploads/services/pro-services.jpg',
                'excerpt' => 'Learn how to secure the coveted 10-year UAE Golden Visa through real estate investment, executive employment, or business ownership. Complete breakdown of criteria and family benefits.',
                'content' => '<h2>What Makes the UAE Golden Visa the World\'s Most Desirable Residency?</h2>
<p>The UAE Golden Visa is a 10-year renewable residency visa granted to foreign investors, entrepreneurs, executives, researchers, and outstanding talents. Unlike conventional visas that require local corporate employer sponsorship and expire every two years, the Golden Visa grants complete self-sufficiency.</p>

<h3>Primary Golden Visa Privileges:</h3>
<ul>
  <li><strong>10-Year Self-Sponsored Residency:</strong> No national employer or local sponsor required.</li>
  <li><strong>No Restrictions on Overseas Travel:</strong> Unlike standard UAE visas that cancel automatically if the holder remains outside the UAE for more than 180 days, Golden Visa holders can stay abroad indefinitely without losing their residency status.</li>
  <li><strong>Comprehensive Family Sponsorship:</strong> Sponsor spouse, children of any age, and domestic staff with full 10-year validity.</li>
  <li><strong>Uninterrupted Family Stay:</strong> In the event of the primary visa holder\'s passing, family members can continue residing in the UAE until the full duration of the visa concludes.</li>
</ul>

<h2>Eligibility Pathways in 2026</h2>
<h3>1. Real Estate Investors (AED 2 Million Property)</h3>
<p>Investors purchasing one or multiple properties in Dubai with an aggregate value of AED 2,000,000 or more qualify for the 10-year Golden Visa. Mortgaged properties are also eligible provided that a minimum down-payment receipt or bank NOC letter confirms equity value.</p>

<h3>2. Senior Executives & High-Earning Professionals</h3>
<p>Executives holding accredited university bachelor\'s degrees, a valid UAE employment contract categorized under MOHRE Skill Level 1 or 2, and a minimum monthly basic salary of AED 30,000 qualify for self-sponsored 10-year residency.</p>

<h3>3. Successful Entrepreneurs & Business Owners</h3>
<p>Shareholders owning equity in a UAE-registered entity generating audited annual revenues of AED 1,000,000 or more, or founders whose projects have been approved by official incubators or the Ministry of Economy.</p>

<h2>Step-by-Step Golden Visa Roadmap</h2>
<ol>
  <li><strong>Eligibility Pre-Check:</strong> Our team reviews your title deeds, employment contracts, and degree attestations.</li>
  <li><strong>Government Nomination & Entry Permit:</strong> Securing the initial ICP / GDRFA nomination approval.</li>
  <li><strong>VIP Medical Exam & Biometrics:</strong> Express health screening at priority centers with zero waiting queues.</li>
  <li><strong>Residency E-Stamping & Emirates ID:</strong> Issuance of the 10-year residency permit and Gold Emirates ID card.</li>
</ol>
<p>Explore our full <a href="/services/golden-visa-services">UAE Golden Visa Support Services</a> or message our immigration specialists directly on WhatsApp for an immediate profile pre-screening.</p>',
                'seo_title' => 'Dubai Golden Visa Requirements & Guide | 10-Year UAE Residency',
                'seo_desc' => 'Everything you need to know about getting the 10-Year UAE Golden Visa in Dubai. Real estate requirements, executive criteria, family sponsorship, and costs.'
            ],
            [
                'title' => 'The Ultimate Dubai Desert Safari Guide: Morning, Evening BBQ & VIP Majlis Compared',
                'slug' => 'best-desert-safari-in-dubai-morning-evening-overnight-comparison',
                'category' => 'Desert Safaris',
                'author' => 'Anchor Point Tourism Team',
                'read_time' => '6 min read',
                'image' => '/uploads/activities/evening-desert-safari-with-bbq-dinner-cover.webp',
                'excerpt' => 'Planning a Dubai desert adventure? Compare Morning Dune Bashing, Evening BBQ Dinner, and VIP Majlis desert safaris to discover the perfect experience for your family or group.',
                'content' => '<h2>Why Desert Safaris Are Dubai\'s Top-Rated Experience</h2>
<p>No trip to the United Arab Emirates is truly complete without experiencing the magnificent red dunes of the Arabian desert. From heart-pounding dune bashing in a Land Cruiser to sipping fresh Arabic Gahwa coffee under the stars, Dubai offers safari formats suited for every travel style.</p>

<h2>1. Morning Desert Safari: Perfect for Adventure & Photography</h2>
<p>If you have tight afternoon flight schedules or want to avoid desert heat during summer, the Morning Desert Safari is an ideal 4-hour escapade. Departing early at 08:00 AM, you arrive when desert sands are crisp and peaceful. Enjoy dune bashing, sandboarding down 300-foot dunes, and a serene camel trek before returning to the city by midday.</p>

<h2>2. Evening Desert Safari & BBQ Dinner: The Classic UAE Favorite</h2>
<p>The definitive desert experience. Departing mid-afternoon around 03:00 PM, guests enjoy thrilling 4x4 dune bashing across Lahbab\'s red dunes, pause for golden sunset photos, and arrive at a traditional Bedouin camp for camel rides, henna painting, live Tanoura dance, fire performances, and a lavish barbecue buffet.</p>
<p>Check out our top-booked <a href="/desert-safari/evening-desert-safari-with-bbq-dinner">Evening Desert Safari with BBQ Dinner</a> with instant WhatsApp booking.</p>

<h2>3. VIP Private Desert Safari: Luxury & Personalized Pace</h2>
<p>Designed for families with young children, couples, or corporate groups seeking exclusive comfort. You receive a private 4x4 Land Cruiser with a chauffeur who customizes dune intensity to your exact comfort level, alongside reserved majlis seating and table-waiter service at dinner.</p>
<p>Discover complete details on our <a href="/desert-safari/vip-private-desert-safari">VIP Private Desert Safari Package</a> with private hotel transfers included.</p>

<h2>4. Desert Safari Tips: What to Wear & Best Seasons</h2>
<p>Wear loose, breathable cotton clothing and sandals or sneakers that can easily be dusted off. Bring sunglasses, sunscreen, and a light jacket if traveling between November and March when desert evenings get pleasantly cool.</p>',
                'seo_title' => 'Best Dubai Desert Safari Guide: Morning, Evening BBQ & VIP Majlis',
                'seo_desc' => 'Discover the best desert safari in Dubai. Compare morning dune bashing, evening BBQ dinner shows, and luxury VIP private safaris.'
            ],
            [
                'title' => 'Opening a Corporate Bank Account in Dubai, UAE: Documents, Timelines & Tips',
                'slug' => 'how-to-open-a-corporate-bank-account-in-dubai-uae',
                'category' => 'Corporate Banking',
                'author' => 'Banking & Compliance Desk',
                'read_time' => '5 min read',
                'image' => '/uploads/services/company-file-update.jpg',
                'excerpt' => 'Navigating UAE corporate banking compliance can be challenging. Discover the exact document checklist, banker interview questions, and digital vs legacy bank options.',
                'content' => '<h2>Why UAE Corporate Bank Account Opening Requires Specialized Guidance</h2>
<p>Following international FATF and AML regulatory updates, UAE commercial banks enforce rigorous due diligence on new company bank accounts. Having a registered trade license alone is not enough; banks require verifiable proof of business substance, clear source of funds, and commercial credibility.</p>

<h2>Top UAE Banks for Business Accounts in 2026</h2>
<ul>
  <li><strong>Wio Bank Business:</strong> Revolutionizing UAE SME banking. Fast digital onboarding, seamless multi-currency accounts (AED, USD, EUR, GBP), and virtual debit cards with approval in as little as 48 hours.</li>
  <li><strong>Mashreq NeoBiz:</strong> Built specifically for startups and growing enterprises with minimal balance requirements and straightforward integration.</li>
  <li><strong>Emirates NBD & Commercial Bank of Dubai (CBD):</strong> Premier institutions offering comprehensive trade finance, LC/LG letters of credit, and dedicated relationship managers for high-volume transactions.</li>
</ul>

<h2>Mandatory Document Checklist for Corporate Banking</h2>
<ol>
  <li>Valid UAE Commercial Trade License and Certificate of Incorporation.</li>
  <li>Notarized Memorandum of Association (MOA) and Articles of Association (AOA).</li>
  <li>Shareholders\' passports, UAE residency visas, and Emirates ID cards.</li>
  <li>Comprehensive Company Business Profile detailing target clients, suppliers, and estimated annual turnover.</li>
  <li>Personal bank statements (last 6 months) for primary signatories.</li>
  <li>Commercial contracts, customer intent letters, or invoices demonstrating active trading.</li>
</ol>
<p>Anchor Point\'s <a href="/services/corporate-bank-account">Corporate Banking Advisory Desk</a> pre-screens your compliance dossier and coordinates direct introductions with experienced corporate bankers to avoid rejection risks.</p>',
                'seo_title' => 'Open a Corporate Bank Account in Dubai UAE | 2026 Guide',
                'seo_desc' => 'How to open a business bank account in Dubai, UAE. Required documents, top banks (Emirates NBD, Wio, Mashreq), KYC tips, and approval timelines.'
            ],
            [
                'title' => 'Top 10 Sightseeing Tours and Iconic Landmarks in Dubai & Abu Dhabi',
                'slug' => 'top-sightseeing-tours-and-attractions-in-dubai-abu-dhabi',
                'category' => 'City Sightseeing',
                'author' => 'Anchor Point Tourism Desk',
                'read_time' => '6 min read',
                'image' => '/uploads/activities/dubai-city-tour-half-day-cover.webp',
                'excerpt' => 'From the soaring height of Burj Khalifa and historic Al Fahidi Fort to the awe-inspiring Sheikh Zayed Grand Mosque in Abu Dhabi, discover the ultimate UAE sightseeing roadmap.',
                'content' => '<h2>Experiencing the Contrasts of Old and Modern UAE</h2>
<p>The United Arab Emirates blends ultra-futuristic architectural marvels with centuries-old Arabian heritage. Whether you are visiting for a short 48-hour stopover or an extended family holiday, these top tours showcase the absolute best of the country.</p>

<h2>1. Dubai Half-Day Guided City Tour</h2>
<p>A comprehensive introduction to the city covering the sail-shaped Burj Al Arab, the vibrant Dubai Marina skyline, the luxury villas of Palm Jumeirah, and a traditional Abra water taxi ride across Dubai Creek to explore the Gold and Spice Souks.</p>
<p>Book the official <a href="/city-tours/dubai-city-tour-half-day">Dubai City Tour Half-Day</a> with hotel pick-up.</p>

<h2>2. Abu Dhabi Full-Day Tour & Sheikh Zayed Grand Mosque</h2>
<p>A 90-minute scenic drive from Dubai takes you to the nation\'s capital. Tour the magnificent Sheikh Zayed Grand Mosque—one of the world\'s largest mosques featuring 82 white marble domes, 24-carat gold chandeliers, and hand-knotted Persian carpets. Continue past the Emirates Palace, the presidential Qasr Al Watan, and Louvre Abu Dhabi on Saadiyat Island.</p>
<p>Explore our highly-recommended <a href="/city-tours/abu-dhabi-city-tour-grand-mosque">Abu Dhabi City Tour from Dubai</a>.</p>

<h2>3. At The Top — Burj Khalifa (124th & 125th Floors)</h2>
<p>Ascend in high-speed double-deck elevators to the observation deck of the world\'s tallest skyscraper (828 meters). Book skip-the-line sunrise or prime sunset slots to witness panoramic vistas extending across the Arabian Gulf.</p>
<p>Reserve instant tickets via our <a href="/tickets-attractions">Attraction Tickets Desk</a>.</p>',
                'seo_title' => 'Top 10 Sightseeing Tours in Dubai & Abu Dhabi | UAE Landmarks',
                'seo_desc' => 'Explore the best sightseeing tours in Dubai and Abu Dhabi. Burj Khalifa, Sheikh Zayed Grand Mosque, Dubai Marina, and historic souks with licensed guides.'
            ],
            [
                'title' => 'Complete Guide to Dubai Marina vs Dubai Creek Dhow Dinner Cruises',
                'slug' => 'complete-guide-to-dubai-marina-and-creek-dinner-cruises',
                'category' => 'Dinner Cruises',
                'author' => 'Cruise & Event Specialist',
                'read_time' => '5 min read',
                'image' => '/uploads/activities/dhow-cruise-dinner-dubai-marina-cover.webp',
                'excerpt' => 'Trying to decide between a modern luxury cruise in Dubai Marina and a traditional heritage dinner along historic Dubai Creek? Here is a detailed comparison of views, food, and atmosphere.',
                'content' => '<h2>Dining on the Water: A Dubai Must-Do</h2>
<p>An evening dinner cruise is one of the most relaxing ways to view Dubai\'s illuminated skyline while indulging in an international buffet with live Arabic entertainment. However, travellers often wonder whether to book a cruise along <strong>Dubai Marina</strong> or <strong>Dubai Creek</strong>.</p>

<h2>Dubai Marina Cruise: Contemporary Luxury & Glittering Towers</h2>
<p>Dubai Marina offers a chic, ultra-modern backdrop. As your two-deck vessel glides past illuminated skyscrapers, Ain Dubai observation wheel, and JBR beachfront, you are surrounded by multi-million-dollar yachts and contemporary architectural splendor.</p>
<h3>Best for:</h3>
<ul>
  <li>Couples seeking a romantic, modern ambiance.</li>
  <li>Photography enthusiasts wanting vibrant neon reflections.</li>
  <li>Celebrations, anniversaries, and corporate group dinners.</li>
</ul>
<p>View our popular <a href="/cruise-dinner/dubai-marina-dhow-dinner-cruise">Dubai Marina Dhow Dinner Cruise</a> package.</p>

<h2>Dubai Creek Cruise: Authentic Arabian Heritage</h2>
<p>Dubai Creek represents the historic heartbeat of the city where Dubai first flourished as a pearling and trading port. Cruising along the Creek provides views of heritage wind towers, illuminated old souks, and traditional wooden cargo dhows under star-lit skies.</p>
<h3>Best for:</h3>
<ul>
  <li>Families wanting authentic old-world cultural charm.</li>
  <li>Budget-conscious travellers (Creek cruises offer exceptional value).</li>
  <li>Those wanting a classic Bedouin maritime feel.</li>
</ul>
<p>Browse our <a href="/cruise-dinner/dubai-creek-dhow-dinner-cruise">Dubai Creek Dhow Dinner Cruise</a> with buffet dining and Tanoura shows.</p>',
                'seo_title' => 'Dubai Marina vs Dubai Creek Dinner Cruise Comparison | UAE Tours',
                'seo_desc' => 'Compare Dubai Marina and Dubai Creek dinner cruises. Find out which offers the best skyline views, buffet menu, entertainment, and value for your evening.'
            ]
        ];

        $insBlog = $db->prepare("
            INSERT INTO blog_posts (
                title, slug, category, excerpt, content, featured_image,
                author, read_time, views, seo_title, seo_desc, is_published
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        foreach ($blogPosts as $bp) {
            $insBlog->execute([
                $bp['title'], $bp['slug'], $bp['category'], $bp['excerpt'],
                $bp['content'], $bp['image'], $bp['author'], $bp['read_time'],
                $bp['views'] ?? 350, $bp['seo_title'], $bp['seo_desc']
            ]);
        }
    }
}
