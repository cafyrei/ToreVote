<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$id_num = isset($_GET['edit']) ? $_GET['edit'] : (isset($_GET['revert']) ? $_GET['revert'] : null);


$stmt = "SELECT * FROM candidates WHERE id_num = ?";
$stmt = $conn->prepare($stmt);
$stmt->bind_param("i", $id_num);
$stmt->execute();
$results = $stmt->get_result();
$result = $results->fetch_assoc();

if (isset($_POST['save'])) {
    $id = intval($result['id_num']);
    $full_name = $_POST['candidate_name'];
    $platform = $_POST['platform'];


    $updateStmt = $conn->prepare("UPDATE candidates SET candidate_name = ?, platform = ? WHERE id_num = ?");
    $updateStmt->bind_param("ssi", $full_name, $platform, $id);
    $updateStmt->execute();

    header("Location: candidates_maintenance.php");
    exit();
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
  <link rel="stylesheet" href="../styles/voters_modification-style.css" />
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
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
                <a href="./candidates_maintenance.php">Candidate Maintenance</a>
                <a href="./voters_maintenance.php" class="active">Voters Maintenance</a>
                <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
            </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Update Candidate's Information</h1>
      </header>
      <form method="POST">
      <table border="0">
        <tr>
            <th>ID</th>
            <th>Candidate Name</th>
            <th>Platform</th>
            <th>Action</th>
        </tr>
            <tr>
                <td><?=$result['id_num']?></td>
                <td><input type="text" name="candidate_name" value="<?=htmlspecialchars($result['candidate_name'])?>"></td>
                <td><input type="text" name="platform" value="<?=htmlspecialchars($result['platform'])?>"></td>
                <td>
                <a href="#" class="save-btn" data-toggle="modal" data-target="#saveModal">Save</a> |
                <a href="#" class="revert-btn" data-toggle="modal" data-target="#revertModal">Revert</a>

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
        <button type="submit" href="./candidates_maintenance.php" name='save'  class="btn btn-primary">Save</button>
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
        <a href="./candidates_maintenance.php" class="btn btn-danger">Revert</a>
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