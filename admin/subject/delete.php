<?php
include('../../functions.php');
include('../partials/header.php');

// Initialize error and success messages
$error_message = null;
$success_message = null;

// Fetch subject data based on the ID passed in the URL
if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Fetch subject details from the database
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $subject_id);  // Binding the subject ID
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();
    } else {
        $error_message = "Subject not found.";
    }
} else {
    $error_message = "No subject ID provided.";
}

// Handle subject deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subject'])) {
    // Perform the deletion
    $delete_query = "DELETE FROM subjects WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $subject_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        $success_message = "Subject deleted successfully.";
        // Redirect to add.php instead of subjects.php
        header("Location: add.php");
        exit;
    } else {
        $error_message = "Error deleting subject: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Delete Subject</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
                <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Delete Subject</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="add.php">Subjects</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
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

                        <!-- Show subject details in bullet form -->
                        <div class="mb-3">
                            <h5>Subject Details:</h5>
                            <ul>
                                <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                                <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                            </ul>
                        </div>

                        <!-- Confirmation form -->
                        <form method="POST">
                            <div class="form-group">
                                <p>Are you sure you want to delete this subject record?</p>
                            </div>
                            <button type="submit" class="btn btn-danger" name="delete_subject">Delete Subject Record</button>
                            <a href="add.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('../partials/footer.php'); ?>
</body>
</html>
