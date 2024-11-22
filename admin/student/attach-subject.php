<?php
include('../../functions.php');
include('../partials/header.php');
include('../partials/side-bar.php');

$success_message = null;
$error_message = null;
$student_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    die("Student ID is required.");
}

// Fetch student information
$student_query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch all subjects
$subjects_query = "SELECT * FROM subjects";
$subjects = $conn->query($subjects_query)->fetch_all(MYSQLI_ASSOC);

// Fetch attached subjects for the student
$attached_query = "SELECT subject_id FROM students_subjects WHERE student_id = ?";
$stmt = $conn->prepare($attached_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attached_subjects = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'subject_id');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_subjects = $_POST['subjects'] ?? [];

    // Clear existing subject associations
    $delete_query = "DELETE FROM students_subjects WHERE student_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    // Insert new associations
    if (!empty($selected_subjects)) {
        $insert_query = "INSERT INTO students_subjects (student_id, subject_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);

        foreach ($selected_subjects as $subject_id) {
            $stmt->bind_param("ii", $student_id, $subject_id);
            $stmt->execute();
        }
    }

    // Refresh the attached subjects after update
    $attached_query = "SELECT subject_id FROM students_subjects WHERE student_id = ?";
    $stmt = $conn->prepare($attached_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $attached_subjects = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'subject_id');

    $success_message = "Subjects updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Attach Subjects</title>
    <style>
        .center-content {
            margin-left: 250px; /* Space for the sidebar */
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 center-content">
        <h1 class="mb-4">Attach Subject to Student</h1>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="students.php">Register Student</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attach Subject</li>
            </ol>
        </nav>

        <!-- Student Information -->
        <div class="card mb-4">
            <div class="card-body">
                <h5>Selected Student Information</h5>
                <ul class="list-unstyled">
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></li>
                </ul>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>
        <?php if ($error_message) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <!-- Subject Selection Form -->
        <form method="POST" action="">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Available Subjects</h5>
                    <?php foreach ($subjects as $subject) { ?>
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                name="subjects[]" 
                                value="<?php echo $subject['id']; ?>" 
                                class="form-check-input" 
                                id="subject-<?php echo $subject['id']; ?>"
                                <?php echo in_array($subject['id'], $attached_subjects) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="subject-<?php echo $subject['id']; ?>">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Attach Subjects</button>
        </form>

        <!-- Subject List -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Subject List</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Grade</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($attached_subjects)) { ?>
                            <?php
                            foreach ($attached_subjects as $subject_id) {
                                // Fetch subject details
                                $subject_query = "SELECT * FROM subjects WHERE id = ?";
                                $stmt = $conn->prepare($subject_query);
                                $stmt->bind_param("i", $subject_id);
                                $stmt->execute();
                                $subject = $stmt->get_result()->fetch_assoc();
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['id']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>-</td> <!-- Placeholder for grade -->
                                    <td>
                                        <a href="detach.php?student_id=<?php echo $student_id; ?>&subject_id=<?php echo $subject_id; ?>" class="btn btn-danger btn-sm">Detach</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" class="text-center">No subjects found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include('../partials/footer.php'); ?>
</body>
</html>
