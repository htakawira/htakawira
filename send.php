<?php
// Handles the Contact Us form and emails the client's details to Ericom.
$to = 'patrick.cox@ericom.co.zw';
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
$address = clean('address', 250);
$message = mb_substr(trim((string)($_POST['message'] ?? '')), 0, 3000);

if ($company === '' || $name === '' || $address === '' || $phone === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    back('error');
}

$body = "New enquiry from the Ericom Communications website\n\n"
      . "Company Name: $company\n"
      . "Customer Name: $name\n"
      . "Email: $email\n"
      . "Phone Number: $phone\n"
      . "Address: $address\n\n"
      . "Message:\n$message\n";

$headers = "From: Ericom Website <$from>\r\n"
         . "Reply-To: $email\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($to, "Website enquiry from $name ($company)", $body, $headers);
back($ok ? 'sent' : 'error');
