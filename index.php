<?php

 $update = false;
 $delete = false;
 $insert = false;

 $servername = "localhost";
 $username = "root";
 $password = "";
 $database = "notes";

 $conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Sorry we failed to connect: " . mysqli_connect_error());
}

if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $sql = "DELETE FROM `notes` WHERE `sno` = $sno";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $delete = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];
        $priority = $_POST["priorityEdit"];
        $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description', `priority` = '$priority' WHERE `notes`.`sno` = $sno";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $update = true;
        }
    } else {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $priority = $_POST["priority"];
        $sql = "INSERT INTO `notes` (`title`, `description`, `priority`) VALUES ('$title', '$description', '$priority')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $insert = true;
        }
    }
}

 $sql = "SELECT * FROM `notes` ORDER BY CASE WHEN priority='High' THEN 1 WHEN priority='Medium' THEN 2 ELSE 3 END, sno DESC";
 $result = mysqli_query($conn, $sql);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>I Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand ms-3" href="#">olfant to do list</a>
    </div>
</nav>

<div class="container my-4">

<?php if ($insert): ?>
    <div class="alert alert-success">Note added successfully!</div>
<?php endif; ?>
<?php if ($update): ?>
    <div class="alert alert-primary">Note updated successfully!</div>
<?php endif; ?>
<?php if ($delete): ?>
    <div class="alert alert-danger">Note deleted successfully!</div>
<?php endif; ?>

<h2>Add a note</h2>
<form action="index.php" method="POST">
    <div class="mb-3">
        <label class="form-label">Note Title</label>
        <input type="text" class="form-control" name="title" placeholder="Enter title" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Note Description</label>
        <textarea class="form-control" name="description" rows="3" placeholder="Write your note" required></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Priority</label>
        <select class="form-control" name="priority" required>
            <option value="High">High</option>
            <option value="Medium" selected>Medium</option>
            <option value="Low">Low</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Add Note</button>
</form>

<hr class="my-4">

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="index.php" method="POST">
                    <input type="hidden" id="snoEdit" name="snoEdit">
                    <div class="mb-3">
                        <label class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="titleEdit" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-control" id="priorityEdit" name="priorityEdit" required>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<h2>Your Notes</h2>
<?php
 $noteCount = mysqli_num_rows($result);
if ($noteCount == 0) {
    echo "No notes yet. Add one above!";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $sno = $row['sno'];
        $title = htmlspecialchars($row['title']);
        $description = htmlspecialchars($row['description']);
        $priority = htmlspecialchars($row['priority']);
        $badgeClass = $priority == 'High' ? 'bg-danger' : ($priority == 'Medium' ? 'bg-warning text-dark' : 'bg-success');
        echo '
        <div class="card mb-3">
            <div class="card-body">
                <h5>' . $title . ' <span class="badge ' . $badgeClass . '">' . $priority . '</span></h5>
                <p class="mb-0">' . $description . '</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-primary" onclick="editNote(' . $sno . ', \'' . $title . '\', \'' . $description . '\', \'' . $priority . '\')">Edit</button>
                <a href="index.php?delete=' . $sno . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this note?\')">Delete</a>
            </div>
        </div>';
    }
}
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editNote(sno, title, description, priority) {
    document.getElementById("snoEdit").value = sno;
    document.getElementById("titleEdit").value = title;
    document.getElementById("descriptionEdit").value = description;
    document.getElementById("priorityEdit").value = priority;
    var m = new bootstrap.Modal(document.getElementById('editModal'));
    m.show();
}
</script>

</body>
</html>
