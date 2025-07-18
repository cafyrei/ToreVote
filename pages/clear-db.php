<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$message = "";
$error = "";

if (!isset($_SESSION['clear_success'])) {
  $_SESSION['clear_success'] = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_clear'])) {
  $inputSecurityCode = trim($_POST['admin_security_code'] ?? '');
  $adminEmail = $_SESSION['admin_email'] ?? '';

  if (!empty($adminEmail)) {
    $stmt = $conn->prepare("SELECT admin_security FROM admin_db WHERE admin_email = ?");
    $stmt->bind_param("s", $adminEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
      $dbSecurityCode = trim($admin['admin_security']);

      if ($inputSecurityCode === $dbSecurityCode) {
        $tables = ['candidates', 'party_list', 'positions', 'user_information'];
        foreach ($tables as $table) {
          $conn->query("TRUNCATE TABLE `$table`");
        }

        $_SESSION['clear_success'] = true;
        header("Location: clear-db.php");
        exit;
      } else {
        $error = "Incorrect security code. Action denied.";
      }
    } else {
      $error = "Admin record not found for email: $adminEmail";
    }
  } else {
    $error = "Session expired or admin not logged in.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Clear Database</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="p-5 bg-light">

  <div class="container">
    <h3 class="text-danger">⚠️ Danger Zone</h3>
    <p>This will <strong>delete all records</strong> from the system. Please proceed with extreme caution.</p>

    <div class="d-flex gap-2 mt-3">
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmClearModal">
        Clear Entire Database
      </button>
      <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
  </div>

  <!-- Confirm Clear Modal -->
  <div class="modal fade" id="confirmClearModal" tabindex="-1" aria-labelledby="confirmClearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title text-danger" id="confirmClearModalLabel">Confirm Database Wipe</h5>
          </div>
          <div class="modal-body">
            <p>This will permanently delete all records. This action cannot be undone.</p>
            <div class="mb-3">
              <label for="admin_security_code" class="form-label">Enter Admin Security Code</label>
              <input type="password" class="form-control" name="admin_security_code" id="admin_security_code" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="confirm_clear" class="btn btn-danger">Yes, Delete All</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <?php if ($_SESSION['clear_success']): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="successModalLabel">Database Cleared</h5>
          </div>
          <div class="modal-body">
            <p>All data has been cleared successfully. Redirecting to dashboard...</p>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        setTimeout(() => {
          window.location.href = 'dashboard.php';
        }, 2500);
      });
    </script>

    <?php $_SESSION['clear_success'] = false; ?>
  <?php endif; ?>

</body>

</html>