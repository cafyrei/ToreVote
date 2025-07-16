<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

if (!isset($_SESSION['email'])) {
  header ('location: login.php');
  exit();
}

$username = $_SESSION['username'];

$sqlVoted = "SELECT hasVoted FROM user_information WHERE username = ?";
$stmtVoted = $conn->prepare($sqlVoted);
$stmtVoted->bind_param("s", $username);
$stmtVoted->execute();
$resultVoted = $stmtVoted->get_result();
$userRow = $resultVoted->fetch_assoc();
$hasVoted = $userRow['hasVoted'] ?? 0;

$sql = "SELECT
  COUNT(*) AS total_voters,
  COUNT(CASE WHEN hasVoted = 1 THEN 1 END) AS votes_cast,
  COUNT(CASE WHEN hasVoted = 0 THEN 1 END) AS votes_remaining
FROM user_information";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_voters = $row['total_voters'];
    $votes_cast = $row['votes_cast'];
    $votes_remaining = $row['votes_remaining'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Voting Dashboard</title>
  <link rel="stylesheet" href="../styles/dashboard-style.css" />
</head>
<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <nav>
        <a href="#" class="active">Dashboard</a>
        <a href="./vote.php">Vote</a>
        <a href="./results.php">Results</a>
        <a href="./logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
        
      </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
        <!-- Hatak dito pre moreon Variables -->
      <header class="topbar">
        <h1>Welcome, <?php echo $username ?></h1>
      </header>

      <section class="cards">
        <div class="card">
          <h3>Total Voters</h3>
          <p><?php echo $total_voters ?></p>
        </div>
        <div class="card">
          <h3>Votes Cast</h3>
          <p><?php echo $votes_cast ?></p>
        </div>
        <div class="card">
          <h3>Remaining</h3>
          <p><?php echo $votes_remaining ?></p>
        </div>
      </section>

      <div class="vote-now">
        <?php if (!$hasVoted) { ?>
          <h2>Ready to vote?</h2>
          <p>Select your candidate from the list and click submit.</p>
          <a href="./vote.php"><button>Go to Voting Page</button></a>
        <?php }  else { ?>
          <h2>You have already casted your vote!</h2>
          <p>See the results!</p>
          <a href="./results.php"><button>Go to Results</button></a>
        <?php } ?>
      </section>
    </main>
  </div>
</body>
</html>
