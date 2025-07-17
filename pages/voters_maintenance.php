<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

if (isset($_POST['search'])) {
    $searchq = strtolower($_POST['search']);
    $searchq = "%$searchq%";
    $query = "SELECT * FROM `user_information` WHERE LOWER(first_name) LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchq);
} else {
    $query = "SELECT * FROM `user_information`";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$results = $stmt->get_result();
$rows = [];

while ($result = $results->fetch_assoc()) {
    $rows[] = $result;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Voting Dashboard</title>
  <link rel="stylesheet" href="../styles/voters_maintenance-style.css" />
  <link rel="stylesheet" href="../styles/results-style.css" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <h3 class="logo">Admnistrator</h3>
      <nav>
        <a href="./dashboard.php">Dashboard</a>
        <a href="#">Partylist Maintenance</a>
        <a href="#">Position Maintenance</a>
        <a href="#">Candidate Maintenance</a>
        <a href="#" class="active">Voters Maintenance</a>
        <a href="#" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
      </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Voters' Maintenance</h1>
      </header>
    <form method="POST" class="add-form" id="addPositionForm">
        <input type="text" name="search" id="position_name" placeholder="Search Voter's First Name" required />
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

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
            <th>Date Created</th>
            <th>Action</th>
        </tr>
        <?php foreach($rows as $row) { ?>
            <tr>
                <td><?=$row['id_number']?></td>
                <td><?=$row['first_name']?></td>
                <td><?=$row['middle_name']?></td>
                <td><?=$row['last_name']?></td>
                <td><?=$row['gender']?></td>
                <td><?=$row['email']?></td>
                <td><?=$row['username']?></td>
                <td><?=$row['password'] = substr($row['password'], 0, 11)?></td>
                <td><?=$row['hasVoted'] ? 'Voted' : 'Not Voted'?></td>
                <td><?=$row['date_created']?></td>
                <td>
                <a href='?edit=<?= $row['id_number'] ?>' class='btn btn-primary btn-sm'>Modify</a>
                <a href='?delete=<?= $row['PositionID'] ?>' class='btn btn-danger btn-sm' onclick='return confirm("Are you sure you want to delete this position?")'>Delete</a>
                </td>
            </tr>
        <?php } ?>
      </table>
      <?php if (isset($_POST['search'])) {
                if ($results->num_rows === 0) {
                        echo '<h1 class="center-text">No Results Found</h1>';
                }
                echo '<div class="center-button"><a href="./voters_maintenance.php" class="myButton">Go Back</a></div>';
                }?>
      <!-- Modal -->
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
    </main>
  </div>
</body>
</html>