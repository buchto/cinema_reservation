<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        echo "Nieprawidłowa nazwa użytkownika lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <title>Logowanie</title>
</head>

<body>
  <h1>Logowanie</h1>
  <form method="POST">
    <label>Nazwa użytkownika:</label>
    <input type="text" name="username" required><br>
    <label>Hasło:</label>
    <input type="password" name="password" required><br>
    <button type="submit">Zaloguj</button>
  </form>
  <p>Nie masz jezcze konta? <a href="register.php">Zarejestruj się</a></p>
</body>

</html>