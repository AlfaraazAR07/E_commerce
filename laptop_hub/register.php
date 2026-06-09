<?php
include "includes/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $phone, $address]);
        $success = "Registration successful. You can login now.";
    } catch (PDOException $e) {
        $error = "Email already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Register</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        .box {
            width: 90%;
            max-width: 450px;
            margin: 70px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            background: #222;
            color: white;
            border: none;
        }

        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<div class="box">
    <h2>User Register</h2>

    <p class="error"><?php echo $error; ?></p>
    <p class="success"><?php echo $success; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="text" name="phone" placeholder="Enter Phone">
        <textarea name="address" placeholder="Enter Address"></textarea>
        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login</a></p>
</div>

</body>
</html>