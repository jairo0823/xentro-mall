<?php
session_start();
$login_error = '';
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Xentro Mall Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('img/bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #006400;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .navbar-custom {
            background-color: rgba(0, 100, 0, 0.8);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link,
        .navbar-custom .btn {
            color: #ffffff;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .btn:hover {
            color: #a3d9a5;
        }
        .hero-section {
            padding: 100px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            max-width: 700px;
            margin: 40px auto;
            box-shadow: 0 4px 15px rgba(0, 100, 0, 0.2);
        }
        .hero-section h1 {
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .btn-login {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        .btn-login:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: white;
        }
        .btn-register {
            background-color: #006400;
            border-color: #006400;
            color: white;
        }
        .btn-register:hover {
            background-color: #004d00;
            border-color: #004d00;
            color: white;
        }
        .login-form-container {
            max-width: 400px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 100, 0, 0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">Xentro Mall</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about-system-section">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contacts</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="tenant_register.php" class="btn btn-register px-4 py-2">Register</a>
                    <a href="login.php" class="btn btn-login px-4 py-2">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <h1>Welcome to Xentro Mall Portal</h1>
        <p>Your gateway to convenient mall services and tenant management.</p>
    </section>

    <section id="about-system-section" class="about-system-section" style="max-width: 700px; margin: 40px auto; background: #e6f4ea; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 100, 0, 0.1);">
        <h2 style="color: #006400; font-weight: 700; margin-bottom: 15px;">About the System</h2>
        <p>
            Xentro Mall Portal is a comprehensive system designed to streamline mall services and tenant management. It provides tenants with an easy way to register, submit documents, and manage their profiles. The system also facilitates maintenance requests, renewal applications, and payment tracking, ensuring efficient mall operations.
        </p>
        <p>
            The portal is built with security and user experience in mind, featuring secure login, role-based access, and responsive design for accessibility across devices.
        </p>
    </section>

  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
