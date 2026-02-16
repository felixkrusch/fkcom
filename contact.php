<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$projectType = trim($_POST['projectType'] ?? '');
$description = trim($_POST['description'] ?? '');
$nonce = trim($_POST['nonce'] ?? '');
$honeypot = trim($_POST['website'] ?? ''); // Honeypot field

// Validate required fields
if (empty($name) || empty($email) || empty($projectType) || empty($description) || empty($nonce)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Spam protection: Honeypot
if (!empty($honeypot)) {
    echo json_encode(['success' => false, 'message' => 'Spam detected']);
    exit;
}

// Basic proof-of-work: Check nonce length (simple check)
if (strlen($nonce) < 10) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Email configuration
$to = 'fk@felixkrusch.com';
$subject = 'New Contact Form Submission';
$message = "
Name: $name
Email: $email
Project Type: $projectType
Description: $description
";

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>