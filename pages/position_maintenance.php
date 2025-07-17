<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$errorMessage = "";

// Add Partylist
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_partylist'])) {
    $partylistName = trim($_POST['partylist_name']);
    $dateCreated = date("Y-m-d");

    if (!empty($partylistName) && isset($_FILES["partylist_image"])) {
        $imageName = uniqid() . "_" . $_FILES["partylist_image"]["name"];
        $targetPath = "uploads/" . $imageName;

        move_uploaded_file($_FILES["partylist_image"]["tmp_name"], $targetPath);

        $stmt = $conn->prepare("INSERT INTO partylist (partylist_name, partylist_image, dateCreated) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $partylistName, $imageName, $dateCreated);
        $stmt->execute();
        header("Location: PartylistMaintenance.php");
        exit();
    }
}

// Delete Partylist
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM partylist WHERE partylist_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Edit Partylist (optional image change)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_partylist'])) {
    $id = intval($_POST['partylist_id']);
    $name = trim($_POST['new_partylist_name']);

    if (isset($_FILES['new_partylist_image']) && $_FILES['new_partylist_image']['error'] === 0) {
        $newImage = uniqid() . "_" . $_FILES['new_partylist_image']['name'];
        move_uploaded_file($_FILES['new_partylist_image']['tmp_name'], "uploads/" . $newImage);
        $stmt = $conn->prepare("UPDATE partylist SET partylist_name = ?, partylist_image = ? WHERE partylist_id = ?");
        $stmt->bind_param("ssi", $name, $newImage, $id);
    } else {
        $stmt = $conn->prepare("UPDATE partylist SET partylist_name = ? WHERE partylist_id = ?");
        $stmt->bind_param("si", $name, $id);
    }

    $stmt->execute();
    header("Location: PartylistMaintenance.php");
    exit();
}
?>




    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Position Maintenance</title>
        <link rel="stylesheet" href="../styles/position_maintenance.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <div class="dashboard">
        <aside class="sidebar">
        <h2 class="logo">VotingSys</h2>
        <h5 class="logo">Administrator</h5>
        <nav>
            <a href="#">Dashboard</a>
            <a href="./partylist_maintenance.php">Partylist Maintenance</a>
            <a href="./position_maintenance.php">Position Maintenance</a>
            <a href="#">Candidate Maintenance</a>
            <a href="#">Voters Maintenance</a>
            <a href="#" class="logout-button">Logout</a>
        </nav>
        </aside>

        <main class="main-content">
        <h1>Position Maintenance</h1>

        <form method="POST" class="add-form" id="addPositionForm">
            <input type="text" name="position_name" id="position_name" placeholder="Enter new position" required />
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Add</button>
            <input type="hidden" name="add_position" value="1">
        </form>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['edit'])): 
            $editID = intval($_GET['edit']);
            $result = $conn->query("SELECT * FROM positions WHERE PositionID = $editID");
            $editRow = $result->fetch_assoc();
        ?>
            <form method="POST" class="edit-form mt-4 d-flex align-items-center gap-2">
                <input type="hidden" name="position_id" value="<?= $editRow['PositionID'] ?>" />
                <input type="text" name="new_position_name" class="form-control mr-2" value="<?= htmlspecialchars($editRow['PositionName']) ?>" required />
                <a href="./PositionMaintenance.php" class="btn btn-secondary">Cancel</a>
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
                $result = $conn->query("SELECT * FROM positions 
                    ORDER BY FIELD(PositionName, 'President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor'), PositionID ASC");

                while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['PositionID']}</td>
                        <td>{$row['PositionName']}</td>
                        <td>
                            <a href='?edit={$row['PositionID']}' class='edit-btn'>Modify</a> |
                            <a href='#' class='delete-btn text-danger' data-id='{$row['PositionID']}' data-toggle='modal' data-target='#deleteModal'>Delete</a>
                        </td>
                        </tr>";
                }
            ?>
            </tbody>
        </table>
        </main>
    </div>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Commit Add?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to add this new position?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-primary" form="addPositionForm">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this position?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Delete</a>
      </div>
    </div>
  </div>
</div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deleteButtons = document.querySelectorAll(".delete-btn");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");
                    confirmDeleteBtn.setAttribute("href", "?delete=" + id);
                });
            });
        });
    </script>
    </body>
</html>
