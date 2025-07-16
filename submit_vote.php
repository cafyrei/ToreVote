<?php
session_start();
include("../database/connection.php");

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  foreach ($_POST as $position => $candidate_id) {

    $check = $conn->prepare("SELECT * FROM votes WHERE user_id = ? AND position = ?");
    $check->bind_param("is", $user_id, $position);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {

      $insert = $conn->prepare("INSERT INTO votes (user_id, candidate_id, position) VALUES (?, ?, ?)");
      $insert->bind_param("iis", $user_id, $candidate_id, $position);
      $insert->execute();
      $insert->close();

      $update = $conn->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id_num = ?");
      $update->bind_param("i", $candidate_id);
      $update->execute();
      $update->close();
    }
  }

  $setVoted = $conn->prepare("UPDATE user_information SET hasVoted = 1 WHERE id_number = ?");
  $setVoted->bind_param("i", $user_id);
  $setVoted->execute();
  $setVoted->close();

  header("Location: results.php");
  exit();
}
?>
