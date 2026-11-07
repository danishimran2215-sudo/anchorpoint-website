<?php
/**
 * Email / SMTP Mailer — Anchor Point UAE Tourism Website
 * Reads SMTP configuration dynamically from Admin Settings (Google Workspace, Zoho, SendGrid, cPanel SMTP)
 * with graceful fallback so messages never crash enquiry capture.
 */

function getSmtpConfig(): array {
    return [
        'host'       => getSetting('smtp_host', SMTP_HOST),
        'port'       => (int)getSetting('smtp_port', (string)SMTP_PORT),
        'user'       => getSetting('smtp_user', SMTP_USER),
        'pass'       => getSetting('smtp_pass', SMTP_PASS),
        'encryption' => strtolower(getSetting('smtp_encryption', SMTP_ENCRYPTION)),
        'from_email' => getSetting('smtp_from_email', getCompanyEmail()),
        'from_name'  => getSetting('smtp_from_name', getSetting('site_name', SITE_NAME)),
    ];
}

function sendMail(string $to, string $subject, string $htmlBody, string $textBody = '', string $replyTo = ''): bool {
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    if ($textBody === '') {
        $textBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</tr>', '</p>'], "\n", $htmlBody)));
    }
    $cfg = getSmtpConfig();
    if ($cfg['host'] !== '' && $cfg['user'] !== '' && $cfg['pass'] !== '') {
        return smtpSend($cfg, $to, $subject, $htmlBody, $textBody, $replyTo);
    }
    return fallbackMail($cfg, $to, $subject, $htmlBody, $textBody, $replyTo);
}

function fallbackMail(array $cfg, string $to, string $subject, string $htmlBody, string $textBody, string $replyTo): bool {
    $boundary = '----=_Part_' . md5(uniqid('', true));
    $headers  = implode("\r\n", [
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'From: ' . $cfg['from_name'] . ' <' . $cfg['from_email'] . '>',
        'Reply-To: ' . ($replyTo ?: $cfg['from_email']),
        'X-Mailer: AnchorPoint-Tourism/2.0',
    ]);
    $body = "--{$boundary}\r\n"
          . "Content-Type: text/plain; charset=UTF-8\r\n"
          . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
          . quoted_printable_encode($textBody) . "\r\n"
          . "--{$boundary}\r\n"
          . "Content-Type: text/html; charset=UTF-8\r\n"
          . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
          . quoted_printable_encode($htmlBody) . "\r\n"
          . "--{$boundary}--";
    return (bool)@mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}

function smtpSend(array $cfg, string $to, string $subject, string $htmlBody, string $textBody, string $replyTo): bool {
    try {
        $port       = $cfg['port'] ?: 587;
        $encryption = $cfg['encryption'];
        $prefix     = ($encryption === 'ssl') ? 'ssl://' : '';
        $socket     = @stream_socket_client("{$prefix}{$cfg['host']}:{$port}", $errno, $errstr, 10);
        if (!$socket) throw new RuntimeException("SMTP connect failed: $errstr ($errno)");
        stream_set_timeout($socket, 10);

        $read = function () use ($socket): string {
            $resp = '';
            while ($line = fgets($socket, 515)) {
                $resp .= $line;
                if (isset($line[3]) && $line[3] === ' ') break;
            }
            return $resp;
        };
        $send = function (string $cmd) use ($socket): void {
            fwrite($socket, $cmd . "\r\n");
        };

        $read();
        $hostName = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $send('EHLO ' . $hostName);
        $read();

        if ($encryption === 'tls') {
            $send('STARTTLS');
            $read();
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $send('EHLO ' . $hostName);
            $read();
        }

        $send('AUTH LOGIN');
        $read();
        $send(base64_encode($cfg['user']));
        $read();
        $send(base64_encode($cfg['pass']));
        $authResp = $read();
        if (!str_contains($authResp, '235')) throw new RuntimeException('SMTP AUTH failed');

        $send('MAIL FROM:<' . $cfg['from_email'] . '>');
        $read();
        $send('RCPT TO:<' . $to . '>');
        $read();
        $send('DATA');
        $read();

        $boundary = '----=_SMTPPart_' . md5(uniqid('', true));
        $msg  = 'From: ' . $cfg['from_name'] . ' <' . $cfg['from_email'] . ">\r\n";
        $msg .= "To: $to\r\n";
        $msg .= 'Reply-To: ' . ($replyTo ?: $cfg['from_email']) . "\r\n";
        $msg .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n";
        $msg .= "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$textBody\r\n";
        $msg .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$htmlBody\r\n";
        $msg .= "--$boundary--\r\n.";

        $send($msg);
        $dataResp = $read();
        $send('QUIT');
        fclose($socket);

        if (!str_contains($dataResp, '250')) throw new RuntimeException('SMTP DATA rejected');
        return true;
    } catch (Throwable $e) {
        error_log('[AnchorPoint SMTP] ' . $e->getMessage());
        return fallbackMail($cfg, $to, $subject, $htmlBody, $textBody, $replyTo);
    }
}

/**
 * Section 10 #2: Send full enquiry submission to main company email.
 * Exact required subject line: New Enquiry: [Activity] - [Customer name] - [Date]
 */
function sendEnquiryNotificationToCompany(array $enq): bool {
    $companyEmail = getCompanyEmail();
    $activity     = $enq['tour_title'] ?? 'UAE Tour';
    $custName     = $enq['customer_name'] ?? 'Guest';
    $dateStr      = $enq['booking_date'] ?? date('Y-m-d');
    $subject      = "New Enquiry: {$activity} - {$custName} - {$dateStr}";

    $fullPhone = trim(($enq['country_code'] ?? '+971') . ' ' . ($enq['customer_phone'] ?? ''));
    $waReply   = buildWhatsAppLink(
        preg_replace('/[^0-9]/', '', $fullPhone),
        "Hi {$custName}, thank you for your enquiry with Anchor Point for {$activity} on {$dateStr}! How can we finalize your booking?"
    );

    $childrenInfo = ((int)($enq['children_count'] ?? 0)) > 0
        ? ((int)$enq['children_count'] . (!empty($enq['children_ages']) ? ' (Ages: ' . $enq['children_ages'] . ')' : ''))
        : '0';

    $utmLine = trim(implode(' | ', array_filter([
        !empty($enq['utm_source']) ? 'source=' . $enq['utm_source'] : '',
        !empty($enq['utm_medium']) ? 'medium=' . $enq['utm_medium'] : '',
        !empty($enq['utm_campaign']) ? 'campaign=' . $enq['utm_campaign'] : '',
        !empty($enq['utm_term']) ? 'term=' . $enq['utm_term'] : '',
        !empty($enq['utm_content']) ? 'content=' . $enq['utm_content'] : '',
    ])));

    $rows = [
        'Reference'         => $enq['booking_ref'] ?? '',
        'Category'          => $enq['category_name'] ?? '',
        'Activity'          => $activity,
        'Full Name'         => $custName,
        'Email'             => $enq['customer_email'] ?? '',
        'Phone / WhatsApp'  => $fullPhone,
        'Preferred Date'    => $dateStr,
        'Preferred Time'    => $enq['preferred_time'] ?: 'Flexible',
        'Adults'            => (string)($enq['adults_count'] ?? 1),
        'Children'          => $childrenInfo,
        'Pick-up Location'  => $enq['pickup_location'] ?: 'N/A',
        'Nationality'       => $enq['nationality'] ?: 'Not specified',
        'Special Requests'  => $enq['special_requests'] ?: 'None',
        'Source Page'       => $enq['source_page'] ?: '/',
        'UTM Tags'          => $utmLine ?: 'Direct / Organic',
    ];

    $trs = '';
    $idx = 0;
    foreach ($rows as $label => $val) {
        $bg = ($idx++ % 2 === 1) ? '#F8FAFC' : '#FFFFFF';
        $trs .= '<tr style="background:' . $bg . '">'
              . '<td style="padding:10px 14px;border-bottom:1px solid #E2E8F0;font-weight:700;color:#334155;width:36%">' . e($label) . '</td>'
              . '<td style="padding:10px 14px;border-bottom:1px solid #E2E8F0;color:#0F172A">' . nl2br(e((string)$val)) . '</td>'
              . '</tr>';
    }

    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#F1F5F9;padding:24px;margin:0">'
          . '<div style="max-width:640px;margin:0 auto;background:#FFFFFF;border-radius:12px;overflow:hidden;border:1px solid #E2E8F0">'
          . '<div style="background:#0B2545;padding:22px 24px;color:#FFFFFF">'
          . '<h2 style="margin:0;font-size:19px">New Website Enquiry Received</h2>'
          . '<p style="margin:6px 0 0;color:#BFDBFE;font-size:14px">' . e($activity) . ' · ' . e($dateStr) . '</p>'
          . '</div>'
          . '<div style="padding:20px 24px">'
          . '<table style="width:100%;border-collapse:collapse;font-size:14px">' . $trs . '</table>'
          . '<div style="margin-top:22px;text-align:center">'
          . '<a href="' . e($waReply) . '" style="display:inline-block;background:#25D366;color:#FFFFFF;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:700;margin:4px">Reply to Customer on WhatsApp</a> '
          . '<a href="' . e(SITE_URL . '/admin/tours/bookings') . '" style="display:inline-block;background:#0063DC;color:#FFFFFF;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:700;margin:4px">Open Admin Enquiries</a>'
          . '</div></div></div></body></html>';

    return sendMail($companyEmail, $subject, $html, '', $enq['customer_email'] ?? '');
}

/**
 * Section 10 #3: Send automatic confirmation email to the customer:
 * "We received your request and will reply within 30 minutes on WhatsApp."
 */
function sendEnquiryConfirmationToCustomer(array $enq): bool {
    $to       = $enq['customer_email'] ?? '';
    $custName = $enq['customer_name'] ?? 'Guest';
    $activity = $enq['tour_title'] ?? 'your selected UAE experience';
    $dateStr  = $enq['booking_date'] ?? '';
    $waUrl    = waLinkCheckout($activity, $custName);

    $subject = 'Enquiry Received: ' . $activity . ' — ' . getSetting('site_name', SITE_NAME);

    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#F1F5F9;padding:24px;margin:0">'
          . '<div style="max-width:600px;margin:0 auto;background:#FFFFFF;border-radius:12px;overflow:hidden;border:1px solid #E2E8F0">'
          . '<div style="background:#0B2545;padding:24px;text-align:center;color:#FFFFFF">'
          . '<h2 style="margin:0;font-size:20px">' . e(getSetting('site_name', SITE_NAME)) . '</h2>'
          . '</div>'
          . '<div style="padding:28px 24px;color:#0F172A;line-height:1.65;font-size:15px">'
          . '<p style="margin-top:0">Hi <strong>' . e($custName) . '</strong>,</p>'
          . '<p style="font-size:16px;font-weight:700;color:#0B2545">We received your request and will reply within 30 minutes on WhatsApp.</p>'
          . '<div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;padding:14px 16px;margin:18px 0;font-size:14px">'
          . '<strong>Activity:</strong> ' . e($activity) . '<br>'
          . '<strong>Preferred Date:</strong> ' . e($dateStr) . '<br>'
          . '<strong>Guests:</strong> ' . (int)($enq['adults_count'] ?? 1) . ' Adult(s)'
          . (((int)($enq['children_count'] ?? 0)) > 0 ? ', ' . (int)$enq['children_count'] . ' Child(ren)' : '')
          . '</div>'
          . '<p>Want instant confirmation right now? Tap the button below to chat with our booking desk directly on WhatsApp:</p>'
          . '<div style="text-align:center;margin:24px 0">'
          . '<a href="' . e($waUrl) . '" style="display:inline-block;background:#25D366;color:#FFFFFF;padding:14px 26px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px">Complete booking on WhatsApp</a>'
          . '</div>'
          . '<p style="font-size:13px;color:#64748B;margin-bottom:0">'
          . e(getSetting('office_address', OFFICE_ADDRESS)) . '<br>'
          . 'Phone / WhatsApp: ' . e(getPrimaryPhone())
          . '</p></div></div></body></html>';

    return sendMail($to, $subject, $html);
}
