<?php
session_start();
include("../database/connection.php");
/** @var mysqli $conn */

$errorMessage = '';

$addedClass = '';
if (isset($_SESSION['partylist_added'])) {
    $addedClass = 'added-success';
    unset($_SESSION['partylist_added']);
}

if (isset($_POST['add_partylist'])) {
    $partylistName = $_POST['partylist_name'];
    $image = $_FILES['partylist_image']['name'] ?? '';
    $imageTmp = $_FILES['partylist_image']['tmp_name'] ?? '';

    $targetDir = "../img/";
    $defaultImage = "your_logo_here.png";

    if (!empty($image)) {
        $targetFile = $targetDir . basename($image);
        if (move_uploaded_file($imageTmp, $targetFile)) {
            $finalImage = $image;
        } else {
            $errorMessage = "Failed to upload image.";
            $finalImage = $defaultImage;
        }
    } else {
        $finalImage = $defaultImage;
    }

    $stmt = $conn->prepare("INSERT INTO party_list (partylist_name, partylist_image, dateCreated) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $partylistName, $finalImage);
    if ($stmt->execute()) {
        $_SESSION['partylist_added'] = true;
        header("Location: partylist_maintenance.php"); // redirect after successful add
        exit();
    }
}


if (isset($_POST['update_partylist'])) {
    $id = $_POST['partylist_id'];
    $newName = $_POST['new_partylist_name'];
    $newImage = $_FILES['new_partylist_image']['name'];
    $imageTmp = $_FILES['new_partylist_image']['tmp_name'];

    if (!empty($newImage)) {
        $targetDir = "../img/";
        $targetFile = $targetDir . basename($newImage);
        move_uploaded_file($imageTmp, $targetFile);
        $stmt = $conn->prepare("UPDATE party_list SET partylist_name = ?, partylist_image = ? WHERE partylist_id = ?");
        $stmt->bind_param("ssi", $newName, $newImage, $id);
    } else {
        $stmt = $conn->prepare("UPDATE party_list SET partylist_name = ? WHERE partylist_id = ?");
        $stmt->bind_param("si", $newName, $id);
    }
    $stmt->execute();
    header("Location: partylist_maintenance.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM party_list WHERE partylist_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: partylist_maintenance.php");
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../styles/position_maintenance.css" />
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2 class="logo">VotingSys</h2>
            <h5 class="logo">Administrator</h5>
            <nav>
                <a href="./dashboard.php">Dashboard</a>
                <a href="./partylist_maintenance.php" class="active">Partylist Maintenance</a>
                <a href="./position_maintenance.php">Position Maintenance</a>
                <a href="./add-candidates.php">Candidate Maintenance</a>
                <a href="./voters_maintenance.php">Voters Maintenance</a>
                <a href="./admin-logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <h1>Partylist Maintenance</h1>

            <hr style="margin: 10px 0; border-top: 4px solid #1e3a8a;" />

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit'])):
                $editID = intval($_GET['edit']);
                $result = $conn->query("SELECT * FROM party_list WHERE partylist_id = $editID");
                $editRow = $result->fetch_assoc();
            ?>
                <form method="POST" class="edit-form mt-4 d-flex align-items-center gap-2" enctype="multipart/form-data">
                    <input type="hidden" name="partylist_id" value="<?= $editRow['partylist_id'] ?>" />
                    <input type="text" name="new_partylist_name" value="<?= htmlspecialchars($editRow['partylist_name']) ?>" required />
                    <input type="file" name="new_partylist_image" accept="image/*" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_partylist" class="btn btn-primary">Save</button>
                </form>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Partylist Logo</th>
                        <th>Partylist Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM party_list ORDER BY partylist_id ASC");

                    while ($row = $result->fetch_assoc()) {
                        $imagePath = '../img/your_logo_here.png'; // default

                        if (!empty($row['partylist_image'])) {
                            $customImage = "../img/" . $row['partylist_image'];
                            if (file_exists($customImage)) {
                                $imagePath = $customImage;
                            }
                        }

                        echo "<tr>
                        <td>{$row['partylist_id']}</td>
                        <td><img src='" . htmlspecialchars($imagePath) . "' width='60' height='60' style='object-fit: cover; border-radius: 5px;' alt='Partylist Image'></td>
                        <td>" . htmlspecialchars($row['partylist_name']) . "</td>
                        <td><a href='#' class='edit-btn text-info' data-bs-toggle='modal' data-bs-target='#editModal' data-id='" . htmlspecialchars($row['partylist_id']) . "' data-name='" . htmlspecialchars($row['partylist_name']) . "' data-image='" . htmlspecialchars($row['partylist_image']) . "'><i class='fas fa-edit'></i> Modify </a> | <a href='#' class='delete-btn text-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . htmlspecialchars($row['partylist_id']) . "'>Delete</a></td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <form method="POST" enctype="multipart/form-data"
                id="addPartylistForm"
                class="add-form mt-4 <?= isset($_SESSION['partylist_added']) ? 'added-success' : '' ?>">
                <input type="text" name="partylist_name" placeholder="Enter partylist name" required />
                <button type="submit" class="btn btn-primary">Add</button>
                <input type="hidden" name="add_partylist" value="1">
            </form>

        </main>
    </div>

    <!-- MODAL SECTION -->
     <!-- Delete Modal -->
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="partylist_id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-name">Position Name</label>
                            <input type="text" class="form-control" id="edit-name" name="new_partylist_name" required>
                            <input type="file" name="new_partylist_image" class="form-control mt-2" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="update_partylist">Save Changes</button>
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

    <!-- END OF MODAL SECTION -->

    <!-- JAVA SCRIPT SECTION -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const deleteButtons = document.querySelectorAll(".delete-btn");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    confirmDeleteBtn.setAttribute("href", "?delete=" + id);
                });
            });
        });
    </script>

    <script>
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
        });
    </script>
    <!-- END OF JAVASCRIPT SECTION -->

</body>

</html>