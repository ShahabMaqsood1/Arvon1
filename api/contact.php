<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

$conn = getDBConnection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$name = sanitize($data['name'] ?? '');
$email = sanitize($data['email'] ?? '');
$phone = sanitize($data['phone'] ?? '');
$subject = sanitize($data['subject'] ?? '');
$message = sanitize($data['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    jsonResponse(['success' => false, 'message' => 'All required fields must be filled'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

if ($stmt->execute()) {
    // Send email notification to admin
    $to = ADMIN_EMAIL;
    $email_subject = "New Contact Form Submission: " . $subject;
    
    // HTML email body
    $email_body = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <h2 style='color: #8B1538;'>New Contact Form Submission</h2>
        <table style='width: 100%; border-collapse: collapse;'>
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;'>Name:</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>$name</td>
            </tr>
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;'>Email:</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>$email</td>
            </tr>
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;'>Phone:</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>$phone</td>
            </tr>
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;'>Subject:</td>
                <td style='padding: 10px; border-bottom: 1px solid #ddd;'>$subject</td>
            </tr>
            <tr>
                <td style='padding: 10px; font-weight: bold; vertical-align: top;'>Message:</td>
                <td style='padding: 10px;'>" . nl2br($message) . "</td>
            </tr>
        </table>
        <p style='margin-top: 20px; color: #666; font-size: 12px;'>
            This message was sent from the ARVON contact form.<br>
            Click 'Reply' to respond directly to the sender.
        </p>
    </body>
    </html>";
    
    // Send email FROM no-reply@arvon.pk TO info@arvon.pk with Reply-To set to user's email
    sendEmail($to, $email_subject, $email_body, true, $email, $name);
    
    $stmt->close();
    $conn->close();
    jsonResponse(['success' => true, 'message' => 'Message sent successfully']);
} else {
    $stmt->close();
    $conn->close();
    jsonResponse(['success' => false, 'message' => 'Failed to send message'], 500);
}
?>
