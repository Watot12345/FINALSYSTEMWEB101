<?php
// Start the session
session_start();
include 'con.php';
// Check if the user is logged in and if the full_name session variable is set
if (!isset($_SESSION['full_name']) || empty($_SESSION['full_name'])) {
    die("Error: User is not logged in or full name is not set.");
}
// At the TOP of your script, after session_start()
$user_id = $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;

// Validate it exists
if (empty($user_id)) {
    die("Error: User ID is required!");
}
// Fetch the full name from the session
$full_name = $_SESSION['full_name'];

$user_query = "SELECT id FROM users WHERE full_name = '$full_name' LIMIT 1";
$user_result = $con->query($user_query);
if($user_result->num_rows > 0){
  $user_row  = $user_result->fetch_assoc();
}else{
  die("error user not found in database");
}

// Handling Time In
if (isset($_POST['time_in'])) {
    $date = date('Y-m-d'); // Current date
    $check_in_time = date('H:i:s'); // Current time

    // Check if the user is already checked in for today
    $check_in_query = "SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_in_time IS NOT NULL";
    $check_in_result = $con->query($check_in_query);
    
    if ($check_in_result->num_rows > 0) {
        // User has already checked in today, so check if they are checking out
    
        // Step 2: Check if the user is trying to check out
        $check_out_result = $con->query("SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_out_time IS NOT NULL");
    
        if ($check_out_result->num_rows > 0) {
            echo "You have already checked out today!";
        } else {
            // User has not checked out yet, check if it's after 5:00 PM (time out logic)
            $check_out_time = '17:00:00'; // The expected check-out time
            $status = "On Time";
    
            if ($check_out_time > $check_in_time && $check_in_time <= '17:30:00') {
                // Check-out time is valid (before 5:30 PM)
                $status = "On Time";
            } else if ($check_out_time > $check_in_time && $check_out_time > '17:30:00') {
                // Check-out time logic: after 5:30 PM (overtime)
                $status = "Overtime";
                $update_overtime = "UPDATE users SET check_out_time = '$check_out_time', status = '$status' WHERE user_id = '$user_id' AND date = '$date'";
                $con->query($update_overtime);
                echo "You are checking out overtime!";
            }
        }
    } else {
        // No check-in record found, so allow check-in
        if ($check_in_time == '08:00:00') {
            // Time-in is exactly 8:00 AM
            $status = 'On Time';
            echo "time-in successfully";
        } else if ($check_in_time < '08:00:00') {
            // Time-in is before 8:00 AM (Under Time)
            $status = 'Under Time';
            echo"time-in successfully";
        } else if ($check_in_time > '08:00:00') {
          $status = 'late';
          echo "time-in successfully";
        }
    
        // Insert the check-in record into the database
        $insert_time_in = "INSERT INTO users (user_id, password, username,full_name, date, check_in_time, status, leave_type, start_date, end_date, reason)
                           VALUES ('$user_id', '', '', '$full_name', '$date', '$check_in_time', '$status',NULL,NULL,NULL,NULL)";
    
        if ($con->query($insert_time_in) === TRUE) {
            echo "Check-in recorded successfully!";
        } else {
            echo "Error: " . $con->error; // Output error message if query fails
        }
    }
    
}

// Handling Time Out
if (isset($_POST['time_out'])) {
    $date = date('Y-m-d'); // Current date
    $check_out_time = date('H:i:s'); // Current time

    // Check if the user has already checked out today
    $check_out_query = "SELECT * FROM users WHERE user_id = '$user_id' AND date = '$date' AND check_out_time IS NULL";
    $check_out_result = $con->query($check_out_query);

    if ($check_out_result->num_rows == 0) {
        echo "You haven't checked in today or already checked out!";
    } else {
        // Update check-out time
        $update_time_out = "UPDATE users SET check_out_time = '$check_out_time' WHERE user_id = '$user_id' AND date = '$date'";
        if ($con->query($update_time_out) === TRUE) {
            echo "Time Out recorded successfully!";
        } else {
            echo "Error: " . $con->error; // Output error message if query fails
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracking</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <style>
        /* Additional styles for attendance page */
        .attendance-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 8px;
            text-align: center;
        }
        
        .live-clock {
            font-size: 2.5rem;
            color: rgb(0, 255, 0);
            margin: 20px 0;
            font-family: monospace;
        }
        
        .attendance-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }
        
        .attendance-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        #time_in_btn {
            background-color: rgb(0, 255, 0);
            color: black;
        }
        
        #time_out_btn {
            background-color: #ff3333;
            color: white;
        }
        
        .attendance-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .footer {
            margin-top: 40px;
            color: white;
            font-size: 0.9rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Logo.png" alt="">
    </div>
    <div class="container">
        <div class="attendance-container">
            <h1 style="color: white; border-bottom: 2px solid rgb(0, 255, 0); padding-bottom: 10px; margin-bottom: 30px;">
                Attendance Tracking
            </h1>
            
            <div class="live-clock" id="liveClock"></div>
            
            <form method="POST" action="attendance.php">
                <div class="attendance-buttons">
                    <button type="submit" name="time_in" id="time_in_btn" class="attendance-btn">Time In</button>
                    <button type="submit" name="time_out" id="time_out_btn" class="attendance-btn">Time Out</button>
                </div>
            </form>
            
            <div class="footer">
                <p>Powered by Siglatrolap Inc.</p>
            </div>
        </div>
    </div>

    <script>
        // Live clock functionality with AM/PM format
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert 24-hour format to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
            document.getElementById('liveClock').textContent = timeString;
        }

        // Update clock every second
        setInterval(updateClock, 1000);

        // Initial clock update
        updateClock();
    </script>
</body>
</html>