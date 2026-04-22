<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    // --- ADD STUDENT ---
    if ($action === 'add') {
        $student_id   = trim($_POST['student_id']);
        $student_name = trim($_POST['student_name']);
        $programme    = trim($_POST['programme']);

        // Basic server-side validation
        if (empty($student_id) || empty($student_name) || empty($programme)) {
            $_SESSION['message'] = "Error: All fields are required.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_students.php");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $student_name, $programme);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_name}' added successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            if ($conn->errno == 1062) {
                $_SESSION['message'] = "Error: Student ID '{$student_id}' already exists.";
            } else {
                $_SESSION['message'] = "Database Error: " . $conn->error;
            }
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- BULK IMPORT CSV ---
    if ($action === 'import_csv') {
        // Ensure a file was actually uploaded without errors
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['csv_file']['tmp_name'];
            $fileExtension = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

            if ($fileExtension === 'csv') {
                $handle = fopen($fileTmpPath, "r");
                $successCount = 0;
                $errorCount = 0;

                // Prepare statement outside the loop for massive performance boost
                $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");

                // Loop through every single row in the CSV file
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Check if row has the required 3 columns
                    if (count($data) >= 3) {
                        $sid = trim($data[0]);
                        $name = trim($data[1]);
                        $prog = trim($data[2]);

                        // Skip the header row if their Excel file has titles like "Student ID"
                        if (strtolower($sid) === 'student id' || strtolower($sid) === 'id') continue;

                        if (!empty($sid) && !empty($name) && !empty($prog)) {
                            $stmt->bind_param("sss", $sid, $name, $prog);
                            if ($stmt->execute()) {
                                $successCount++;
                            } else {
                                $errorCount++; // Usually means Duplicate ID
                            }
                        }
                    }
                }
                fclose($handle);
                $stmt->close();

                // Fire off the Toast notifications
                if ($successCount > 0) {
                    $_SESSION['message'] = "Import Complete: {$successCount} students added. " . ($errorCount > 0 ? "({$errorCount} duplicates skipped)" : "");
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Import Failed: No valid rows found or all IDs were duplicates.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Error: Please upload a valid .csv file.";
                $_SESSION['message_type'] = "error";
            }
        }
        header("Location: ../admin/manage_students.php");
        exit();
    }

    // --- UPDATE STUDENT ---
    if ($action === 'update') {
        $student_id   = trim($_POST['student_id']);
        $student_name = trim($_POST['student_name']);
        $programme    = trim($_POST['programme']);

        if (empty($student_name) || empty($programme)) {
            $_SESSION['message'] = "Error: Name and programme cannot be empty.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_students.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE students SET student_name = ?, programme = ? WHERE student_id = ?");
        $stmt->bind_param("sss", $student_name, $programme, $student_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_id}' updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- DELETE STUDENT ---
    if ($action === 'delete') {
        $student_id = trim($_POST['student_id']);

        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_id}' deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting student: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: ../admin/manage_students.php");
    exit();

} else {
    header("Location: ../admin/manage_students.php");
    exit();
}
?>