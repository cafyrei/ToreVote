<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$id_num = isset($_GET['edit']) ? $_GET['edit'] : (isset($_GET['revert']) ? $_GET['revert'] : null);

$stmt = $conn->prepare("SELECT * FROM candidates WHERE id_num = ?");
$stmt->bind_param("i", $id_num);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (isset($_POST['save'])) {
  $id = intval($result['id_num']);
  $full_name = $_POST['candidate_name'];
  $platform = $_POST['platform'];

  $image = $_FILES['candidate_image']['name'] ?? '';
  $imageTmp = $_FILES['candidate_image']['tmp_name'] ?? '';
  $targetDir = "../img/";
  $finalImage = $result['photo']; // default to existing

  if (!empty($image)) {
    $targetFile = $targetDir . basename($image);
    if (move_uploaded_file($imageTmp, $targetFile)) {
      $finalImage = $image;
    }
  }

  $updateStmt = $conn->prepare("UPDATE candidates SET candidate_name = ?, platform = ?, photo = ? WHERE id_num = ?");
  $updateStmt->bind_param("sssi", $full_name, $platform, $finalImage, $id);
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
  <title>Update Candidate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../styles/candidate_modification-style.css" />
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard">
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <h3 class="logo">Administrator</h3>
      <nav>
        <a href="./dashboard.php">Dashboard</a>
        <a href="./partylist_maintenance.php">Partylist Maintenance</a>
        <a href="./position_maintenance.php">Position Maintenance</a>
        <a href="./candidates_maintenance.php" class="active">Candidate Maintenance</a>
        <a href="./voters_maintenance.php" >Voters Maintenance</a>
        <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
      </nav>
    </aside>

    <main class="main-content">
      <header class="topbar">
        <h1>Update Candidate's Information</h1>
      </header>
      <form method="POST" enctype="multipart/form-data">
        <table class="table">
          <tr>
            <th>ID</th>
            <th>Candidate Name</th>
            <th>Platform</th>
            <th>Photo</th>
            <th>Action</th>
          </tr>
          <tr>
            <td><?= $result['id_num'] ?></td>
            <td><input type="text" name="candidate_name" class="form-control" value="<?= htmlspecialchars($result['candidate_name']) ?>"></td>
            <td><input type="text" name="platform" class="form-control" value="<?= htmlspecialchars(string: $result['platform']) ?>"></td>
            <td>
              <input type="file" name="candidate_image" id="candidate_image" class="d-none" accept="image/*">
              <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('candidate_image').click()">Modify Photo</button>
            </td>

            <td>
              <button type="button" class="save-btn" data-bs-toggle="modal" data-bs-target="#saveModal">Save</button> |
              <button type="button" class="revert-btn" data-bs-toggle="modal" data-bs-target="#revertModal">Revert</button>
            </td>
          </tr>
        </table>

        <!-- SAVE MODAL -->
        <div class="modal fade" id="saveModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirm Save</h5>
              </div>
              <div class="modal-body">
                Are you sure you want to save current changes?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="save" class="btn btn-primary">Save</button>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- REVERT MODAL -->
      <div class="modal fade" id="revertModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Revert Changes</h5>
            </div>
            <div class="modal-body">
              Are you sure you want to revert changes?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="./candidates_maintenance.php" class="btn btn-danger">Revert</a>
            </div>
          </div>
        </div>
      </div>

      <!-- LOGOUT MODAL -->
      <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Logout</h5>
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
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>