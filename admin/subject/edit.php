<?php
include('../../functions.php'); 
include('../partials/header.php'); 

// Initialize messages
$success_message = null;
$error_message = null;

// Fetch the subject to be edited
$subject = null;
if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $subject = $result->fetch_assoc();
    } else {
        $error_message = "Subject not found.";
    }
}

// Handle subject update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];
    $subject_id = $_POST['subject_id'];
  
    if (!empty($subject_code) && !empty($subject_name)) {
        // Check if the subject code already exists
        $check_query = "SELECT * FROM subjects WHERE subject_code = ? AND id != ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("si", $subject_code, $subject_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // Subject code already exists
            $error_message = "The subject code '$subject_code' already exists. Please use a unique subject code.";
        } else {
            // Update the subject
            $update_query = "UPDATE subjects SET subject_code = ?, subject_name = ? WHERE id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("ssi", $subject_code, $subject_name, $subject_id);
            
            if ($stmt_update->execute()) {
                $success_message = "Subject updated successfully.";
                // Trigger the redirect to add.php using JavaScript after the successful update
                echo "<script>window.location.href = 'add.php';</script>";
                exit();
            } else {
                $error_message = "Error updating subject: " . $conn->error;
            }
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Edit Subject</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../partials/side-bar.php'); ?>

            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Edit Subject</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="add.php">Subject List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
                    </ol>
                </nav>

                <!-- Form to Edit Subject -->
                <div class="card mb-2">
                    <div class="card-body">
                        <?php if ($success_message) { ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php } ?>
                        <?php if ($error_message) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>
                        <?php if ($subject) { ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="subject_code" class="form-label">Subject Code</label>
                                    <input type="text" name="subject_code" id="subject_code" class="form-control" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subject_name" class="form-label">Subject Name</label>
                                    <input type="text" name="subject_name" id="subject_name" class="form-control" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
                                </div>
                                <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                <button type="submit" name="update_subject" class="btn btn-primary w-100">Update Subject</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php include('../partials/footer.php'); ?>
