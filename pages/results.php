<?php
include("../database/connection.php");

$sql = "SELECT DISTINCT position FROM candidates ORDER BY CASE position WHEN 'President' THEN 1 WHEN 'Vice President' THEN 2 WHEN 'Secretary' THEN 3 WHEN 'Treasurer' THEN 4 WHEN 'Auditor' THEN 5 WHEN 'PRO' THEN 6 ELSE 99 END";
$position_result = mysqli_query($conn, $sql);

$sql = "SELECT DISTINCT partylist_name FROM party_list";
$partylist_result = mysqli_query($conn, $sql);

$results_sections = [];

while ($position_row = mysqli_fetch_assoc($position_result)) {
  $position = $position_row['position'];

  $candidates_sql = "SELECT * FROM candidates WHERE position = '$position'";
  $candidates_sql = "SELECT * FROM party_list WHERE position = '$position'";
  $candidates_result = mysqli_query($conn, $candidates_sql);

  $total_votes = 0;
  $candidates = [];

  while ($candidate = mysqli_fetch_assoc($candidates_result)) {
    $vote = isset($candidate['vote_count']) ? (int)$candidate['vote_count'] : 0;
    $total_votes += $vote;
    $candidate['vote_count'] = $vote;
    $candidates[] = $candidate;
  }


  $results_sections[] = [
    'position' => $position,
    'candidates' => $candidates,
    'total_votes' => $total_votes
  ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Results | Voting System</title>
  <link rel="stylesheet" href="../styles/results-style.css" />
</head>

<body>
  <div class="dashboard">
    <!-- sidebar -->
    <aside class="sidebar">
      <h2 class="logo">VotingSys</h2>
      <nav>
        <a href="./dashboard.html">Dashboard</a>
        <a href="./vote.html">Vote</a>
        <a href="./results.php" class="active">Results</a>
        <a href="#">Logout</a>
      </nav>
    </aside>

    <!-- main -->
    <main class="main-content">
      <header class="topbar">
        <h1>Voting Results</h1>
        <p>See who’s leading for each position.</p>
      </header>

      <?php foreach ($results_sections as $section): ?>
        <section class="results-section">
          <h2 class="position-title"><?= htmlspecialchars($section['position']) ?></h2>
          <div class="results-list">
            <?php foreach ($section['candidates'] as $c):
              $percentage = $section['total_votes'] > 0 ? round(($c['vote_count'] / $section['total_votes']) * 100) : 0;
            ?>
              <div class="result-card">
                <img src="../img/<?= htmlspecialchars($c['photo']) ?>" alt="<?= htmlspecialchars($c['candidate_name']) ?>">
                <div class="info">
                  <h3><?= htmlspecialchars($c['candidate_name']) ?></h3>
                  <p>Votes: <?= $c['vote_count'] ?></p>
                  <div class="progress-bar">
                    <div class="fill" style="width: <?= $percentage ?>%;"><?= $percentage ?>%</div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

    </main>
  </div>
</body>

</html>