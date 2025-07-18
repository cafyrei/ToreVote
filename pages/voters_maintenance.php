<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $deleteStmt = $conn->prepare("DELETE FROM user_information WHERE id_number = ?");
  $deleteStmt->bind_param("i", $id);
  $deleteStmt->execute();
  header("Location: voters_maintenance.php");
  exit();
}

if (isset($_POST['search'])) {
  $searchq = trim(strtolower(str_replace(' ', '', $_POST['search'])));
  $searchq = "%$searchq%";
  $query = "SELECT * FROM user_information WHERE REPLACE(LOWER(first_name), ' ', '') LIKE ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $searchq);
} else {
  $query = "SELECT * FROM user_information";
  $stmt = $conn->prepare($query);
}

$stmt->execute();
$results = $stmt->get_result();
$rows = [];

while ($result = $results->fetch_assoc()) {
  $rows[] = $result;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Voting Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../styles/voters_maintenance-style.css" />
  <link rel="stylesheet" href="../styles/results-style.css"/>
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
      </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1 style="font-size: 40px;">Voters' Maintenance</h1>
        <hr style="margin: 10px 0; border-top: 4px solid #1e3a8a;" />
      </header>
      <form method="POST" class="add-form mt-4" id="addPositionForm">
        <input type="text" name="search" id="position_name" placeholder="Search Voter's First Name" required />
        <button type="submit" class="btn btn-primary">Search</button>
      </form>

      <table border="0">
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Middle Name</th>
          <th>Last Name</th>
          <th>Gender</th>
          <th>Email</th>
          <th>Username</th>
          <th>Password</th>
          <th>Vote Status</th>
          <th>Date Created</th>
          <th>Action</th>
        </tr>
        <?php foreach ($rows as $row) { ?>
          <tr>
            <td><?= $row['id_number'] ?></td>
            <td><?= $row['first_name'] ?></td>
            <td><?= $row['middle_name'] ?></td>
            <td><?= $row['last_name'] ?></td>
            <td><?= $row['gender'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['password'] = substr($row['password'], 0, 11) ?></td>
            <td><span class="<?= $row['hasVoted'] ? 'text-success' : 'text-danger' ?>"><?= $row['hasVoted'] ? 'Voted' : 'Not Voted' ?></span></td>
            <td><?= $row['date_created'] ?></td>
            <td>
              <a href='voters_modification.php?edit=<?= $row['id_number'] ?>' class='edit-btn'>Modify</a> |
              <a href="#" class="delete-btn" data-id="<?= $row['id_number'] ?>" data-toggle="modal" data-target="#deleteModal">Delete</a>


            </td>
          </tr>
        <?php } ?>
      </table>
      <?php if (isset($_POST['search'])) {
        if ($results->num_rows === 0) {
          echo '<h1 class="center-text">No Results Found</h1>';
        }
        echo '<div class="center-button"><a href="./voters_maintenance.php" class="myButton">Go Back</a></div>';
      } else {
        echo '<div class="center-btn-ADD"><a href="./voters_addition.php" class="addBTN">Add Voter</a></div>';
      } ?>
      <!-- Modal -->
      <!-- DELETE MODAL -->
      <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Deletion</h5>
            </div>
            <div class="modal-body">
              Are you sure you want to delete this voter?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
          </div>
        </div>
      </div>

      <!-- LOGOUT MODAL -->
      <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
            </div>
            <div class="modal-body">
              Are you sure you want to logout?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="./admin-logout.php" class="btn btn-primary">Yes, Logout</a>
            </div>
          </div>
        </div>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          const deleteButtons = document.querySelectorAll(".delete-btn");
          const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

          deleteButtons.forEach(button => {
            button.addEventListener("click", function() {
              const id = this.getAttribute("data-id");
              confirmDeleteBtn.setAttribute("href", "?delete=" + id);
            });
          });
        });
      </script>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>