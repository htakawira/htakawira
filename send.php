<?php
// Handles the Contact Us form and emails the client's details to the chosen branch.
$recipients = [
    'harare'   => 'creditcontrol@ericom.co.zw',
    'bulawayo' => 'creditcontrol@byo.ericom.co.zw',
];
$from = 'noreply@ericom.co.zw';

function back($status) {
    header('Location: contact.html?status=' . $status);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') back('error');
if (!empty($_POST['website'])) back('sent'); // spam bot trap

function clean($key, $max) {
    $v = trim((string)($_POST[$key] ?? ''));
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr($v, 0, $max);
}

$name    = clean('name', 100);
$company = clean('company', 100);
$email   = clean('email', 150);
$phone   = clean('phone', 30);
$branch  = clean('branch', 20);
$message = mb_substr(trim((string)($_POST['message'] ?? '')), 0, 3000);

if ($name === '' || $phone === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !isset($recipients[$branch])) {
    back('error');
}

$body = "New enquiry from the Ericom Communications website\n\n"
      . "Name: $name\n"
      . "Company: $company\n"
      . "Email: $email\n"
      . "Phone: $phone\n"
      . "Branch: " . ucfirst($branch) . "\n\n"
      . "Message:\n$message\n";

$headers = "From: Ericom Website <$from>\r\n"
         . "Reply-To: $email\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($recipients[$branch], "Website enquiry from $name", $body, $headers);
back($ok ? 'sent' : 'error');
