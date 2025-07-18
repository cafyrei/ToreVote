<?php
$conn = new mysqli("localhost", "root", "", "votingsysdb");
$errorMessage = "";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Position
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_position'])) {
    $positionName = trim($_POST['position_name']);
    if (!empty($positionName)) {
        // Check for duplicates
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM positions WHERE position_name = ?");
        $checkStmt->bind_param("s", $positionName);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $errorMessage = "The position '$positionName' already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO positions (position_name) VALUES (?)");
            $stmt->bind_param("s", $positionName);
            $stmt->execute();
            header("Location: position_maintenance.php");
            exit();
        }
    }
}

// Update Position
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_position'])) {
    $newName = trim($_POST['new_position_name']);
    $id = intval($_POST['position_id']);
    if (!empty($newName)) {
        $stmt = $conn->prepare("UPDATE positions SET position_name = ? WHERE position_id = ?");
        $stmt->bind_param("si", $newName, $id);
        $stmt->execute();
        header("Location: position_maintenance.php");
        exit();
    }
}

// Delete Position
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM positions WHERE position_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: position_maintenance.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Position Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/position_maintenance.css" />

</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2 class="logo">VotingSys</h2>
            <h5 class="logo">Administrator</h5>
            <nav>
                <a href="./dashboard.php">Dashboard</a>
                <a href="./partylist_maintenance.php">Partylist Maintenance</a>
                <a href="./position_maintenance.php" class="active">Position Maintenance</a>
                <a href="./add-candidates.php">Candidate Maintenance</a>
                <a href="./voters_maintenance.php">Voters Maintenance</a>
                <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <h1>Position Maintenance</h1>

            <hr style="margin: 10px 0; border-top: 4px solid #1e3a8a;" />

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit'])):
                $editID = intval($_GET['edit']);
                $result = $conn->query("SELECT * FROM positions WHERE position_id = $editID");
                $editRow = $result->fetch_assoc();
            ?>
                <form method="POST" class="edit-form mt-4 d-flex align-items-center gap-2">
                    <input type="hidden" name="position_id" value="<?= $editRow['position_id'] ?>" />
                    <input type="text" name="new_position_name" class="form-control mr-2" value="<?= htmlspecialchars($editRow['position_name']) ?>" required />
                    <a href="./position_maintenance.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update_position" class="btn btn-primary">Save</button>
                </form>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Position Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT DISTINCT * FROM positions ORDER BY
                    CASE position_name
                        WHEN 'President' THEN 1
                        WHEN 'Vice President' THEN 2
                        WHEN 'Secretary' THEN 3
                        WHEN 'Treasurer' THEN 4
                        WHEN 'Auditor' THEN 5
                        WHEN 'PRO' THEN 6
                        ELSE 99
                    END;");

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$row['position_id']}</td>
                        <td>{$row['position_name']}</td>
                        <td>
                            <a href='#' 
                            class='edit-btn text-info' 
                            data-bs-toggle='modal' 
                            data-bs-target='#editPositionModal' 
                            data-id='{$row['position_id']}'
                            data-name='" . htmlspecialchars($row['position_name'], ENT_QUOTES) . "'> Modify </a> |
                            <a href='#' class='delete-btn text-danger' data-id='{$row['position_id']}' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</a>
                        </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <form method="POST" class="add-form mt-4" id="addPositionForm">
                <input type="text" name="position_name" id="position_name" placeholder="Enter new position" required />
                <button type="submit" class="btn btn-primary">Add</button>
                <input type="hidden" name="add_position" value="1">
            </form>

        </main>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this position?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="position_id" id="edit_position_id">
                        <div class="form-group">
                            <label for="edit_position_name">Position Name</label>
                            <input type="text" class="form-control" name="new_position_name" id="edit_position_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_position" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle Edit Buttons
            const editButtons = document.querySelectorAll(".edit-btn");
            editButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    const name = this.getAttribute("data-name");

                    document.getElementById("edit_position_id").value = id;
                    document.getElementById("edit_position_name").value = name;
                });
            });

            // Handle Delete Buttons
            const deleteButtons = document.querySelectorAll(".delete-btn");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    confirmDeleteBtn.href = `position_maintenance.php?delete=${id}`;
                });
            });
        });
    </script>



</body>

</html>