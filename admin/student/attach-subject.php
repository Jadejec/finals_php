<?php
// Include necessary files and database connection
include('../../functions.php'); // Adjust the path as needed
session_start();

// Handling form submission and registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $studentId = $_POST['student_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];

    // Initialize errors array
    $errors = [];

    // Check if fields are empty
    if (empty($studentId)) {
        $errors[] = "Student ID is required.";
    }

    if (empty($firstName)) {
        $errors[] = "First Name is required.";
    }

    if (empty($lastName)) {
        $errors[] = "Last Name is required.";
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO students (student_id, first_name, last_name) VALUES (:student_id, :first_name, :last_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->execute();

            $_SESSION['success'] = "Student registered successfully!";
            header('Location: students.php'); // Redirect to the list of students or another page
            exit;
        } catch (PDOException $e) {
            $errors[] = "Error occurred while registering student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register a New Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1>Register a New Student</h1>

        <!-- Display error messages if any -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="studentId">Student ID:</label>
                <input type="text" class="form-control" id="studentId" name="student_id" required value="<?php echo isset($studentId) ? htmlspecialchars($studentId) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="first_name" required value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="last_name" required value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
