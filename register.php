<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $stmt = $pdo->prepare("INSERT INTO Users (username, password) VALUES (?, ?)");
  $stmt->execute([$username, $password]);
  header('Location: login.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <title>Rejestracja</title>
</head>

<body>
  <h1>Rejestracja</h1>
  <form method="POST">
    <label>Nazwa użytkownika:</label>
    <input type="text" name="username" required><br>
    <label>Hasło:</label>
    <input type="password" name="password" required><br>
    <button type="submit">Zarejestruj</button>
  </form>
</body>

</html>