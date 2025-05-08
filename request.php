<?php
session_start();


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Error: Login First"; // Store error message in session
    header("Location: login-ad-use.php"); // Redirect to login page
    exit();
}
include 'connect.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    } else {
        header("Location: request.php");
        exit();
    }
    
    try {
        $stmt = $con->prepare("UPDATE users SET stat = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Leave request has been $status!";
        } else {
            $_SESSION['error'] = "Error updating leave request.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: request.php");
    exit();
}

// Fetch pending leave requests
$result = $con->query("SELECT id,full_name, leave_type, start_date, end_date, reason FROM users WHERE stat = 'pending' AND leave_type IS NOT NULL AND leave_type != '' ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Leave Requests</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <style>
        /* Additional styles for pending leave requests */
        .pending-requests-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .pending-requests-table th {
            background-color: rgb(0, 255, 0);
            color: black;
        }
        
        .pending-requests-table th, 
        .pending-requests-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid rgb(0, 255, 0);
            color: white;
        }
        
        .action-links {
            display: flex;
            gap: 10px;
        }
        
        .approve {
            color: rgb(0, 255, 0) !important;
            text-decoration: none;
        }
        
        .reject {
            color: red !important;
            text-decoration: none;
        }
        
        .no-requests {
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Logo.png" alt="">
    </div>
    <div class="container">
        <h1 style="color: white; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 10px; margin-bottom: 20px;">
            Pending Leave Requests
        </h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message" style="color: rgb(0, 255, 0); padding: 10px; margin-bottom: 20px; border: 1px solid rgb(0, 255, 0);">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error" style="color: red; padding: 10px; margin-bottom: 20px; border: 1px solid red;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="pending-requests-table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($row['leave_type'] ?? '')); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['start_date'] ?? '')); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['end_date'] ?? '')); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['reason'] ?? '')); ?></td>
                            <td class="action-links">
                                <a href="request.php?action=approve&id=<?php echo $row['id']; ?>" class="approve">Approve</a>
                                <a href="request.php?action=reject&id=<?php echo $row['id']; ?>" class="reject">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-requests">No pending leave requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>