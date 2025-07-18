<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

if (!isset($_SESSION['username'])) {
  header('location: ../index.php');
  exit;
}

$username = $_SESSION['username'];

// Check voting status
$sqlVoted = "SELECT hasVoted FROM user_information WHERE username = ?";
$stmt = $conn->prepare($sqlVoted);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($userRow = $result->fetch_assoc()) {
  $hasVoted = $userRow['hasVoted'];
} else {
  echo "User not Found";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vote | Voting System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../styles/vote-style.css" />
  <link rel="shortcut icon" href="../img/ToreVote.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard">

    <!-- main -->
    <main class="main-content">
      <?php if (!$hasVoted) { ?>

        <hr style="margin: 10px 0; border-top: 4px solid #2b3b68ff;" />

        <header class="topbar">
          <h1>Cast Your Vote</h1>
          <p>Select your preferred candidate for each position.</p>
        </header>

        <hr style="margin: 10px 0; border-top: 4px solid #2b3b68ff;" />

        <form class="mt-4" id="vote-form" method="POST" action="submit_vote.php">
          <?php
          $sql = "SELECT DISTINCT position
                  FROM candidates
                  ORDER BY CASE position
                    WHEN 'President' THEN 1
                    WHEN 'Vice President' THEN 2
                    WHEN 'Secretary' THEN 3
                    WHEN 'Treasurer' THEN 4
                    WHEN 'Auditor' THEN 5
                    WHEN 'PRO' THEN 6
                    ELSE 99
                  END";
          $positions_result = mysqli_query($conn, $sql);

          while ($position_row = mysqli_fetch_assoc($positions_result)) {
            $position = $position_row['position'];
            $position_lower = strtolower(str_replace(' ', '_', $position));

            echo "<section class='vote-section'>";
            echo "<h2 class='position-title'>" . htmlspecialchars($position) . "</h2>";
            echo "<div class='candidate-list'>";

            $stmt = $conn->prepare("SELECT c.*, p.partylist_name 
                        FROM candidates c 
                        LEFT JOIN party_list p ON c.partylist = p.partylist_id 
                        WHERE c.position = ?");
            $stmt->bind_param("s", $position);
            $stmt->execute();
            $candidates_result = $stmt->get_result();

            while ($candidate = $candidates_result->fetch_assoc()) {
              $name = htmlspecialchars($candidate['candidate_name']);
              $platform = htmlspecialchars($candidate['platform']);
              $partylist_name = htmlspecialchars($candidate['partylist_name']);
              $photo = "../img/" . $candidate['photo'];
              $id = $candidate['id_num'];

              echo "
              <label class='candidate-card'>
                <input type='radio' name='{$position_lower}' value='{$id}' required />
                <div class='card-content'>
                  <img src='{$photo}' alt='{$name}' />
                  <h3>{$name}</h3>
                  <p style='color: #0056e0ff' ><strong>Partylist: </strong>{$partylist_name}</p>
                  <p style='color: #1e293b' >{$platform}</p>
                </div>
              </label>";
            }

            echo "</div></section>";
          }
          ?>
          <div class="submit-container">
            <button type="button" class="logout-button" data-bs-toggle="modal" data-bs-target="#voteModal">Submit</button>
          </div>

          <div class="modal fade" id="voteModal" tabindex="-1" aria-labelledby="voteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="voteModalLabel">Confirm Vote</h5>
                </div>
                <div class="modal-body">
                  Are you sure you want to finalize and submit your votes?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" form="vote-form">Yes, I Confirm</button>
                </div>
              </div>
            </div>
          </div>
        </form>

      <?php } else { ?>
        <header class="topbar vote-casted-elements">
          <h1>You Have Already Casted Your Vote</h1>
          <p>Thank you for casting your vote and helping keep the election fair and honest.</p>
          <button class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
        </header>
      <?php } ?>
    </main>
  </div>

  <!-- Confirm Vote Modal -->
  <!-- Logout Modal -->
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
          <a href="./logout.php" class="btn btn-primary">Yes, Logout</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>