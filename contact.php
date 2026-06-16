<?php
/**
 * Nderi Law & Associates — Contact Form Handler
 * Handles POST submissions, validates input, sends email, returns JSON.
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// ── Configuration ──────────────────────────────────────────
define('TO_EMAIL',   'info@nderilawassociates.co.ke');   // ← Ann's real email
define('TO_NAME',    'Nderi Law & Associates');
define('FROM_EMAIL', 'noreply@nderilawassociates.co.ke');
define('SITE_NAME',  'Nderi Law & Associates Advocates');
// ───────────────────────────────────────────────────────────

function respond($success, $message, $error = '') {
    echo json_encode(['success' => $success, 'message' => $message, 'error' => $error]);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, '', 'Method not allowed.');
}

// Honeypot anti-spam
if (!empty($_POST['website'])) {
    respond(true, 'Thank you.'); // silently pass to fool bots
}

// Sanitise & validate
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$matter  = trim(strip_tags($_POST['matter']  ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

if (empty($name) || strlen($name) < 2) {
    respond(false, '', 'Please provide your full name.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, '', 'Please provide a valid email address.');
}
if (empty($message) || strlen($message) < 10) {
    respond(false, '', 'Please include a brief description of your matter.');
}

// Build email to Ann's office
$subject = 'New Enquiry via Website' . ($matter ? ' — ' . $matter : '');

$body  = "New enquiry received via the Nderi Law website.\n\n";
$body .= "─────────────────────────────────────\n";
$body .= "Name:    {$name}\n";
$body .= "Email:   {$email}\n";
$body .= "Matter:  " . ($matter ?: 'Not specified') . "\n";
$body .= "─────────────────────────────────────\n\n";
$body .= "Message:\n{$message}\n\n";
$body .= "─────────────────────────────────────\n";
$body .= "Sent: " . date('D, d M Y H:i:s T') . "\n";

$headers  = "From: " . SITE_NAME . " <" . FROM_EMAIL . ">\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$sent = mail(TO_EMAIL, $subject, $body, $headers);

if (!$sent) {
    respond(false, '', 'Our mail server encountered an error. Please email us directly at ' . TO_EMAIL);
}

// Auto-reply to sender
$reply_subject = 'We received your enquiry — ' . SITE_NAME;
$reply_body  = "Dear {$name},\n\n";
$reply_body .= "Thank you for reaching out to Nderi Law & Associates Advocates.\n\n";
$reply_body .= "We have received your enquiry";
if ($matter) { $reply_body .= " regarding {$matter}"; }
$reply_body .= " and a member of our team will be in touch with you within 1–2 business days.\n\n";
$reply_body .= "If your matter is urgent, please call us directly:\n";
$reply_body .= "+254 700 000 000\n\n";  // ← replace with Ann's real number
$reply_body .= "Warm regards,\n";
$reply_body .= "The Team at Nderi Law & Associates Advocates\n";
$reply_body .= "Advocates of the High Court of Kenya\n";
$reply_body .= "Nairobi, Kenya\n";

$reply_headers  = "From: " . SITE_NAME . " <" . FROM_EMAIL . ">\r\n";
$reply_headers .= "MIME-Version: 1.0\r\n";
$reply_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail($email, $reply_subject, $reply_body, $reply_headers);

respond(true, 'Thank you, ' . $name . '. Your enquiry has been received.');
?>
