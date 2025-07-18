<?php
session_start();
include("../database/connection.php");

/** @var mysqli $conn */

$login_err = '';

if (isset($_POST['btnLogin'])) {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  $sql = "SELECT id_number, email, password, username, hasVoted FROM user_information WHERE email = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row && password_verify($pass, $row['password'])) {
    $_SESSION['email'] = $row['email'];
    $_SESSION['id_number'] = $row['id_number'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['hasVoted'] = $row['hasVoted'];
    header('Location: vote.php');
    exit();
  } else {
    $login_err = "Invalid Email or Password";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link rel="stylesheet" href="../styles/login-style.css">
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>

  <div class="video-background">
    <video autoplay muted loop playsinline>
      <source src="../img/white_wave.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>

  <div class="login-container">
    <div class="img-container">
      <img src="../img/user_logo.png" alt="Voting Logo">
    </div>
    <h2>User Login</h2>
    <form action="#" method="post">
      <?php if (!empty($login_err)): ?>
        <p class="error"><?= $login_err ?></p>
      <?php endif; ?>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <button type="submit" name="btnLogin">Login</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="./registration.php">Sign up</a></p>
  </div>
</body>

</html>