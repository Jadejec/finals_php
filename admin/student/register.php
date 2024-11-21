<?php
include('../../functions.php');
include('../partials/header.php');

// Handle student addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    if (!empty($student_id) && !empty($first_name) && !empty($last_name)) {
        $query = "INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $student_id, $first_name, $last_name);
        if ($stmt->execute()) {
            $success_message = "Student added successfully.";
        } else {
            $error_message = "Error adding student: " . $conn->error;
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Fetch student list
$student_list = [];
$query = "SELECT * FROM students";
$result = $conn->query($query);
if ($result) {
    $student_list = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register Student</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Register a New Student</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Register Student</li>
                    </ol>
                </nav>

                <!-- Registration Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <?php if (isset($success_message)) { ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php } ?>
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" name="student_id" id="student_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                            <button type="submit" name="add_student" class="btn btn-primary w-100">Add Student</button>
                        </form>
                    </div>
                </div>

                <!-- Student List -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Student List</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($student_list)) { ?>
                                    <?php foreach ($student_list as $student) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                                <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                                <a href="attach-subject.php?student_id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No students found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php include('../partials/footer.php'); ?>
