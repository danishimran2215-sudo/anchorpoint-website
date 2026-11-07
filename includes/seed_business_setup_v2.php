<?php
/**
 * Business Setup v2 — complete category-wise service structure (matches the previous Anchor Point website).
 *
 * 5 categories → 31 sub-services. Safe to run repeatedly: guarded by the `business_setup_v2` setting and
 * upserts by slug, so admin edits to existing services' text are never overwritten.
 */
declare(strict_types=1);

function seedBusinessSetupV2(PDO $db): void {
    try {
        $chk = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'business_setup_v2' LIMIT 1");
        $chk->execute();
        $r = $chk->fetch();
        if ($r && (string)$r['setting_value'] === '1') return;
    } catch (Throwable $e) {
        return;
    }

    // Tables are created by seedBusinessAndBlog(); bail out safely if missing
    try {
        $db->query('SELECT 1 FROM services LIMIT 1');
        $db->query('SELECT 1 FROM service_categories LIMIT 1');
    } catch (Throwable $e) {
        return;
    }

    // ── Categories (existing slugs are reused so older services stay attached) ──
    $cats = [
        ['company-formation', 'Business Setup & Licensing',             'Mainland, Free Zone and offshore company formation, trade license renewal, amendments, quota changes and liquidation.', 1],
        ['visa-services',     'Visa & Immigration Services',            'Investor, employment, family and freelance visas, Golden Visa, Emirates ID and medical typing.', 2],
        ['pro-services',      'Government Approvals & PRO Services',    'PRO outsourcing, local service agent, Dubai Municipality, RTA and SIRA approvals, police clearance, POA and establishment card.', 3],
        ['vehicle-services',  'Vehicle & Traffic Services',             'Driving license renewal, Salik and Darb toll accounts, vehicle insurance and Mulkiya registration.', 4],
        ['banking-legal',     'Corporate, Legal & Financial Services',  'Corporate bank accounts, VAT and tax registration, legal support, wills, business email and website development.', 5],
    ];
    $catId = [];
    $selCat = $db->prepare('SELECT id FROM service_categories WHERE slug = ? LIMIT 1');
    $insCat = $db->prepare('INSERT INTO service_categories (name, slug, description, sort_order, is_active) VALUES (?, ?, ?, ?, 1)');
    $updCat = $db->prepare('UPDATE service_categories SET name = ?, description = ?, sort_order = ?, is_active = 1 WHERE slug = ?');
    foreach ($cats as [$slug, $name, $desc, $sort]) {
        $selCat->execute([$slug]);
        $row = $selCat->fetch();
        if ($row) {
            $updCat->execute([$name, $desc, $sort, $slug]);
            $catId[$slug] = (int)$row['id'];
        } else {
            $insCat->execute([$name, $slug, $desc, $sort]);
            $selCat->execute([$slug]);
            $catId[$slug] = (int)($selCat->fetch()['id'] ?? 0);
        }
    }

    // ── Existing services that must be (re)assigned to the right category, in display order ──
    // slug => [category, image]
    $existing = [
        'new-trade-license'           => ['company-formation', '/uploads/services/new-trade-license.jpg'],
        'freezone-company-setup'      => ['company-formation', '/uploads/services/freezone-company-setup.jpg'],
        'renewal-trade-license'       => ['company-formation', '/uploads/services/renewal-trade-license.jpg'],
        'company-liquidation'         => ['company-formation', '/uploads/services/company-liquidation.jpg'],
        'golden-visa-services'        => ['visa-services',     '/uploads/services/golden-visa-services.jpg'],
        'emirates-id-typing'          => ['visa-services',     '/uploads/services/emirates-id-typing.jpg'],
        'pro-services'                => ['pro-services',      '/uploads/services/pro-services.jpg'],
        'local-agent-service'         => ['pro-services',      '/uploads/services/local-agent-service.jpg'],
        'police-clearance-certificate'=> ['pro-services',      '/uploads/services/dubai-police-clearance.jpg'],
        'corporate-bank-account'      => ['banking-legal',     '/uploads/services/corporate-bank-account.jpg'],
    ];

    $faqGen = static fn(string $t, string $a): array => [['q' => $t, 'a' => $a]];
    $steps  = '<h3>How It Works</h3><ol><li><strong>Free consultation</strong> on WhatsApp or at our Al Jafiliya office.</li><li><strong>Document review</strong> and government submission by our PRO team.</li><li><strong>Tracking &amp; delivery</strong> — you receive approved documents with status updates.</li></ol>';

    // ── New services: [slug, category, title, badge, short, price, timeline, image, overview, covers[], deliverables[], requirements[], faq[], seoTitle, seoDesc] ──
    $new = [
        ['company-file-update', 'company-formation', 'Company File Update & License Amendments', 'License & Contract Amendments',
         'Update trade license activities, partners, manager, address or legal form with DET, free zones and MOHRE without interrupting operations.', 1200, '2–5 business days', '/uploads/services/company-file-update.jpg',
         'Businesses evolve, and your license must evolve with them. We handle every amendment to your company file, from adding new activities to changing partners, so your license, contracts and government records always match.',
         ['Adding or removing business activities', 'Change of partners, manager or shareholding', 'Trade name and legal form changes', 'Office address and Ejari updates', 'MOHRE and immigration establishment file updates'],
         ['Amended trade license', 'Updated MOA / addendum', 'Updated establishment card', 'Chamber of Commerce amendment certificate'],
         ['Current trade license', 'Passports and Emirates IDs of partners', 'Signed partner resolution or amendment', 'Tenancy contract and Ejari (for address changes)'],
         $faqGen('Can I add a new activity to my existing trade license?', 'Yes. Most activities can be added to an existing Mainland or Free Zone license after approval from the relevant authority. We confirm requirements and costs before you commit.'),
         'Company File Update & Trade License Amendment Dubai', 'Update your Dubai trade license: add activities, change partners, manager or address. Fast DET, Free Zone and MOHRE amendments by Anchor Point.'],

        ['modification-of-quota', 'company-formation', 'Modification of Visa Quota (MOHRE)', 'Increase Your Visa Capacity',
         'Increase or modify your company labour and visa quota with MOHRE so you can hire and sponsor more employees.', 900, '1–3 business days', '/uploads/services/modification-of-quota.jpg',
         'Your visa quota decides how many employees your company can sponsor. When you outgrow it, we prepare the MOHRE application, office-space justification and approvals needed to raise it.',
         ['Quota assessment based on office size and activity', 'MOHRE establishment file review', 'Quota increase application and tracking', 'Immigration card update after approval'],
         ['MOHRE quota approval', 'Updated establishment card', 'Updated labour quota record'],
         ['Valid trade license', 'Tenancy contract / Ejari showing office area', 'Authorised signatory passport and Emirates ID', 'Existing establishment card'],
         $faqGen('How many visas can my company sponsor?', 'The quota depends on your license type, activity and the size of your registered office. We review these before applying so you know the realistic number up front.'),
         'Visa Quota Modification Dubai | MOHRE Labour Quota Increase', 'Need more employee visas? Anchor Point handles MOHRE visa quota modification and increases for Dubai Mainland and Free Zone companies.'],

        ['investor-partner-visa', 'visa-services', 'Investor & Partner Residence Visa', 'Residency for Owners & Partners',
         'Residence visas for company investors, shareholders and partners, including medical, Emirates ID and visa stamping.', 3200, '5–7 business days', '/uploads/services/investor-partner-visa.jpg',
         'As a shareholder or partner in a UAE company you can sponsor your own residence visa. We manage the entry permit, status change, medical test, Emirates ID biometrics and visa stamping in one coordinated process.',
         ['Entry permit or status change inside the UAE', 'Medical fitness test booking', 'Emirates ID application and biometrics', 'Residence visa stamping'],
         ['UAE residence visa', 'Emirates ID', 'Medical fitness certificate'],
         ['Passport copy with 6+ months validity', 'Passport-size photo with white background', 'Valid trade license and MOA / partnership proof', 'Establishment card'],
         $faqGen('How long is an investor visa valid?', 'Investor and partner visas are generally issued for 2 years and are renewable. Your trade license and establishment card must stay valid.'),
         'Investor & Partner Visa Dubai | UAE Residence for Business Owners', 'Get your UAE investor or partner residence visa with medical, Emirates ID and stamping handled end to end by Anchor Point.'],

        ['employment-visa', 'visa-services', 'Employment Visa & Labour Contract', 'New Hire & Visa Transfer',
         'Hire overseas or inside-UAE employees with work permit, labour contract, medical, Emirates ID and visa stamping handled for you.', 2800, '5–7 business days', '/uploads/services/employment-visa.jpg',
         'From the MOHRE offer letter and work permit to the visa stamp, we manage every government step for your new employees, whether they are arriving from abroad or transferring from another sponsor.',
         ['MOHRE offer letter and work permit', 'Entry permit and status change', 'Medical test and Emirates ID biometrics', 'Labour contract registration via Tasheel'],
         ['Residence visa', 'Emirates ID', 'Approved labour contract'],
         ['Employee passport copy and photo', 'Educational certificates (attested where required)', 'Company trade license and establishment card', 'Signed employment offer'],
         $faqGen('Can you transfer an employee from another company?', 'Yes. We handle visa transfers including the new labour contract, cancellation clearance and Emirates ID update.'),
         'Employment Visa Dubai | Work Permit & Labour Contract Services', 'Hire staff in the UAE with ease. Anchor Point processes employment visas, MOHRE work permits and labour contracts for companies in Dubai.'],

        ['family-visa', 'visa-services', 'Family Sponsorship Visa', 'Spouse, Children & Parents',
         'Sponsor your spouse, children or parents on a UAE residence visa with all attestations, medicals and Emirates IDs.', 2400, '5–7 business days', '/uploads/services/family-visa.jpg',
         'Bring your family to live with you in the UAE. We check your sponsorship eligibility, prepare attested documents and manage the visa, medical and Emirates ID steps for each family member.',
         ['Eligibility check (salary / contract / accommodation)', 'Marriage and birth certificate attestation guidance', 'Entry permit, medical and biometrics', 'Residence visa stamping and Emirates ID'],
         ['Family residence visas', 'Emirates IDs', 'Medical fitness certificates'],
         ['Sponsor passport, visa and Emirates ID', 'Attested marriage / birth certificates', 'Tenancy contract / Ejari', 'Salary certificate or labour contract'],
         $faqGen('What salary is required to sponsor my family?', 'Requirements depend on your profession and the authorities at the time of application. Share your details on WhatsApp and we will confirm eligibility before any fees.'),
         'Family Visa Dubai | Spouse, Children & Parents Sponsorship', 'Sponsor your family in the UAE. Anchor Point handles attestations, medicals, Emirates ID and residence visas for spouse, children and parents.'],

        ['freelance-visa', 'visa-services', 'Freelance Visa & Permit', 'Work for Yourself in the UAE',
         'Obtain a freelance permit and UAE residence visa without needing a full company license or an employer sponsor.', 7500, '5–10 business days', '/uploads/services/freelance-visa.jpg',
         'Freelance permits let creatives, consultants and IT professionals live and work legally in the UAE. We choose the right free-zone package for your activity and manage the permit, visa and Emirates ID.',
         ['Free-zone freelance package selection', 'Permit application and approval', 'Residence visa and medical', 'Emirates ID and bank-account guidance'],
         ['Freelance permit', 'UAE residence visa', 'Emirates ID'],
         ['Passport copy and photo', 'CV / portfolio and qualifications', 'Brief description of your freelance activity'],
         $faqGen('Can I sponsor my family on a freelance visa?', 'In most cases yes, once you hold a valid residence visa and meet the income and housing requirements. We will confirm for your package.'),
         'Freelance Visa Dubai | UAE Freelancer Permit & Residency', 'Get a UAE freelance permit and residence visa. Anchor Point guides freelancers, consultants and creatives through the full process.'],

        ['dm-trade-licensing', 'pro-services', 'Dubai Municipality (DM) Trade Licensing Approval', 'Food, Health & Safety Approvals',
         'Secure Dubai Municipality approvals for food, health and safety compliant business activities and premises.', 800, '3–7 business days', '/uploads/services/dm-trade-licensing.jpg',
         'Many activities, from restaurants and cafeterias to trading and cleaning, need Dubai Municipality approval before the license can be issued. We prepare drawings, documents and inspection bookings so approval goes through first time.',
         ['Initial approval and activity review', 'Premises plan and layout submission', 'Inspection booking and follow-up', 'Final DM approval for trade license'],
         ['DM approval certificate', 'Updated trade license activities'],
         ['Trade license or initial approval', 'Tenancy contract / Ejari', 'Premises plan or layout', 'Authorised signatory ID'],
         $faqGen('Which activities need Dubai Municipality approval?', 'Food and beverage, health, cleaning, general trading with storage and many other activities. We check your activity codes and confirm if DM approval is required.'),
         'Dubai Municipality Trade License Approval | DM Approvals Dubai', 'Fast Dubai Municipality (DM) approvals for trade licensing, food and health activities handled by Anchor Point PRO specialists.'],

        ['rta-trade-licensing', 'pro-services', 'RTA Trade Licensing Approval', 'Transport & Commercial Vehicle Approvals',
         'Obtain Roads & Transport Authority approvals for transport, limousine, cargo and vehicle-related trade licenses.', 1000, '5–10 business days', '/uploads/services/rta-trade-licensing.jpg',
         'Transport and vehicle-related businesses need RTA approval before trading. We prepare the application, NOC and vehicle documentation and track it until approval.',
         ['RTA initial approval and NOC', 'Commercial transport activity registration', 'Vehicle and driver documentation', 'Follow-up until approval is issued'],
         ['RTA approval / NOC', 'Updated trade license'],
         ['Trade license or initial approval', 'Tenancy contract / Ejari', 'Vehicle details and ownership documents', 'Authorised signatory ID'],
         $faqGen('Which businesses need RTA approval?', 'Transport, limousine and taxi services, cargo and logistics, vehicle rental and some vehicle trading activities.'),
         'RTA Trade License Approval Dubai | Transport Activity NOC', 'Get Roads & Transport Authority (RTA) approvals for your Dubai transport or vehicle business with Anchor Point.'],

        ['sira-trade-licensing', 'pro-services', 'SIRA Trade Licensing Approval (Security)', 'Security Industry Regulatory Agency',
         'Secure Security Industry Regulatory Agency (SIRA) approval for security, CCTV and guarding activities in Dubai.', 1500, '5–10 business days', '/uploads/services/sera-trade-licensing.jpg',
         'Security guarding, CCTV installation and related activities are regulated by SIRA in Dubai. We manage the application, staff clearances and compliance documentation needed for approval.',
         ['SIRA initial approval application', 'Activity and premises compliance check', 'Staff and manager clearance guidance', 'Final approval for trade license'],
         ['SIRA approval certificate', 'Updated trade license'],
         ['Trade license or initial approval', 'Tenancy contract / Ejari', 'Manager and staff documents', 'Authorised signatory ID'],
         $faqGen('Do CCTV installation companies need SIRA approval?', 'Yes. In Dubai, security systems and guarding activities require SIRA approval. We confirm the exact requirements for your activity.'),
         'SIRA Approval Dubai | Security Company Trade License', 'Fast SIRA (Security Industry Regulatory Agency) approvals for security, CCTV and guarding trade licenses in Dubai.'],

        ['dubai-police-lost-item', 'pro-services', 'Dubai Police Lost Item Certificate', 'Official Lost Report',
         'Report lost documents or items and receive an official Dubai Police lost-item certificate for replacement of IDs and passports.', 150, 'Same day', '/uploads/services/dubai-police-loss-item.jpg',
         'Lost your passport, Emirates ID, license or important documents? We file the report with Dubai Police and deliver the official certificate you need to start replacements.',
         ['Online lost-item report filing', 'Official certificate with QR verification', 'Guidance for passport / Emirates ID replacement'],
         ['Dubai Police lost-item certificate'],
         ['Emirates ID or passport copy (if available)', 'Valid mobile number and email', 'Description of the lost item'],
         $faqGen('How fast can I get the certificate?', 'In most cases the same day, once details are verified.'),
         'Dubai Police Lost Item Certificate | Lost Passport & ID Report', 'Get an official Dubai Police lost-item certificate fast. Anchor Point files the report and guides your document replacement.'],

        ['power-of-attorney', 'pro-services', 'Power of Attorney (POA) Drafting & Attestation', 'Notarised & Court-Attested',
         'Draft, notarise and attest a Power of Attorney for business, property, vehicle or banking matters in the UAE.', 600, '1–2 business days', '/uploads/services/power-of-attorney.jpg',
         'A Power of Attorney lets someone you trust act on your behalf. We prepare the Arabic-English draft, arrange notarisation and handle attestation so your POA is accepted by banks, courts and authorities.',
         ['Bilingual POA drafting', 'Notary public / court registration', 'MOFA attestation when needed', 'Guidance on scope and validity'],
         ['Notarised Power of Attorney', 'Attestation stamps where required'],
         ['Passport / Emirates ID of the principal', 'Passport / ID of the attorney', 'Clear description of powers to be granted'],
         $faqGen('Can I give a POA from outside the UAE?', 'Yes, but it must be signed and attested in the country of issue and then legalised for UAE use. We guide you through the steps.'),
         'Power of Attorney Dubai | POA Drafting, Notary & Attestation', 'Professional POA drafting, notarisation and attestation in Dubai for business, property, vehicle and banking matters.'],

        ['establishment-card', 'pro-services', 'Establishment Card (Immigration & Labour File)', 'Open Your Company Visa File',
         'Issue or renew your company establishment card and open the immigration and labour file required to sponsor employees.', 1100, '2–4 business days', '/uploads/services/establishment-card.jpg',
         'The establishment card links your company to the immigration and labour authorities and is required before you can sponsor any visa. We open, renew or amend it quickly and correctly.',
         ['New establishment card issuance', 'Card renewal and amendment', 'Linking quota and e-signature card', 'Immigration file clearance'],
         ['Establishment card', 'Labour and immigration file number'],
         ['Valid trade license', 'Tenancy contract / Ejari', 'Passport and Emirates ID of owner / manager', 'MOA or partnership agreement'],
         $faqGen('When do I need an establishment card?', 'Immediately after trade license issuance if you plan to sponsor any employee or partner visa. It must also be renewed each year.'),
         'Establishment Card Dubai | Immigration & Labour File Services', 'Issue or renew your company establishment card in Dubai quickly. Anchor Point manages immigration and labour file services.'],

        ['driving-license-renewal', 'vehicle-services', 'UAE Driving License Renewal (RTA)', 'Renew in Minutes',
         'Renew your UAE driving license with eye test and RTA processing handled by our team, with no queues.', 250, 'Same day', '/uploads/services/driving-license-renewal.jpg',
         'Do not risk fines for an expired license. We arrange the eye test, submit your renewal to RTA and deliver the renewed license so you can keep driving without interruption.',
         ['Eye test booking', 'RTA renewal submission', 'Traffic fine clearance check', 'License delivery'],
         ['Renewed driving license'],
         ['Current driving license', 'Emirates ID', 'Valid mobile number'],
         $faqGen('Can I renew my license if I have unpaid fines?', 'Outstanding fines usually must be cleared first. We can check and settle them as part of the service.'),
         'Driving License Renewal Dubai | RTA License Renewal Service', 'Renew your UAE driving license the easy way. Anchor Point handles the eye test and RTA renewal with delivery to you.'],

        ['dubai-salik', 'vehicle-services', 'Dubai Salik Registration & Tag Activation', 'Toll Account Setup',
         'Register a Salik toll account, activate your tag and top up your balance so you never get a toll fine.', 120, 'Same day', '/uploads/services/dubai-salik.jpg',
         'Driving on Dubai roads requires an active Salik account. We register your vehicle, activate the tag and set up automatic top-up so toll gates charge your account correctly.',
         ['Salik account registration', 'Tag activation and installation guidance', 'Balance top-up and auto-recharge setup', 'Fine and balance check'],
         ['Active Salik account and tag'],
         ['Vehicle registration card (Mulkiya)', 'Emirates ID', 'Valid mobile number and email'],
         $faqGen('What happens if I drive through a Salik gate without a tag?', 'A fine is applied for each crossing. Registering and keeping a balance avoids this.'),
         'Dubai Salik Registration | Salik Tag Activation & Top-Up', 'Register your Dubai Salik account and activate your tag with Anchor Point. Fast, simple toll account setup.'],

        ['abu-dhabi-salik', 'vehicle-services', 'Abu Dhabi Darb Toll Registration', 'Darb Account & Tag',
         'Register your vehicle for the Abu Dhabi Darb toll system and manage your account and balance.', 120, 'Same day', '/uploads/services/abu-dhabi-salik.jpg',
         'Abu Dhabi uses the Darb toll system on selected roads. We help register your vehicle, link your account and keep your balance topped up.',
         ['Darb account registration', 'Vehicle linking', 'Balance top-up guidance', 'Toll fine enquiry'],
         ['Active Darb account'],
         ['Vehicle registration card (Mulkiya)', 'Emirates ID', 'Valid mobile number and email'],
         $faqGen('Is Darb different from Dubai Salik?', 'Yes. Darb is the Abu Dhabi toll system and Salik is the Dubai toll system. Each requires its own account.'),
         'Abu Dhabi Darb Toll Registration | Account & Top-Up Help', 'Register for Abu Dhabi Darb tolls with Anchor Point. Account setup and balance help for UAE drivers.'],

        ['vehicle-insurance-registration', 'vehicle-services', 'Vehicle Insurance & Registration (Mulkiya)', 'Comprehensive & Third-Party Cover',
         'Compare and buy UAE vehicle insurance and renew your registration card (Mulkiya) with testing centre handling.', 800, 'Same day', '/uploads/services/vehicle-insurance.jpg',
         'Renewing your car registration requires valid insurance and a technical test. We compare insurers, arrange the policy and complete the registration renewal so you receive your new Mulkiya.',
         ['Insurance quotation and comparison', 'Technical test booking', 'Registration renewal submission', 'Delivery of new Mulkiya'],
         ['Insurance policy', 'Renewed vehicle registration (Mulkiya)'],
         ['Current Mulkiya', 'Driving license / Emirates ID', 'Previous insurance details'],
         $faqGen('What is the price of car insurance in the UAE?', 'Premiums depend on car value, age and driver history. The starting price shown is a service fee; we send you insurer quotations before you decide.'),
         'Vehicle Insurance & Mulkiya Renewal Dubai | Car Registration', 'Buy UAE car insurance and renew your Mulkiya with Anchor Point. Quick comparison, testing and registration service.'],

        ['vat-tax-registration', 'banking-legal', 'VAT & Corporate Tax Registration', 'FTA Compliance Support',
         'Register for UAE VAT and Corporate Tax with the Federal Tax Authority and stay compliant with filing deadlines.', 1500, '3–7 business days', '/uploads/services/vat-tax-registration.jpg',
         'UAE businesses above the mandatory thresholds must register for VAT and Corporate Tax. We handle FTA registration, TRN issuance and ongoing filing support so you avoid penalties.',
         ['VAT registration and TRN certificate', 'Corporate Tax registration', 'Return filing guidance and reminders', 'De-registration when required'],
         ['VAT certificate / TRN', 'Corporate Tax registration number'],
         ['Trade license and MOA', 'Passport and Emirates ID of owner', 'Financial statements / turnover details', 'Bank account details'],
         $faqGen('When must I register for VAT?', 'Mandatory VAT registration applies when taxable supplies exceed AED 375,000 in 12 months; voluntary registration is possible above AED 187,500.'),
         'VAT & Corporate Tax Registration UAE | FTA Compliance Dubai', 'Register for UAE VAT and Corporate Tax with Anchor Point. FTA registration and filing support for Dubai businesses.'],

        ['corporate-legal-support', 'banking-legal', 'Corporate Legal Support & Contract Advisory', 'Contracts, MOAs & Disputes',
         'Draft and review commercial contracts, MOAs, agreements and get legal guidance for your UAE business.', 1500, 'Custom timeline', '/uploads/services/corporate-legal-support.jpg',
         'Strong contracts protect your business. We coordinate with licensed UAE legal advisers to draft and review agreements, shareholder arrangements and commercial documents.',
         ['Contract drafting and review', 'MOA and shareholder agreements', 'Commercial dispute guidance', 'Legal translation and notarisation'],
         ['Drafted / reviewed legal documents', 'Legal advisory notes'],
         ['Details of the parties', 'Draft or existing agreements', 'Summary of the matter or requirements'],
         $faqGen('Do you provide courtroom representation?', 'We coordinate with licensed legal partners for court and arbitration matters when needed.'),
         'Corporate Legal Support Dubai | Contract Drafting & Advisory', 'Corporate legal support in Dubai: contract drafting, MOAs, shareholder agreements and advisory with Anchor Point partners.'],

        ['will-preparation', 'banking-legal', 'Will Preparation & Registration (DIFC / Dubai Courts)', 'Protect Your Family & Assets',
         'Prepare and register a UAE will for expats to protect your family and assets under your own wishes.', 2500, '3–7 business days', '/uploads/services/will-preparation.jpg',
         'Expats living in the UAE can register a will so assets are distributed as they wish. We guide you through drafting and registration with the DIFC Wills Service or Dubai Courts.',
         ['Will drafting guidance', 'Guardianship and asset instructions', 'Registration with DIFC / Dubai Courts', 'Certified copies'],
         ['Registered will', 'Certified copies'],
         ['Passport and Emirates ID', 'List of assets and beneficiaries', 'Guardian details for minor children'],
         $faqGen('Is a will recognised for non-Muslim expats?', 'Yes, a registered will lets non-Muslim expats choose how their UAE assets are distributed, subject to registration rules.'),
         'Will Registration Dubai | DIFC Wills & Dubai Courts for Expats', 'Prepare and register a UAE will in Dubai. Anchor Point guides expats through DIFC and Dubai Courts will registration.'],

        ['business-email-setup', 'banking-legal', 'Business Email & Domain Setup', 'Professional @yourcompany.ae',
         'Register your domain and set up professional business email with secure cloud workspace for your team.', 350, 'Same day', '/uploads/services/business-email-setup.jpg',
         'A professional email address builds trust with clients and banks. We register your domain and configure secure business email, spam protection and mobile access for your team.',
         ['Domain registration (.ae, .com)', 'Business email accounts and aliases', 'SPF, DKIM and spam protection', 'Phone and Outlook configuration'],
         ['Registered domain', 'Configured business email accounts'],
         ['Company name and preferred domain', 'List of required email users', 'Trade license copy for .ae domains'],
         $faqGen('Can I use my own domain for business email?', 'Yes. We can connect any domain you own or register a new one for you.'),
         'Business Email Setup UAE | Domain & Professional Email', 'Set up professional business email and your domain in the UAE with Anchor Point. Fast, secure and fully configured.'],

        ['website-development', 'banking-legal', 'Corporate Website Development & Design', 'UI/UX + SEO-Ready',
         'A fast, responsive and SEO-ready corporate website that presents your Dubai business professionally.', 2500, '7–14 business days', '/uploads/services/website-development.jpg',
         'Your website is your digital office. We design and build modern, mobile-friendly websites with on-page SEO, contact forms and WhatsApp integration so clients can reach you instantly.',
         ['Custom responsive design', 'On-page SEO and fast loading', 'Contact and WhatsApp integration', 'Hosting and domain setup assistance'],
         ['Live corporate website', 'Admin access and handover guide'],
         ['Logo and brand colours', 'Company profile and services text', 'Photos or preferred references'],
         $faqGen('Can I edit the website myself later?', 'Yes. We deliver an easy-to-use admin area or train your team so you can update content without developers.'),
         'Website Development Dubai | Corporate Website Design & SEO', 'Corporate website design and development in Dubai. Responsive, SEO-ready websites built by Anchor Point.'],
    ];

    $selSvc = $db->prepare('SELECT id FROM services WHERE slug = ? LIMIT 1');
    $updSvc = $db->prepare('UPDATE services SET category_id = ?, sort_order = ?, featured_image = ?, is_active = 1 WHERE slug = ?');
    $insSvc = $db->prepare('
        INSERT INTO services (category_id, title, slug, badge, short_desc, content, featured_image, price_starting_aed, timeline,
                              deliverables_json, requirements_json, faq_json, sort_order, is_featured, is_active, seo_title, seo_desc)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)
    ');
    $json = static fn($v): string => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Display order per category (existing + new interleaved the way the previous website listed them)
    $order = [
        'new-trade-license', 'freezone-company-setup', 'renewal-trade-license', 'company-file-update', 'modification-of-quota', 'company-liquidation',
        'investor-partner-visa', 'employment-visa', 'family-visa', 'freelance-visa', 'golden-visa-services', 'emirates-id-typing',
        'pro-services', 'local-agent-service', 'dm-trade-licensing', 'rta-trade-licensing', 'sira-trade-licensing', 'police-clearance-certificate', 'dubai-police-lost-item', 'power-of-attorney', 'establishment-card',
        'driving-license-renewal', 'dubai-salik', 'abu-dhabi-salik', 'vehicle-insurance-registration',
        'corporate-bank-account', 'vat-tax-registration', 'corporate-legal-support', 'will-preparation', 'business-email-setup', 'website-development',
    ];
    $sortOf   = array_flip($order);
    $featured = ['new-trade-license', 'freezone-company-setup', 'investor-partner-visa', 'golden-visa-services', 'pro-services', 'driving-license-renewal', 'corporate-bank-account', 'vat-tax-registration'];

    foreach ($existing as $slug => [$catSlug, $img]) {
        $updSvc->execute([$catId[$catSlug], ($sortOf[$slug] ?? 99) + 1, $img, $slug]);
    }

    foreach ($new as [$slug, $catSlug, $title, $badge, $short, $price, $timeline, $img, $overview, $covers, $deliv, $reqs, $faq, $seoT, $seoD]) {
        $content = '<p>' . htmlspecialchars($overview, ENT_QUOTES, 'UTF-8') . '</p>'
                 . '<h3>What Our Service Covers</h3><ul>'
                 . implode('', array_map(static fn($c) => '<li>' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '</li>', $covers))
                 . '</ul>' . $steps;
        $selSvc->execute([$slug]);
        if ($selSvc->fetch()) {
            $updSvc->execute([$catId[$catSlug], ($sortOf[$slug] ?? 99) + 1, $img, $slug]);
            continue;
        }
        $insSvc->execute([
            $catId[$catSlug], $title, $slug, $badge, $short, $content, $img, $price, $timeline,
            $json($deliv), $json($reqs), $json($faq), ($sortOf[$slug] ?? 99) + 1,
            in_array($slug, $featured, true) ? 1 : 0, $seoT, $seoD,
        ]);
    }

    // Make sure the 10 original services appear in the featured list correctly
    $db->exec('UPDATE services SET is_featured = 0');
    $mark = $db->prepare('UPDATE services SET is_featured = 1 WHERE slug = ?');
    foreach ($featured as $f) {
        $mark->execute([$f]);
    }

    // Flag as migrated
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $db->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('business_setup_v2', '1')")->execute();
    } else {
        $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('business_setup_v2', '1') ON DUPLICATE KEY UPDATE setting_value = '1'")->execute();
    }
}
