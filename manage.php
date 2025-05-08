<?php
include 'connect.php';
session_start();

// Fetch unique employees with latest check_in and check_out
$query = "SELECT full_name, 
                 MAX(check_in_time) AS latest_check_in, 
                 MAX(check_out_time) AS latest_check_out 
          FROM users 
          WHERE role = 'employee'
          GROUP BY full_name";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

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

        <?php if (!empty($employees)): ?>
            <?php foreach ($employees as $employee): ?>
                <?php 
                // Create a unique identifier based on full name
                $employee_id = 'emp-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($employee['full_name']));
                ?>
                <div class="employee-container" id="<?= $employee_id ?>-container">
                    <div class="profile">
                        <img id="<?= $employee_id ?>-profile-img" 
                             src="profile.jpg"  
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
                                <?= htmlspecialchars($employee['latest_check_in'] ?? '--:--') ?>
                            </span>
                        </div>
                        <div class="attendance-row">
                            <span class="label">Time Out:</span>
                            <span id="<?= $employee_id ?>-time-out">
                                <?= htmlspecialchars($employee['latest_check_out'] ?? '--:--') ?>
                            </span>
                        </div>
                        <div class="attendance-row">
                            <span class="label">Late Time:</span>
                            <span id="<?= $employee_id ?>-late-time">
                                0 minutes
                            </span>
                        </div>
                        <div class="attendance-row">
                            <span class="label">Overtime:</span>
                            <span id="<?= $employee_id ?>-overtime">
                                0 minutes
                            </span>
                        </div>
                        <div class="attendance-row">
                            <span class="label">Deductions:</span>
                            <span id="<?= $employee_id ?>-deductions">
                                $0.00
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No employees found.</p>
        <?php endif; ?>
    </div>
</body>
</html>