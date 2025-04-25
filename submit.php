<?php
session_start();


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Error: Login First"; // Store error message in session
    header("Location: login-ad-use.php"); // Redirect to login page
    exit();
}
include 'con.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $user_id = $_SESSION['user_id'];
    $full_name = $_SESSION['full_name']; // Assuming you store full name in session
    
    try {
        $stmt = $con->prepare("INSERT INTO users (user_id, full_name, leave_type, start_date, end_date, reason, status, password, username) VALUES (?, ?, ?, ?, ?, ?, 'pending', '', '')");
$stmt->bind_param("isssss", $user_id, $full_name, $leave_type, $start_date, $end_date, $reason);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Leave request submitted successfully!";
        } else {
            $_SESSION['error'] = "Error submitting leave request.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: submit.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <title>Submit Leave Request</title>
    <style>
        /* Additional styles specific to the leave form */
        .leave-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 8px;
        }
        
        .leave-form label {
            display: block;
            margin: 15px 0 5px;
            color: white;
        }
        
        .leave-form select,
        .leave-form input[type="date"],
        .leave-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid rgb(0, 255, 0);
            background: transparent;
            color: white;
            border-radius: 5px;
        }
        
        .leave-form textarea {
            min-height: 100px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Logo.png" alt="">
    </div>
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message" style="color: rgb(0, 255, 0); padding: 10px; margin: 10px 0; border: 1px solid rgb(0, 255, 0);">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error" style="color: red; padding: 10px; margin: 10px 0; border: 1px solid red;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="leave-form">
            <h1 style="color: white; text-align: center; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 10px;">
                Submit Leave Request
            </h1>
            
            <form action="submit.php" method="POST">
                <label for="leave_type">Leave Type:</label>
                <select name="leave_type" required>
                    <option value="">-- Select Leave Type --</option>
                    <option value="vacation">Vacation</option>
                    <option value="sick">Sick</option>
                    <option value="personal">Personal</option>
                    <option value="other">Other</option>
                </select>

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" required>

                <label for="reason">Reason:</label>
                <textarea name="reason" required placeholder="Please explain the reason for your leave"></textarea>

                <button type="submit" class="toggle-button" style="width: 100%; margin-top: 20px;">
                    Submit Request
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="status.php" style="color: rgb(0, 255, 0);">View My Leave Status</a>
            </p>
        </div>
    </div>
</body>
</html>
