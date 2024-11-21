<?php
include('../../functions.php');
include('../partials/header.php');

// Initialize error and success messages
$error_message = null;
$success_message = null;

// Fetch student data based on the ID passed in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student details from the database
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $error_message = "Student not found.";
    }
} else {
    $error_message = "No student ID provided.";
}

// Handle student deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    // Perform the deletion
    $delete_query = "DELETE FROM students WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $student_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        $success_message = "Student deleted successfully.";
        // Redirect to register.php after deletion
        header("Location: register.php?success=Student deleted successfully");
        exit;
    } else {
        $error_message = "Error deleting student: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Delete Student</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Delete Student</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="register.php">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                    </ol>
                </nav>

                <!-- Deletion Form -->
                <div class="card mb-2">
                    <div class="card-body">
                        <!-- Display success or error message -->
                        <?php if ($success_message) { ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php } ?>
                        <?php if ($error_message) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>

                        <?php if (isset($student)) { ?>
                            <!-- Show student details in bullet form -->
                            <div class="mb-3">
                                <h5>Student Details:</h5>
                                <ul>
                                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                                </ul>
                            </div>

                            <!-- Confirmation form -->
                            <form method="POST">
                                <div class="form-group">
                                    <p>Are you sure you want to delete this student record?</p>
                                </div>
                                <button type="submit" class="btn btn-danger" name="delete_student">Delete Student Record</button>
                                <a href="register.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('../partials/footer.php'); ?>
</body>
</html>
