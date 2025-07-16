<?php
include("../database/connection.php");
$message = "";
/** @var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['candidate_name'];
  $position = $_POST['position'];
  $platform = $_POST['platform'];

  $uploadDir = "../img/";
  $photoName = uniqid() . '_' . basename($_FILES["photo"]["name"]);
  $targetFile = $uploadDir . $photoName;

  if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
    $sql = "INSERT INTO candidates (candidate_name, position, platform, photo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
      $message = "Prepare failed: " . $conn->error;
    } else {
      $stmt->bind_param("ssss", $name, $position, $platform, $photoName);

      if ($stmt->execute()) {
        $message = "✅ Candidate added successfully!";
      } else {
        $message = "❌ Execute failed: " . $stmt->error;
      }
    }
  } else {
    $message = "❌ Error uploading image.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Candidate | Admin</title>
  <link rel="stylesheet" href="../styles/add-candidate.css" />
</head>

<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <nav>
        <a href="./dashboard.php">Dashboard</a>
        <a href="./add-candidate.php" class="active">Add Candidates</a>
        <a href="./vote.php">Vote</a>
        <a href="./logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
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

          <!-- get nalang tayo by id dito -->
          <div class="form-group">
            <label for="position">Position</label>
            <select name="position" id="position" required>
              <option value="">Select a Position</option>
              <option value="President">President</option>
              <option value="Vice President">Vice President</option>
              <option value="Secretary">Secretary</option>
              <option value="Treasurer">Treasurer</option>
              <option value="Auditor">Auditor</option>
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

          <button type="submit" class="submit-btn">Add Candidate</button>
        </form>
      </section>
    </main>
  </div>
</body>

</html>