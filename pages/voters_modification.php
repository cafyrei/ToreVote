<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$id_number = $_GET['edit'];

$stmt = "SELECT * FROM user_information WHERE id_number = ?";
$stmt = $conn->prepare($stmt);
$stmt->bind_param("i", $id_number);
$stmt->execute();
$results = $stmt->get_result();
$result = $results->fetch_assoc();

if (isset($_POST['save'])) {
    $id = intval($result['id_number']);
    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password = $result['password'];
    }
    $hasVoted = intval($_POST['hasVoted']);

    $updateStmt = $conn->prepare("UPDATE user_information SET first_name = ?, middle_name = ?, last_name = ?, gender = ?, email = ?, username = ?, password = ?, hasVoted = ? WHERE id_number = ?");
    $updateStmt->bind_param("sssssssii", $first, $middle, $last, $gender, $email, $username, $password, $hasVoted, $id);
    $updateStmt->execute();

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

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <h3 class="logo">Administrator</h3>
        <nav>
                <a href="./dashboard.php">Dashboard</a>
                <a href="./partylist_maintenance.php">Partylist Maintenance</a>
                <a href="./position_maintenance.php">Position Maintenance</a>
                <a href="./add-candidates.php">Candidate Maintenance</a>
                <a href="./voters_maintenance.php" class="active">Voters Maintenance</a>
                <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
            </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Update Voter's Information</h1>
      </header>
      <form method="POST">
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
            <th>Action</th>
        </tr>
            <tr>
                <td><?=$result['id_number']?></td>
                <td><input type="text" name="first_name" value="<?=htmlspecialchars($result['first_name'])?>"></td>
                <td><input type="text" name="middle_name" value="<?=htmlspecialchars($result['middle_name'])?>"></td>
                <td><input type="text" name="last_name" value="<?=htmlspecialchars($result['last_name'])?>"></td>
                <td><input type="text" name="gender" value="<?=htmlspecialchars($result['gender'])?>"></td>
                <td><input type="text" name="email" value="<?=htmlspecialchars($result['email'])?>"></td>
                <td><input type="text" name="username" value="<?=htmlspecialchars($result['username'])?>"></td>
                <td><input type="text" name="password" value="<?=htmlspecialchars($result['password'])?>"></td>
                <td>
                    <select name="hasVoted">
                        <option value="1" <?= $result['hasVoted'] ? 'selected' : '' ?>>Voted</option>
                        <option value="0" <?= !$result['hasVoted'] ? 'selected' : '' ?>>Not Voted</option>
                    </select>
                </td>
                <td>
                <a href="#" class="save-btn" data-toggle="modal" data-target="#saveModal">Save</a> |
                <a href="#" class="revert-btn" data-id="<?=$result['id_number']?>" data-toggle="modal" data-target="#revertModal">Revert</a>

                </td>
            </tr>
      </table>

      <!-- SAVE MODAL -->
      <div class="modal fade" id="saveModal" tabindex="-1" role="dialog" aria-labelledby="saveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Save</h5>
      </div>
      <div class="modal-body">
        Are you sure you want to save current changes?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" href="./voters_maintenance.php" name='save'  class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
      </form>
      <!-- Modal -->
       <!-- REVERT MODAL -->
        <div class="modal fade" id="revertModal" tabindex="-1" role="dialog" aria-labelledby="revertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Revert Changes</h5>
      </div>
      <div class="modal-body">
        Are you sure you want to revert changes?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a id="confirmRevertBtn" href="./voters_maintenance.php" class="btn btn-danger">Revert</a>
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

  document.addEventListener("DOMContentLoaded", function () {
  const confirmSaveBtn = document.getElementById("confirmSaveBtn");
  const confirmRevertBtn = document.getElementById("confirmRevertBtn");

  const revertButtons = document.querySelectorAll(".revert-btn");
  revertButtons.forEach(button => {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      confirmRevertBtn.setAttribute("href", "?revert=" + id);
    });
  });
});

    </script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>