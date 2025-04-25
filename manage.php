<?php
include 'con.php';

// Fetch employees from database
$query = "SELECT id, full_name, check_in_time, check_out_time FROM users WHERE role = 'employee' AND date IS NOT NULL 
    AND date != '0000-00-00'";
$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

// Fetch all employees as an array
$employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <title>Employee Attendance Management</title>
    <script src="online-admin.js" defer></script>
</head>

<body>
    <div class="logo">
        <img src="Logo.png" alt="">
    </div>
 

    <div class="container">
        <h1>Manage Payslip Employee for:</h1>

        <?php foreach ($employees as $employee): ?>
        <!-- Dynamic Employee Container -->
        <?php 
        // Create a unique identifier from the full_name
        $employee_id = 'emp-' . $employee['id'];
        ?>
        <div class="employee-container" id="<?= $employee_id ?>-container">
            <div class="profile">
                <img id="<?= $employee_id ?>-profile-img" 
                     src="profile.jpg"  <!-- Default image since profile_pic isn't in your query -->
                     alt="<?= htmlspecialchars($employee['full_name']) ?> Profile Picture">
                <span id="<?= $employee_id ?>-name" class="name">
                    <?= htmlspecialchars($employee['full_name']) ?>
                </span>
            </div>
            <button onclick="toggleAttendanceInfo('<?= $employee_id ?>')" class="toggle-button">Informations</button>
            <div class="dropdown-container" id="<?= $employee_id ?>-attendance-info" style="display: none;">
                <h3>Attendance Summary:</h3>
                <div class="attendance-row">
                    <span class="label">Employee Name:</span>
                    <span id="<?= $employee_id ?>-employee-name">
                        <?= htmlspecialchars($employee['full_name']) ?>
                    </span>
                </div>
                <div class="attendance-row">
                    <span class="label">Time In:</span>
                    <span id="<?= $employee_id ?>-time-in">
                        <?= htmlspecialchars($employee['check_in_time'] ?? '--:--') ?>
                    </span>
                </div>
                <div class="attendance-row">
                    <span class="label">Time Out:</span>
                    <span id="<?= $employee_id ?>-time-out">
                        <?= htmlspecialchars($employee['check_out_time'] ?? '--:--') ?>
                    </span>
                </div>
                <!-- These fields would need to be in your query or calculated -->
                <div class="attendance-row">
                    <span class="label">Late Time:</span>
                    <span id="<?= $employee_id ?>-late-time">
                        <!-- You would need to calculate this or add to your query -->
                        0 minutes
                    </span>
                </div>
                <div class="attendance-row">
                    <span class="label">Overtime:</span>
                    <span id="<?= $employee_id ?>-overtime">
                        <!-- You would need to calculate this or add to your query -->
                        0 minutes
                    </span>
                </div>
                <div class="attendance-row">
                    <span class="label">Deductions:</span>
                    <span id="<?= $employee_id ?>-deductions">
                        <!-- You would need to calculate this or add to your query -->
                        $0.00
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>