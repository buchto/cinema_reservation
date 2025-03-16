<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$movies = $pdo->query("SELECT * FROM Movies")->fetchAll();

$selectedMovieId = $_GET['movie_id'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $movie_id = $_POST['movie_id'];
  $seat_id = $_POST['seat_id'];

  $stmt = $pdo->prepare("SELECT * FROM Reservations WHERE seat_id = ? AND movie_id = ?");
  $stmt->execute([$seat_id, $movie_id]);
  if (!$stmt->fetch()) {

    $stmt = $pdo->prepare("INSERT INTO Reservations (user_id, seat_id, movie_id) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $seat_id, $movie_id]);
  }

  header("Location: index.php?movie_id=$movie_id");
  exit();
}

$seats = $pdo->query("SELECT * FROM Seats")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <title>Rezerwacja biletów</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    margin: 20px;
  }

  .cinema-hall {
    display: grid;
    grid-template-columns: repeat(20, 30px);
    gap: 5px;
    margin-top: 20px;
  }

  .seat {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: green;
    color: white;
    border: 1px solid grey;
    cursor: pointer;
  }

  .seat.reserved {
    background-color: red;
    cursor: not-allowed;
  }

  .logout-link {
    color: red;
    text-decoration: none;
    margin-left: 20px;
  }
  </style>
</head>

<body>
  <h1>Witaj, <?php echo $_SESSION['username']; ?>!</h1>
  <a href="logout.php" class="logout-link">Wyloguj się</a>

  <form method="GET" action="index.php">
    <label for="movie_id">Wybierz film:</label>
    <select name="movie_id" id="movie_id" onchange="this.form.submit()">
      <option value="">-- Wybierz jaki film chcesz obejrzec --</option>
      <?php foreach ($movies as $movie): ?>
      <option value="<?php echo $movie['movie_id']; ?>"
        <?php echo ($selectedMovieId == $movie['movie_id']) ? 'selected' : ''; ?>>
        <?php echo $movie['title']; ?>
      </option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($selectedMovieId): ?>
  <h2>ROzkład miejsc na film
    <?php echo $movies[array_search($selectedMovieId, array_column($movies, 'movie_id'))]['title']; ?></h2>
  <div class="cinema-hall">
    <?php foreach ($seats as $seat): ?>
    <?php

        $stmt = $pdo->prepare("SELECT * FROM Reservations WHERE seat_id = ? AND movie_id = ?");
        $stmt->execute([$seat['seat_id'], $selectedMovieId]);
        $isReserved = $stmt->fetch();
        ?>
    <div class="seat <?php echo $isReserved ? 'reserved' : ''; ?>"
      onclick="reserveSeat(<?php echo $seat['seat_id']; ?>)">
      <?php echo $seat['seat_id']; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p></p>
  <?php endif; ?>

  <script>
  function reserveSeat(seatId) {
    const seatElement = document.querySelector(`.seat[onclick*="${seatId}"]`);
    if (seatElement.classList.contains('reserved')) {
      return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php';

    const seatInput = document.createElement('input');
    seatInput.type = 'hidden';
    seatInput.name = 'seat_id';
    seatInput.value = seatId;

    const movieInput = document.createElement('input');
    movieInput.type = 'hidden';
    movieInput.name = 'movie_id';
    movieInput.value = <?php echo $selectedMovieId; ?>;

    form.appendChild(seatInput);
    form.appendChild(movieInput);
    document.body.appendChild(form);
    form.submit();
  }
  </script>
</body>

</html>