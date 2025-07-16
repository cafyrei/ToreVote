<?php
session_start();
include("../database/connection.php");

$username = $_SESSION['username'] ?? null;

if (!$username) {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  foreach ($_POST as $position => $candidate_id) {
    // Check if user already voted for this position
    $check = $conn->prepare("SELECT * FROM votes WHERE username = ? AND position = ?");
    $check->bind_param("ss", $username, $position);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
      // Insert vote record
      $insert = $conn->prepare("INSERT INTO votes (user_id, candidate_id, position) VALUES (?, ?, ?)");
      $insert->bind_param("iis", $user_id, $candidate_id, $position);
      $insert->execute();
      $insert->close();

      // Update candidate's vote count
      $update = $conn->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id_num = ?");
      $update->bind_param("i", $candidate_id);
      $update->execute();
      $update->close();
    }
  }

  // ✅ Now update hasVoted to 1 after successful submission
  $setVoted = $conn->prepare("UPDATE user_information SET hasVoted = 1 WHERE id_number = ?");
  $setVoted->bind_param("i", $user_id);
  $setVoted->execute();
  $setVoted->close();

  // ✅ Redirect to results
  header("Location: results.php");
  exit();
}
?>