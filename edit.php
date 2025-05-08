<?php
session_start();
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch record
    $result = $con->query("SELECT * FROM users WHERE id = '$id'");
    $record = $result->fetch_assoc();

    if (isset($_POST['update'])) {
        $date = $_POST['date'];
        $check_in_time = $_POST['check_in_time'];
        $check_out_time = $_POST['check_out_time'];
        $status = $_POST['status'];

        // Update record
        $con->query("UPDATE users SET date = '$date', check_in_time = '$check_in_time', check_out_time = '$check_out_time', status = '$status' WHERE id = '$id'");

        header('Location: records.php');
        exit;
    }
} else {
    header('Location: records.php');
    exit;
}

function convertToFullDate($date) {
    if (empty($date)) {
        return 'N/A';
    }
    return date('Y-m-d', strtotime($date));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Record</h2>
        <form action="" method="post">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo $record['date']; ?>"><br><br>
            <label for="check_in_time">Check In Time:</label>
            <input type="time" id="check_in_time" name="check_in_time" value="<?php echo $record['check_in_time']; ?>"><br><br>
            <label for="check_out_time">Check Out Time:</label>
            <input type="time" id="check_out_time" name="check_out_time" value="<?php echo $record['check_out_time']; ?>"><br><br>
            <label for="status">Status:</label>
            <input type="text" id="status" name="status" value="<?php echo $record['status']; ?>"><br><br>
            <input type="submit" name="update" value="Update">
        </form>
    </div>
</body>
</html>