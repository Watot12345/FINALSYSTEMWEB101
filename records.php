<?php
session_start();
include 'con.php';

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch Employees

$employees = $con->query("
    SELECT DISTINCT id, full_name 
    FROM users  WHERE role = 'employee' 
    AND date IS NOT NULL 
    AND date != '0000-00-00'
");

function convertTo12HourFormat($time) {
    if (empty($time) || $time == '00:00:00') {
        return 'N/A';
    }
    return date('h:i:s A', strtotime($time));
}

function convertToFullDate($date) {
    if (empty($date) || $date == '0000-00-00') {
        return 'N/A';
    }
    return date('F j, Y', strtotime($date));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Record</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
</head>
<body>
    <div class="logo">
        <img src="Logo.png" alt="">
    </div>
    <div class="container">
        <h1>Employee Attendance Record</h1>
<br><br><br>
        <!-- Employee List -->
        <div class="employee-list">
            <h3 style="color: white; margin-bottom: 15px; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 5px;">Employee List:</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php while ($row = $employees->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $row['id']; ?>" 
                       style="display: inline-block; 
                              padding: 8px 15px;
                              background-color: rgb(0, 255, 0);
                              color: black;
                              text-decoration: none;
                              border-radius: 5px;
                              margin-bottom: 10px;">
                        <?php echo htmlspecialchars($row['full_name']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Attendance Record -->
        <?php if (isset($_GET['user_id'])): ?>
            <?php
            $employee_id = (int)$_GET['user_id'];
            
            // Get employee name first
            $stmt = $con->prepare("SELECT full_name FROM users WHERE id = ?");
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $employee_result = $stmt->get_result();
            $employee = $employee_result->fetch_assoc();
            $employee_name = $employee['full_name'] ?? 'Unknown Employee';
            
            // Get attendance records (only where date is not empty)
            $stmt = $con->prepare("SELECT * FROM users WHERE id = ? AND date IS NOT NULL AND date != '0000-00-00' ORDER BY date DESC");
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <div class="attendance-record" style="margin-top: 30px;">
                <h3 style="color: white; margin-bottom: 15px; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 5px;">
                    <?php echo htmlspecialchars($employee_name); ?>'s Attendance Records
                </h3>
                <table class="leave-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody style="color: white;">
                        <?php if ($result->num_rows > 0) : ?>
                            <?php while ($record = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo convertToFullDate($record['date']); ?></td>
                                <td><?php echo convertTo12HourFormat($record['check_in_time']); ?></td>
                                <td><?php echo convertTo12HourFormat($record['check_out_time']); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                <td class="action-links">
                                    <a href="edit.php?id=<?php echo $record['id']; ?>" style="color: rgb(0, 255, 0);">Edit</a> | 
                                    <a href="delete.php?id=<?php echo $record['id']; ?>" 
                                       style="color: rgb(255, 0, 0);" 
                                       onclick="return confirm('Are you sure you want to delete this record?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: white;">No attendance records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>