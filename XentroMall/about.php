<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$stmtUser = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch();

$stmtMobile = $pdo->prepare("SELECT mobile FROM tenant_details WHERE user_id = ?");
$stmtMobile->execute([$userId]);
$tenantDetails = $stmtMobile->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>About You</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">
  <div class="max-w-xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">About You</h1>
    <?php if ($user): ?>
      <div class="bg-white p-6 rounded shadow space-y-4">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($tenantDetails['mobile'] ?? ''); ?></p>
        <p><strong>Member Since:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($user['created_at']))); ?></p>
      </div>
    <?php else: ?>
      <p>User information not found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
