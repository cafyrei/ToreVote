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

    $targetDir = "../img/";
    $defaultImage = "your_logo_here.png";

    if (!empty($image)) {
        $targetFile = $targetDir . basename($image);
        if (move_uploaded_file($imageTmp, $targetFile)) {
            $finalImage = $image;
        } else {
            $errorMessage = "Failed to upload image.";
            $finalImage = $defaultImage; // fallback
        }
    } else {
        $finalImage = $defaultImage; // if no file was uploaded
    }

    $stmt = $conn->prepare("INSERT INTO party_list (partylist_name, partylist_image, dateCreated) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $partylistName, $finalImage);
    if ($stmt->execute()) {
        $_SESSION['partylist_added'] = true;
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
    <link rel="stylesheet" href="../styles/partylist_maintenance.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <td>
                            <a href='#' 
                            class='edit-btn text-info' 
                            data-toggle='modal' 
                            data-target='#editModal' 
                            data-id='" . $row['partylist_id'] . "' 
                            data-name='" . htmlspecialchars($row['partylist_name'], ENT_QUOTES) . "'> Modify </a> |
                            <a href='#' 
                            class='delete-btn text-danger' 
                            data-toggle='modal' 
                            data-target='#deleteModal' 
                            data-id='" . $row['partylist_id'] . "'>Delete</a>
                        </td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <form method="POST" enctype="multipart/form-data"
                id="addPartylistForm"
                class="add-form mt-4 <?= isset($_SESSION['partylist_added']) ? 'added-success' : '' ?>">
                <input type="text" name="partylist_name" placeholder="Enter partylist name" required />
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add</button>
                <input type="hidden" name="add_partylist" value="1">
            </form>

            
        </main>
    </div>

    <!-- MODAL SECTION -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Commit Add?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to add this new partylist?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-primary" form="addPartylistForm">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this partylist?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Partylist</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="partylist_id" id="edit_partylist_id">
                        <div class="form-group">
                            <label for="new_partylist_name">Partylist Name</label>
                            <input type="text" class="form-control" name="new_partylist_name" id="edit_partylist_name" required>
                        </div>
                        <div class="form-group">
                            <label for="new_partylist_image">Partylist Logo</label>
                            <input type="file" class="form-control-file" name="new_partylist_image" id="edit_partylist_image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_partylist" class="btn btn-primary">Save Changes</button>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
        $('#editModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            const modal = $(this);
            modal.find('#edit_partylist_id').val(id);
            modal.find('#edit_partylist_name').val(name);
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector(".add-form.added-success");

            if (form) {
                const buttons = form.querySelectorAll("button, input[type='submit']");
                buttons.forEach(btn => btn.style.visibility = "hidden");

                setTimeout(() => {
                    form.classList.remove("added-success");
                    buttons.forEach(btn => btn.style.visibility = "visible");
                    const url = new URL(window.location);
                    url.searchParams.delete("added");
                    window.history.replaceState({}, document.title, url);

                }, 1000);
            }
        });
    </script>

    <!-- END OF JAVASCRIPT SECTION -->

</body>

</html>