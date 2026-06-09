<?php
// Usage (CLI):
// c:\xampp\php\php.exe scripts\set_admin_password.php [email] [password]
// Example:
// c:\xampp\php\php.exe scripts\set_admin_password.php admin@lapphub.com alf123

require_once __DIR__ . '/../includes/db.php';

if (PHP_SAPI !== 'cli') {
    echo "This script is intended to be run from the command line.\n";
}

$email = $argv[1] ?? 'admin@lapphub.com';
$password = $argv[2] ?? 'alf123';

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE admins SET password = :password WHERE email = :email");
$stmt->execute(['password' => $hash, 'email' => $email]);

$updated = $stmt->rowCount();
if ($updated) {
    echo "Updated password for {$email}.\n";
} else {
    echo "No rows updated. Check that the email exists in the admins table.\n";
}

?>