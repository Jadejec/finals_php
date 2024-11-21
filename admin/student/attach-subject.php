<?php
include('../../functions.php');  // Include functions.php to access the database connection
include('../partials/header.php');

// Initialize error and success messages
$error_message = null;
$success_message = null;
$student = null;  // Initialize the student variable

// Ensure the student ID is provided in the query string
if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    // Fetch student details from the database using the student ID
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $studentId);  // 's' for string, change if student_id is another type
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();  // Fetch the student data as an associative array

    // Check if student data exists
    if (!$student) {
        $error_message = "Student not found.";
    }
} else {
    $error_message = "No student ID provided.";
}

// Fetch subjects from the database using MySQLi
$query = "SELECT * FROM subjects";
$subjects_result = $conn->query($query);  // This fetches the subjects directly

// Handle form submission for attaching subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['attach_subjects']) && !empty($_POST['selected_subjects'])) {
        $selectedSubjects = $_POST['selected_subjects'];

        // Attach the selected subjects to the student in the database
        foreach ($selectedSubjects as $subjectCode) {
            $sql = "INSERT INTO student_subjects (student_id, subject_code) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $studentId, $subjectCode);  // 'ss' for two strings (student_id, subject_code)
            $stmt->execute();
        }

        $success_message = "Subjects successfully attached to the student.";
        // Redirect to students page
        header('Location: students.php');
        exit;
    } else {
        $error_message = "Please select at least one subject.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Attach Subjects to Student</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Attach Subjects to Student</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Attach Subjects</li>
                    </ol>
                </nav>

                <!-- Check for errors or success messages -->
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <!-- Subject Attachment Form -->
                <?php if ($student): ?> <!-- Check if student data was fetched -->
                    <div class="card mb-2">
                        <div class="card-body">
                            <!-- Form for selecting subjects -->
                            <form method="POST">
                                <div class="form-group">
                                    <label for="studentId">Student ID:</label>
                                    <input type="text" class="form-control" id="studentId" name="studentId" value="<?php echo $student['student_id']; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="studentName">Student Name:</label>
                                    <input type="text" class="form-control" id="studentName" name="studentName" value="<?php echo $student['student_name']; ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Select Subjects:</label>
                                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="selected_subjects[]" value="<?php echo $subject['subject_code']; ?>">
                                            <label class="form-check-label"><?php echo $subject['subject_name']; ?></label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>

                                <button type="submit" class="btn btn-primary" name="attach_subjects">Attach Subjects</button>
                                <a href="students.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No student data available to attach subjects.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>
</body>
</html>
