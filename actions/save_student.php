<?php
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
        $student_id   = strtoupper(trim($_POST['student_id']));
        $student_name = trim($_POST['student_name']);
        $programme    = trim($_POST['programme']);

        if (empty($student_id) || empty($student_name) || empty($programme)) {
            $_SESSION['message'] = "Error: All fields are required.";
            $_SESSION['message_type'] = "error";
            header("Location: ../admin/manage_students.php");
            exit();
        }

        // Check for duplicate ID BEFORE attempting insert
        $check = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $check->bind_param("s", $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Error: Student ID '{$student_id}' already exists. Please use a unique ID.";
            $_SESSION['message_type'] = "error";
            $check->close();
            header("Location: ../admin/manage_students.php");
            exit();
        }
        $check->close();

        // Safe to insert
        $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $student_id, $student_name, $programme);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '{$student_name}' added successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Database Error: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }

    // --- BULK IMPORT CSV ---
    if ($action === 'import_csv') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath   = $_FILES['csv_file']['tmp_name'];
            $fileExtension = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

            if ($fileExtension === 'csv') {
                $handle       = fopen($fileTmpPath, "r");
                $successCount = 0;
                $errorCount   = 0;
                $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Ensure the row isn't blank and has exactly 3 columns
                    if (array_filter($data) && count($data) >= 3) {
                        $sid  = trim($data[0]);
                        $name = trim($data[1]);
                        $prog = trim($data[2]);
                        
                        // Skip header rows or empty IDs
                        if (strtolower($sid) === 'student id' || strtolower($sid) === 'id' || empty($sid)) continue;
                        
                        // Ensure name and programme aren't empty before executing
                        if (!empty($name) && !empty($prog)) {
                            $stmt->bind_param("sss", $sid, $name, $prog);
                            // Execute logic: count successes and errors
                            if ($stmt->execute()) { 
                                $successCount++; 
                            } else { 
                                $errorCount++; 
                            }
                        }
                    }
                }
                fclose($handle);
                $stmt->close();

                if ($successCount > 0) {
                    $_SESSION['message'] = "Import Complete: {$successCount} students added." . ($errorCount > 0 ? " ({$errorCount} duplicates skipped)" : "");
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