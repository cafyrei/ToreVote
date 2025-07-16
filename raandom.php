<?php
session_start();
include("../database/connection.php"); 
/* Kung nakalogin magrekta dashboard na
if (isset($_SESSION['email'])) {
  header('location: dashboard.php');
  exit();
}
*/
if (isset($_POST['btnLogin'])) {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  $sql = "SELECT id_number, email, password FROM user_information WHERE email = ?";
  $stmt = $conn->prepare($sql);

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row && password_verify($pass, $row['password'])) {
    $_SESSION['email'] = $row['email'];
    header('Location: dashboard.php');
    exit();
  } else {
    echo "Invalid Email or Password";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link rel="stylesheet" href="../styles/login-style.css" />
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form action="#" method="post">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <button type="submit" name = "btnLogin" >Login</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="./registration.php">Sign up</a>
  </div>
</body>
</html>