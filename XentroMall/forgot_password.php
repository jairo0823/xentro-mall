<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require 'config.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } else {
        // Check if email exists in users table
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a secure token
            $token = bin2hex(random_bytes(16));
            $user_id = $user['id'];

            // Insert or update token in password_resets table
            $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, created_at) VALUES (:user_id, :token, NOW()) ON DUPLICATE KEY UPDATE token = :token2, created_at = NOW()');
            $stmt->execute(['user_id' => $user_id, 'token' => $token, 'token2' => $token]);

            // Send email with reset link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

            $to = $email;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password:\n\n$reset_link\n\nIf you did not request this, please ignore this email.";
            $headers = "From: jaigalac@gmail.com\r\n";

            // Use PHPMailer to send email via SMTP
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer();
            try {
                //Server settings
                $mail->isSMTP();
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = 'html';
                $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth   = true;
                $mail->Username   = 'jairopogirobiso@gmail.com'; // SMTP username
                $mail->Password   = 'wedi stuc gbbz qisl'; // SMTP password or app password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('jairopogirobiso@gmail.com', 'Xentro Mall');
                $mail->addAddress($to);

                // Content
                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body    = $message;

                $mail->send();
                $success_message = 'If this email is registered, a password reset link has been sent.';
            } catch (Exception $e) {
                $error_message = "Failed to send password reset email. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $success_message = 'If this email is registered, a password reset link has been sent.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Forgot Password - TMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('img/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: rgba(30, 30, 30, 0.85);
            border: none;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }
        .form-control,
        .form-control:focus {
            background-color: rgba(44, 44, 44, 0.85);
            color: #fff;
            border: 1px solid #444;
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .btn-info {
            background-color: #0dcaf0;
            border: none;
        }
        .btn-info:hover {
            background-color: #31d2f2;
        }
        a {
            color: #0dcaf0;
        }
        a:hover {
            color: #31d2f2;
        }
        h2 {
            margin-bottom: 1rem;
            color: #0dcaf0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Forgot Password</h2>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post" action="forgot_password.php">
            <div class="mb-3">
                <label for="email" class="form-label">Enter your email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
            </div>
            <button type="submit" class="btn btn-info w-100">Send Reset Link</button>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
