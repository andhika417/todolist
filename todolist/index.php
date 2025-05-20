<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if (!empty($search)) {
    $query = "SELECT * FROM tasks 
              WHERE user_id = $user_id AND title LIKE '%$search%' 
              ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM tasks 
              WHERE user_id = $user_id 
              ORDER BY created_at DESC";
}

$result = mysqli_query($conn, $query);

$today = date('Y-m-d');



if (isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    if (!empty($title) && !empty($deadline)) {
        $insert_query = "INSERT INTO tasks (user_id, title, description, deadline) 
                         VALUES ('$user_id', '$title', '$description', '$deadline')";
        mysqli_query($conn, $insert_query);
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Judul dan Tanggal Deadline wajib diisi!</div>";
    }
}

if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id = $task_id");
    header("Location: index.php");
}

$edit_mode = false;
$edit_task = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];

    $edit_result = mysqli_query($conn, "SELECT * FROM tasks WHERE id = $edit_id AND user_id = $user_id");
    if ($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_task = mysqli_fetch_assoc($edit_result);
    }
}

if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    mysqli_query($conn, "UPDATE tasks SET is_done = 1 WHERE id = $task_id");
    header("Location: index.php");
}

if (isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = trim($_POST['deadline']);

    if (!empty($title) && !empty($deadline)) {
        $update_query = "UPDATE tasks SET title='$title', description='$description', deadline='$deadline' 
                         WHERE id='$task_id' AND user_id='$user_id'";
        mysqli_query($conn, $update_query);
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Judul dan Tanggal Deadline wajib diisi!</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            /* user-select: none; */
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-wrapper input[type="text"] {
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="header">
            <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
            <div class="header-content">
                <div class="search-wrapper">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari task..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <div>
                    <a href="auth/logout.php" class="btn btn-danger">Logout</a>
                    <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
        
        <h3 class="mt-4"><?php echo $edit_mode ? 'Edit Task' : 'Add New Task'; ?></h3>
        <form method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="task_id" value="<?php echo $edit_task['id']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="title" class="form-label">Task Title</label>
                <input type="text" class="form-control" name="title"
                    value="<?php echo $edit_mode ? $edit_task['title'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control"
                    name="description"><?php echo $edit_mode ? $edit_task['description'] : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" class="form-control" name="deadline"
                    value="<?php echo $edit_mode ? $edit_task['deadline'] : ''; ?>" required>
            </div>

            <button type="submit" name="<?php echo $edit_mode ? 'update_task' : 'add_task'; ?>" class="btn btn-primary">
                <?php echo $edit_mode ? 'Update Task' : 'Add Task'; ?>
            </button>

            <?php if ($edit_mode): ?>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>


        <h3 class="mt-4">Your Tasks</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = mysqli_fetch_assoc($result)): ?>
                    <tr <?php if ($task['is_done']) {
                        echo 'class="table-success"';
                    } elseif ($task['deadline'] < $today) {
                        echo 'class="table-danger"';
                    }
                    ?>>
                        <td><?php echo $task['id']; ?></td>
                        <td><?php echo $task['title']; ?></td>
                        <td><?php echo $task['description']; ?></td>
                        <td><?php echo $task['deadline']; ?></td>
                        <td>
                            <?php if ($task['is_done']): ?>
                                <button class="btn btn-success btn-sm" disabled>Selesai</button>
                            <?php else: ?>
                                <?php if ($task['deadline'] < $today): ?>
                                    <span class="badge bg-danger">Terlambat</span><br>
                                <?php endif; ?>
                               <a href="?complete=<?php echo $task['id']; ?>" 
                            class="btn btn-success btn-sm" 
                            onclick="return confirm('Apakah Anda yakin ingin menandai tugas ini sebagai selesai?');">
                            Mark as Done
                            </a>
                            <?php endif; ?>

                        </td>
                        <td>
                            <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            <?php if ($task['is_done']): ?>
                                <button class="btn btn-success btn-sm" disabled>Edit</button>
                            <?php else: ?>
                                <a href="?edit=<?php echo $task['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
</body>

</html>