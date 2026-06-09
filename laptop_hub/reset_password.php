<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

ensurePasswordResetTable();

$message = '';
$success = '';
$debugInfo = null;
$email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));
$token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';

    if ($email && $token && $newPassword) {
        $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = :email AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['email' => $email]);
        $reset = $stmt->fetch();

        if ($reset && password_verify($token, $reset['token_hash'])) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute(['password' => $hash, 'id' => $reset['user_id']]);

            $cleanup = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $cleanup->execute(['email' => $email]);

            $success = 'Password updated. You can login now.';
        } else {
            $message = 'Invalid or expired token.';
            $debugStmt = $conn->prepare("SELECT id, email, expires_at, created_at FROM password_resets WHERE email = :email ORDER BY created_at DESC LIMIT 1");
            $debugStmt->execute(['email' => $email]);
            $latest = $debugStmt->fetch();
            $debugInfo = [
                'email' => $email,
                'tokenProvided' => $token ? 'yes' : 'no',
                'recordFound' => $latest ? 'yes' : 'no',
                'createdAt' => $latest['created_at'] ?? 'n/a',
                'expiresAt' => $latest['expires_at'] ?? 'n/a'
            ];
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }
        .login-box {
            width: 90%;
            max-width: 420px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
        }
        button {
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }
        .message { color: red; text-align: center; }
        .success { color: green; text-align: center; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Reset Password</h2>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if ($debugInfo): ?>
        <div style="margin:12px 0; font-size:12px; color:#444;">
            <strong>Debug:</strong>
            <div>Email: <?php echo htmlspecialchars($debugInfo['email']); ?></div>
            <div>Token provided: <?php echo htmlspecialchars($debugInfo['tokenProvided']); ?></div>
            <div>Reset record found: <?php echo htmlspecialchars($debugInfo['recordFound']); ?></div>
            <div>Latest created at: <?php echo htmlspecialchars($debugInfo['createdAt']); ?></div>
            <div>Latest expires at: <?php echo htmlspecialchars($debugInfo['expiresAt']); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
        <input type="text" name="token" placeholder="Reset Token" required value="<?php echo htmlspecialchars($token); ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit">Update Password</button>
    </form>

    <p style="margin-top:10px;"><a href="login.php">Back to login</a></p>
</div>

</body>
</html>
