<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

ensurePasswordResetTable();

$message = '';
$tokenValue = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email) {
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $cleanup = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $cleanup->execute(['email' => $user['email']]);

            $tokenValue = bin2hex(random_bytes(16));
            $tokenHash = password_hash($tokenValue, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO password_resets (user_id, email, token_hash, expires_at) VALUES (:user_id, :email, :token_hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $insert->execute([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'token_hash' => $tokenHash
            ]);

            $resetLink = 'reset_password.php?email=' . urlencode($user['email']) . '&token=' . urlencode($tokenValue);
            $message = 'Reset token created. Use the token below or the reset link.';
        } else {
            $message = 'If that email exists, a reset link will be generated.';
        }
    } else {
        $message = 'Please enter your email address.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
        .message {
            text-align: center;
            margin-bottom: 10px;
        }
        .token-box {
            background: #f7f7f7;
            border: 1px dashed #bbb;
            padding: 10px;
            word-break: break-all;
            font-family: monospace;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Forgot Password</h2>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <?php if ($tokenValue): ?>
        <h4>Reset Token (dev only)</h4>
        <div class="token-box"><?php echo htmlspecialchars($tokenValue); ?></div>
        <p style="margin-top:10px;"><a href="<?php echo htmlspecialchars($resetLink); ?>">Open reset page</a></p>
    <?php endif; ?>

    <p style="margin-top:10px;"><a href="login.php">Back to login</a></p>
</div>

</body>
</html>
