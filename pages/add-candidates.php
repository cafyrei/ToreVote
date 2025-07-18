<?php
include("../database/connection.php");
/** @var mysqli $conn */

$message = "";

// Fetch positions
$positions = [];
$posQuery = "SELECT DISTINCT position_name FROM positions ORDER BY
  CASE position_name
    WHEN 'President' THEN 1
    WHEN 'Vice President' THEN 2
    WHEN 'Secretary' THEN 3
    WHEN 'Treasurer' THEN 4
    WHEN 'Auditor' THEN 5
    WHEN 'PRO' THEN 6
    ELSE 7
  END";

$posResult = $conn->query($posQuery);
if ($posResult && $posResult->num_rows > 0) {
  while ($row = $posResult->fetch_assoc()) {
    $positions[] = $row['position_name'];
  }
}

// Fetch partylists
$partylists = [];
$partylist_Query = "SELECT partylist_id, partylist_name FROM party_list ORDER BY partylist_name ASC";
$partylist_Results = $conn->query($partylist_Query);

if ($partylist_Results && $partylist_Results->num_rows > 0) {
  while ($row = $partylist_Results->fetch_assoc()) {
    $partylists[] = $row;
  }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['candidate_name']);
  $position = trim($_POST['position']);
  $platform = trim($_POST['platform']);
  $partylist_id = $_POST['partylist_id'];

  $checkSql = "SELECT * FROM candidates WHERE position = ? AND partylist = ?";
  $checkStmt = $conn->prepare($checkSql);
  $checkStmt->bind_param("ss", $position, $partylist_id);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $message = "Only one candidate per position is allowed for each partylist.";
  } else {
    // Handle file upload
    $uploadDir = "../img/";
    $photoName = uniqid() . '_' . basename($_FILES["photo"]["name"]);
    $targetFile = $uploadDir . $photoName;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
      $sql = "INSERT INTO candidates (candidate_name, position, platform, photo, partylist) VALUES (?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);

      if (!$stmt) {
        $message = "Prepare failed: " . $conn->error;
      } else {
        $stmt->bind_param("sssss", $name, $position, $platform, $photoName, $partylist_id);

        if ($stmt->execute()) {
          $message = "Candidate added successfully.";
          header('Location: candidates_maintenance.php');
        } else {
          $message = "Execute failed: " . $stmt->error;
        }
      }
    } else {
      $message = "Error uploading image.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Candidate | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../styles/add-candidate.css" />
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard">
    <aside class="sidebar">
      <img src="../img/logo2.png" alt="VotingSys Logo" style="width: 80px; height: auto; display: block; margin: 0 auto;" />
      <h5 class="admin" style="margin-top: 20px; text-align: center;">Administrator</h5>
      <nav>
        <a href="./dashboard.php">Dashboard</a>
        <a href=" ./partylist_maintenance.php">Partylist Maintenance</a>
        <a href="./position_maintenance.php">Position Maintenance</a>
        <a href="#" class="active">Candidate Maintenance</a>
        <a href="./voters_maintenance.php">Voters Maintenance</a>
        <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
        <a href="./clear-db.php">Clear Database</a>
      </nav>
    </aside>

    <!-- main Content -->
    <main class="main-content">
      <header class="topbar">
        <h1>Add Candidate</h1>
        <p>Fill out the form below to register a new candidate.</p>

        <?php if (!empty($message)): ?>
          <div class="alert"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

      </header>

      <section class="form-section">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="candidate_name">Full Name</label>
            <input type="text" name="candidate_name" id="candidate_name" required />
          </div>

          <div class="form-group">
            <label for="position">Position</label>
            <select name="position" id="position" required>
              <option value="">Select a Position</option>
              <?php foreach ($positions as $position): ?>
                <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
              <?php endforeach; ?>

            </select>
          </div>

          <div class="form-group">
            <label for="partylist_id">Partylist</label>
            <select name="partylist_id" id="partylist_id" required>
              <option value="">Select a Partylist</option>
              <?php foreach ($partylists as $plist): ?>
                <option value="<?= $plist['partylist_id'] ?>"><?= htmlspecialchars($plist['partylist_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>


          <div class="form-group">
            <label for="platform">Platform/Advocacy</label>
            <textarea name="platform" id="platform" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label for="photo">Upload Image</label>
            <input type="file" name="photo" id="photo" accept="image/*" required />
          </div>

          <div class="card-footer d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#revertModal">Back</button>
              <button type="submit" class="submit-btn">Add Candidate</button>
            </div>

          <div class="modal fade" id="revertModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Return</h5>
                </div>
                <div class="modal-body">
                  Are you sure you want to return?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <a href="./candidates_maintenance.php" class="btn btn-danger">Back</a>
                </div>
              </div>
            </div>
          </div>

        </form>
      </section>

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
    </main>
  </div>
</body>

</html>