<?php
include('../../functions.php');

// Check if the student ID is provided in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch the student's current details
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        die("Student not found.");
    }
} else {
    die("Invalid student ID.");
}

// Handle the form submission for updating student details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $new_student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Validate the student ID, first name, and last name
    if (!empty($new_student_id) && !empty($first_name) && !empty($last_name)) {
        // Check if the new student ID already exists (excluding the current student)
        $check_query = "SELECT * FROM students WHERE student_id = ? AND id != ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("si", $new_student_id, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Student ID already exists
            $error_message = "Student ID already exists. Please use a unique ID.";
        } else {
            // Update the student details
            $update_query = "UPDATE students SET student_id = ?, first_name = ?, last_name = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $new_student_id, $first_name, $last_name, $student_id);

            if ($stmt->execute()) {
                header("Location: register.php?success=Student updated successfully");
                exit;
            } else {
                $error_message = "Error updating student: " . $conn->error;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Student</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Edit Student</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="register.php">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                    </ol>
                </nav>

                <!-- Edit Form -->
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" name="student_id" id="student_id" class="form-control" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                            </div>
                            <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                            <a href="register.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
