<?php
include('../../functions.php'); 
include('../partials/header.php'); 

// Initialize messages
$success_message = null;
$error_message = null;

// Handle subject addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];

    if (!empty($subject_code) && !empty($subject_name)) {
        // Check if the subject code already exists
        $check_query = "SELECT * FROM subjects WHERE subject_code = '$subject_code'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows > 0) {
            // Subject code already exists
            $error_message = "The subject code '$subject_code' already exists. Please use a unique subject code.";
        } else {
            // Insert the new subject
            $query = "INSERT INTO subjects (subject_code, subject_name) VALUES ('$subject_code', '$subject_name')";
            if ($conn->query($query)) {
                $success_message = "Subject added successfully.";
            } else {
                $error_message = "Error adding subject: " . $conn->error;
            }
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Fetch subjects from the database
$subject_list = [];
$query = "SELECT * FROM subjects";
$result = $conn->query($query);
if ($result) {
    $subject_list = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Add Subject</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
                <?php include('../partials/side-bar.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 mx-auto">
                <h1>Add a New Subject</h1>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                         <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                    </ol>
                </nav>

                <!-- Form to Add Subject -->
                <div class="card mb-2">
                    <div class="card-body">
                        <?php if ($success_message) { ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php } ?>
                        <?php if ($error_message) { ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php } ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="subject_code" class="form-label">Subject Code</label>
                                <input type="text" name="subject_code" id="subject_code" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject_name" class="form-label">Subject Name</label>
                                <input type="text" name="subject_name" id="subject_name" class="form-control" required>
                            </div>
                            <button type="submit" name="add_subject" class="btn btn-primary w-100">Add Subject</button>
                        </form>
                    </div>
                </div>

                <!-- Subject List -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Subject List</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($subject_list)) { ?>
                                    <?php foreach ($subject_list as $subject) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $subject['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                                <a href="delete.php?id=<?php echo $subject['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No subjects found.</td>
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
