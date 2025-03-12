<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["message"])) {
        echo json_encode(["status" => "error", "message" => "Please fill in all required fields."]);
        exit();
    }

    // Validate email format
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit();
    }

    // Sanitize user inputs
    $name = htmlspecialchars(strip_tags($_POST["name"]));
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $phone = !empty($_POST["phone"]) ? htmlspecialchars(strip_tags($_POST["phone"])) : "N/A";
    $message = htmlspecialchars(strip_tags($_POST["message"]));

    // Prevent email header injection
    if (preg_match('/[\r\n]/', $email)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit();
    }

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP Server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com'; // Replace with your Gmail address
        $mail->Password   = 'your-app-password'; // Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Headers & Recipients
        $mail->setFrom('your-email@gmail.com', 'Your Website Name');
        $mail->addAddress('wantadmarketing@gmail.com'); // Recipient Email
        $mail->addReplyTo($email, $name); // Allow user to reply back

        // Email Content
        $mail->Subject = "New Contact Form Submission";
        $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";

        // Send Email
        if ($mail->send()) {
            echo json_encode(["status" => "success", "message" => "Your message has been sent successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send message."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
