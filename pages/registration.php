<?php
include("../database/connection.php");

/** @var mysqli $conn */

// List of expected fields consider it null pag wala empty
$fields = [
  "first_name" => "",
  "last_name" => "",
  "middle_name" => "",
  "gender" => "",
  "username" => "",
  "email" => "",
  "password" => "",
  "confirm_password" => ""
];

$errors = [];

$hasVoted = false;

foreach ($fields as $key => $_) {
  $fields[$key] = trim($_POST[$key] ?? '');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Required fields must match
  $required = ["first_name", "last_name", "username", "email", "password", "confirm_password", "gender"];

  foreach ($required as $field) {
    if (empty($fields[$field])) {
      $errors[$field] = ucwords(str_replace("_", " ", $field)) . " is required.";
    }
  }

  if (!isset($errors['email']) && !str_ends_with($fields['email'], '@fit.edu.ph')) {
    $errors['email'] = "Email must end with @fit.edu.ph";
  }

  if (!isset($errors['password']) && !isset($errors['confirm_password'])) {
    if ($fields['password'] !== $fields['confirm_password']) {
      $errors['confirm_password'] = "Passwords do not match.";
    }
    $hashed_password = password_hash($fields["password"], PASSWORD_DEFAULT);
  }

  if (!$errors) {
    $stmt = $conn->prepare("INSERT INTO `user_information`(`first_name`, `last_name`, `middle_name`, `gender`, `email`, `username`, `password`, `role`, `hasVoted`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $admin_emails = ['admin@votingsys.com', 'admin@VTsyssite.com'];
    $role = in_array($fields['email'], $admin_emails) ? 'admin' : 'user';



    $stmt->bind_param(
      "sssssssss",
      $fields["first_name"],
      $fields["last_name"],
      $fields["middle_name"],
      $fields["gender"],
      $fields["email"],
      $fields["username"],
      $hashed_password,
      $role,
      $hasVoted,
    );

    if ($stmt->execute()) {
      $success_msg = "Account Successfully Created";

      session_start();
      session_regenerate_id(true);

      $_SESSION['username'] = $fields['username'];
      $_SESSION['email'] = $fields['email'];
      $_SESSION['id_number'] = $fields['id_number'];
      $_SESSION["hasVoted"] = $hasVoted;
      $_SESSION["role"] = $role;

      $fields = array_map(fn() => '', $fields);
      header('location: vote.php');
      exit();
    } else {
      $errors['db'] = "Database error: " . $stmt->error;
    }

    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../styles/regis-style.css" />
  <title>Sign Up | Voting System</title>
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>
  <div class="regis-wrapper">
    <div class="form-box">
      <h2>Create Your Account</h2>
      <p class="subhead">Be part of the voting system now</p>

      <form id="registration-form" method="POST">

        <div class="row">
          <div class="input-group">
            <?php if (!empty($errors['first_name'])): ?>
              <p class="error"><?= $errors['first_name'] ?></p>
            <?php endif; ?>
            <label for="first_name">First Name*</label>
            <input type="text" id="first_name" name="first_name" />

          </div>
          <div class="input-group">
            <?php if (!empty($errors['last_name'])): ?>
              <p class="error"><?= $errors['last_name'] ?></p>
            <?php endif; ?>
            <label for="last_name">Last Name*</label>
            <input type="text" id="last_name" name="last_name" />
          </div>
        </div>

        <div class="row">
          <div class="input-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" />
          </div>
          <div class="input-group">
            <?php if (!empty($errors['gender'])): ?>
              <p class="error"><?= $errors['gender'] ?></p>
            <?php endif; ?>
            <label for="gender">Gender</label>
            <select id="gender" name="gender">
              <option value="">Select</option>
              <option <?= $fields['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
              <option <?= $fields['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
              <option <?= $fields['gender'] === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
            </select>

          </div>
        </div>

        <div class="input-group full-width">
          <?php if (!empty($errors['username'])): ?>
            <p class="error"><?= $errors['username'] ?></p>
          <?php endif; ?>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" />
        </div>

        <div class="input-group full-width">
          <?php if (!empty($errors['email'])): ?>
            <p class="error"><?= $errors['email'] ?></p>
          <?php endif; ?>
          <label for="email">Student Email</label>
          <input type="email" id="email" name="email" />
        </div>

        <div class="row">
          <div class="input-group">
            <?php if (!empty($errors['password'])): ?>
              <p class="error"><?= $errors['password'] ?></p>
            <?php endif; ?>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" />
          </div>
          <div class="input-group">
            <?php if (!empty($errors['confirm_password'])): ?>
              <p class="error"><?= $errors['confirm_password'] ?></p>
            <?php endif; ?>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" />
          </div>
        </div>
        <!-- SUBMIT BUTTON -->
        <button type="submit">Sign Up</button>

        <?php if (!empty($success_msg)): ?>
          <p class="success"><?= $success_msg ?></p>
        <?php endif; ?>
      </form>
    </div>
  </div>
</body>

</html>