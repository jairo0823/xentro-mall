<?php
session_start();
require 'config.php';

// Redirect logged-in users to their dashboards
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'tenant') {
        header('Location: tenant_dashboard.php');
        exit;
    }
}

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $input = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($input) || empty($password)) {
        $login_error = 'Please fill all required fields.';
    } else {
        // Check admins table
        $stmt = $pdo->prepare('SELECT id, username, password, "admin" as role FROM admins WHERE username = :input1 OR email = :input2');
        $stmt->execute(['input1' => $input, 'input2' => $input]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE (username = :input1 OR email = :input2) AND role = :role');
            $stmt->execute(['input1' => $input, 'input2' => $input, 'role' => 'tenant']);
            $tenant = $stmt->fetch();

            if ($tenant && password_verify($password, $tenant['password'])) {
                $_SESSION['user_id'] = $tenant['id'];
                $_SESSION['username'] = $tenant['username'];
                $_SESSION['role'] = 'tenant';
                header('Location: tenant_dashboard.php');
                exit;
            } else {
                $login_error = 'Invalid username/email or password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - TMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      z-index: 0;
    }

    .card {
      background-color: rgba(30, 30, 30, 0.85);
      border: none;
      border-radius: 1rem;
      position: relative;
      z-index: 1;
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

    .logo-text {
      font-size: 2rem;
      font-weight: bold;
      color: #0dcaf0;
    }

    .login-image {
      object-fit: cover;
      height: 100vh;
      border-top-right-radius: 1rem;
      border-bottom-right-radius: 1rem;
    }

    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>

<div class="container login-container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
  <div class="row w-100 justify-content-center">
    <div class="col-lg-6 d-none d-lg-block p-0">
      <img src="img/bg.jpg"
           alt="Login visual" class="img-fluid login-image">
    </div>
    <div class="col-lg-6 d-flex align-items-center justify-content-center">
      <div class="card p-4 w-100" style="max-width: 420px;">

        <div class="text-center mb-4">
          <div class="logo-text">TMS</div>
         
        </div>

        <?php if (!empty($login_error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username or Email" required>
            <label for="username">Username or Email</label>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
          </div>

          <button type="submit" name="login_submit" class="btn btn-info w-100 mb-3">Log In</button>

          <div class="d-flex justify-content-between small">
            <a href="forgot_password.php" class="">Forgot password?</a>
            <span>Don't have an account? <a href="register.php">Register here</a></span>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
