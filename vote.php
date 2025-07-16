<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT hasVoted FROM user_information WHERE id_number = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row && $row['hasVoted'] == 1) {

  echo "<script>alert('You have already voted.'); window.location.href='dashboard.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vote | Voting System</title>
  <link rel="stylesheet" href="../styles/vote-style.css" />
</head>
<body>
   <!-- Custom Logout Confirmation Modal -->
      <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to logout?dsa
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="./logout.php" class="btn btn-primary">Yes, Logout</a>
            </div>
          </div>
        </div>
      </div>

  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <nav>
        <a href="./dashboard.php">Dashboard</a>
        <a href="./add-candidates.php">Add candidates</a>
        <a href="./vote.php" class="active">Vote</a>
        <a href="./results.php">Results</a>
        <a href="#" class="btn logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
      </nav>
    </aside>
    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Cast Your Vote</h1>
        <p>Select your preferred candidate for each position.</p>
      </header>

      <form id="vote-form" method="POST" action="submit_vote.php">
        <?php
        include("../database/connection.php");

        $positions_result = mysqli_query($conn, "SELECT DISTINCT position FROM candidates ORDER BY FIELD(position, 'President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor')");

        while ($position_row = mysqli_fetch_assoc($positions_result)) {
            $position = $position_row['position'];
            $position_lower = strtolower(str_replace(' ', '_', $position));

            echo "<section class='vote-section'>";
            echo "<h2 class='position-title'>" . htmlspecialchars($position) . "</h2>";
            echo "<div class='candidate-list'>";

            $stmt = $conn->prepare("SELECT * FROM candidates WHERE position = ?");
            $stmt->bind_param("s", $position);
            $stmt->execute();
            $candidates_result = $stmt->get_result();

            while ($candidate = $candidates_result->fetch_assoc()) {
                $name = htmlspecialchars($candidate['candidate_name']);
                $platform = htmlspecialchars($candidate['platform']);
                $photo = "../uploads/" . $candidate['photo'];
                $id = $candidate['id_num'];

                echo "
                <label class='candidate-card'>
                  <input type='radio' name='{$position_lower}' value='{$id}' required />
                  <div class='card-content'>
                    <img src='{$photo}' alt='{$name}' />
                    <h3>{$name}</h3>
                    <p>{$platform}</p>
                  </div>
                </label>
                ";
            }

            echo "</div></section>";
        }
        ?>

        <div class="submit-container">
          <button type="submit" class="vote-btn">Submit Vote</button>
        </div>
      </form>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>