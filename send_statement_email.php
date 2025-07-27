<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

session_start();
require_once('../config/db.php');

$membership_no = $_GET['membership_no'] ?? '';
$stmt = $conn->prepare("SELECT email, full_name FROM members WHERE membership_no = ?");
$stmt->bind_param("s", $membership_no);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

$member_email = $member['email'] ?? '';
$member_name = $member['full_name'] ?? '';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Your Gmail
    $mail->Password = 'your_app_password';    // App Password (NOT Gmail password)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'SACCO System');
    $mail->addAddress('voiceofvisionsacco@gmail.com');
    $mail->addAddress($member_email, $member_name);

    $mail->isHTML(true);
    $mail->Subject = "Statement for Member $membership_no";
    $mail->Body = "<p>Dear $member_name,</p><p>Find your statement attached or in the system.</p>";

    $mail->send();
    echo "✅ Email sent to $member_email and SACCO.";
} catch (Exception $e) {
    echo "❌ Mail Error: {$mail->ErrorInfo}";
}
?>
