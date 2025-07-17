<?php
session_start();
include("../database/connection.php");

/** @var mysqli $conn */

$login_err = '';

if (isset($_POST['btnLogin'])) {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  $sql = "SELECT admin_id, admin_email, admin_password FROM admin_db WHERE admin_email = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  var_dump($row);

  $plain = "admin123";
  $hash = '$2y$10$W9ZmVEfzfWQqA83KDYxPIeHE1QUSYHxt3hQmWl0XGe5MYu1MZAjAq';

  if (password_verify($plain, $hash)) {
    echo "Password is correct!";
  } else {
    echo "Wrong password.";
  }


  if ($row && password_verify($pass, $row['admin_password'])) {
    $_SESSION['admin_id'] = $row['admin_id'];
    $_SESSION['admin_email'] = $row['admin_email'];

    header('Location: dashboard.php');
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
  <link rel="stylesheet" href="../styles/admin-style.css">
</head>

<body>

  <div class="video-background">
    <video autoplay muted loop playsinline>
      <source src="../img/admin_bg.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>

  <div class="login-container">
    <div class="img-container">
      <img src="../img/admin_logo.png" alt="Voting Logo">
    </div>
    <h2>Admin Login</h2>
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
  </div>
</body>

</html>