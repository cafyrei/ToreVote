<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

if (isset($_POST['add'])) {
    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hasVoted = 0;

    $stmt = "INSERT INTO `user_information`(`first_name`, `last_name`, `middle_name`, `gender`, `email`, `username`, `password`, `hasVoted`, `date_created`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $addStmt = $conn->prepare($stmt);
    $addStmt->bind_param("sssssssi", $first, $last, $middle, $gender, $email, $username, $password, $hasVoted);
    $addStmt->execute();

    header("Location: voters_maintenance.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Voting Dashboard</title>
  <link rel="stylesheet" href="../styles/voters_modification-style.css" />
  <link rel="stylesheet" href="../styles/add_voter-style.css" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../styles/voters_modification-style.css" />
    <link rel="stylesheet" href="../styles/add_voter-style.css" />
    <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <img src="../img/logo2.png" alt="VotingSys Logo" style="width: 80px; height: auto; display: block; margin: 0 auto;" />
      <h5 class="admin" style="margin-top: 20px; text-align: center;">Administrator</h5>
        <nav>
                <a href="./dashboard.php">Dashboard</a>
                <a href="./partylist_maintenance.php">Partylist Maintenance</a>
                <a href="./position_maintenance.php">Position Maintenance</a>
                <a href="./candidates_maintenance.php">Candidate Maintenance</a>
                <a href="./voters_maintenance.php" class="active">Voters Maintenance</a>
                <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                <a href="./clear-db.php">Clear Database</a>
            </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Add Voter</h1>
        <hr style="margin: 10px 0; border-top: 4px solid #1e3a8a;" />
      </header>
 <div class="regis-wrapper">
    <div class="form-box">
      <form id="registration-form" method="POST">

        <div class="row">
          <div class="input-group">
            <?php if (!empty($errors['first_name'])): ?>
              <p class="error"><?= $errors['first_name'] ?></p>
            <?php endif; ?>
            <label for="first_name">First Name*</label>
            <input type="text" id="first_name" name="first_name" required/>

          </div>
          <div class="input-group">
            <?php if (!empty($errors['last_name'])): ?>
              <p class="error"><?= $errors['last_name'] ?></p>
            <?php endif; ?>
            <label for="last_name">Last Name*</label>
            <input type="text" id="last_name" name="last_name" required/>
          </div>
        </div>

        <div class="row">
          <div class="input-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" required/>
          </div>
          <div class="input-group">
            <?php if (!empty($errors['gender'])): ?>
              <p class="error"><?= $errors['gender'] ?></p>
            <?php endif; ?>
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
              <option value="">Select</option>
              <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
              <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
              <option value="Prefer not to say" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Prefer not to say') ? 'selected' : '' ?>>Prefer not to say</option>
            </select>

          </div>
        </div>
        <div class="row">
          <div class="input-group full-width">
          <?php if (!empty($errors['username'])): ?>
            <p class="error"><?= $errors['username'] ?></p>
          <?php endif; ?>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required/>
        </div>

        <div class="input-group full-width">
          <?php if (!empty($errors['email'])): ?>
            <p class="error"><?= $errors['email'] ?></p>
          <?php endif; ?>
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required/>
        </div>
        </div>

        <div class="row">
          <div class="input-group">
            <?php if (!empty($errors['password'])): ?>
              <p class="error"><?= $errors['password'] ?></p>
            <?php endif; ?>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required/>
          </div>
        </div>
        <!-- SUBMIT BUTTON -->
        <button type="submit" name="add">Add Voter</button>
      </form>
    </div>
  </div>
      <!-- Modal -->
      <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="logoutModalLabel" style="color: black">Confirm Logout</h5>
            </div>
            <div class="modal-body" style="color: black">
              Are you sure you want to logout?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="./admin-logout.php" class="btn btn-primary">Yes, Logout</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>