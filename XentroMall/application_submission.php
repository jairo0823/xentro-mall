<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $uploadDir = 'uploads/applications/' . $userId . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $files = [
        'letter_of_intent',
        'business_profile',
        'business_registration',
        'valid_id',
        'bir_registration',
        'extended_bir_registration',
        'financial_statement'
    ];

    $uploadedFiles = [];

    foreach ($files as $file) {
        if (isset($_FILES[$file]) && $_FILES[$file]['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES[$file]['tmp_name'];
            $fileName = basename($_FILES[$file]['name']);
            $targetFile = $uploadDir . uniqid() . '_' . $fileName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedFiles[$file] = $targetFile;
            } else {
                $uploadedFiles[$file] = null;
            }
        } else {
            $uploadedFiles[$file] = null;
        }
    }

    error_log("Uploaded files: " . print_r($uploadedFiles, true));

    if ($uploadedFiles['extended_bir_registration'] === null) {
        error_log("Warning: extended_bir_registration file upload failed or missing.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO application_submissions (user_id, letter_of_intent, business_profile, business_registration, valid_id, bir_registration, extended_bir_registration, financial_statement) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $uploadedFiles['letter_of_intent'],
            $uploadedFiles['business_profile'],
            $uploadedFiles['business_registration'],
            $uploadedFiles['valid_id'],
            $uploadedFiles['bir_registration'],
            $uploadedFiles['extended_bir_registration'],
            $uploadedFiles['financial_statement']
        ]);
    } catch (PDOException $e) {
        error_log("Database insert error: " . $e->getMessage());
        error_log("SQLSTATE error code: " . $e->getCode());
        error_log("Error info: " . print_r($stmt->errorInfo(), true));
        $_SESSION['error_message'] = "There was an error submitting your application. Please try again later.";
        header("Location: application_submission.php");
        exit;
    }

    $_SESSION['success_message'] = "Application submitted successfully.";
    header("Location: tenant_dashboard.php?page=application_submission");
    exit;
}
?>
<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$existingExtendedBir = null;

try {
    $stmt = $pdo->prepare("SELECT extended_bir_registration FROM application_submissions WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $existingExtendedBir = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error fetching existing extended_bir_registration: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Application Submission</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7fafc;
            padding: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="file"] {
            margin-top: 5px;
            width: 100%;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #1E73FF;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #155db2;
        }
        .preview {
            margin-top: 10px;
            font-size: 14px;
        }
        .preview a {
            color: #1E73FF;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Application Submission</h1>
    <form action="application_submission.php" method="POST" enctype="multipart/form-data">
        <label for="letter_of_intent">Letter of Intent</label>
        <input type="file" name="letter_of_intent" id="letter_of_intent" required>

        <label for="business_profile">Business Profile</label>
        <input type="file" name="business_profile" id="business_profile" required>

        <label for="business_registration">Business Registration</label>
        <input type="file" name="business_registration" id="business_registration" required>

        <label for="valid_id">Valid ID (Image)</label>
        <input type="file" name="valid_id" id="valid_id" required>

        <label for="bir_registration">BIR Registration</label>
        <input type="file" name="bir_registration" id="bir_registration" required>

        <label for="extended_bir_registration">Extended BIR Registration</label>
        <input type="file" name="extended_bir_registration" id="extended_bir_registration" required>
        <?php if ($existingExtendedBir): ?>
            <div class="preview">
                Previous Extended BIR Registration: <a href="<?php echo htmlspecialchars($existingExtendedBir); ?>" target="_blank">View File</a>
            </div>
        <?php endif; ?>

        <label for="financial_statement">Financial Statement</label>
        <input type="file" name="financial_statement" id="financial_statement" required>

        <input type="submit" value="Submit Application">
    </form>
</body>
</html>
