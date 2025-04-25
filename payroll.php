<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login-ad-use.php");
    exit();
}
include 'con.php';

// Configuration
$regular_hours_per_day = 8;
$regular_end_time = '17:00:00'; // 5:00 PM
$overtime_rate = 70; // 70 pesos per 30-minute block
$basic_salary = 50000; // Monthly basic salary
$working_days_per_month = 22; // Typical working days

// Get payroll period (default to current month)
$pay_period = isset($_GET['period']) ? $_GET['period'] : date('Y-m');
$start_date = date('Y-m-01', strtotime($pay_period));
$end_date = date('Y-m-t', strtotime($pay_period));

// Get user details
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Calculate attendance and overtime for the period
$attendance_data = [];
$total_regular_hours = 0;
$total_overtime_pay = 0;
$total_late_minutes = 0;

// 1. Get all attendance records for the period
$stmt = $con->prepare("SELECT date, check_in_time, check_out_time 
                      FROM users
                      WHERE user_id = ? 
                      AND date BETWEEN ? AND ?");
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// 2. Process each attendance record
while ($row = $result->fetch_assoc()) {
    $check_in = new DateTime($row['date'] . ' ' . $row['check_in_time']);
    $check_out = new DateTime($row['date'] . ' ' . $row['check_out_time']);
    $regular_end = new DateTime($row['date'] . ' ' . $regular_end_time);
    
    // Calculate work duration
    $work_duration = $check_out->diff($check_in);
    $work_hours = $work_duration->h + ($work_duration->i / 60);
    
    // Calculate late arrival (if check-in after 9:00 AM)
    $scheduled_start = new DateTime($row['date'] . ' 09:00:00');
    if ($check_in > $scheduled_start) {
        $late_duration = $check_in->diff($scheduled_start);
        $total_late_minutes += ($late_duration->h * 60) + $late_duration->i;
    }
    
    // Calculate overtime
    $overtime_pay = 0;
    if ($check_out > $regular_end) {
        $overtime_duration = $check_out->diff($regular_end);
        $overtime_minutes = ($overtime_duration->h * 60) + $overtime_duration->i;
        $overtime_blocks = ceil($overtime_minutes / 30);
        $overtime_pay = $overtime_blocks * $overtime_rate;
    }
    
    // Store daily data
    $attendance_data[] = [
        'date' => $row['date'],
        'check_in' => $row['check_in_time'],
        'check_out' => $row['check_out_time'],
        'hours_worked' => $work_hours,
        'overtime_pay' => $overtime_pay
    ];
    
    $total_regular_hours += min($work_hours, $regular_hours_per_day);
    $total_overtime_pay += $overtime_pay;
}

// 3. Calculate deductions
$late_deduction = min(floor($total_late_minutes / 30) * 300, 3000); // Max 3000 deduction
$sss_deduction = 300;
$insurance_deduction = 300;

// 4. Calculate salary components
$daily_rate = $basic_salary / $working_days_per_month;
$hourly_rate = $daily_rate / $regular_hours_per_day;

$attendance_pay = $total_regular_hours * $hourly_rate;
$gross_pay = $basic_salary + $total_overtime_pay - $late_deduction - $sss_deduction - $insurance_deduction;
$net_salary = $gross_pay - ($late_deduction + $sss_deduction + $insurance_deduction);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payslip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .payslip-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 600px;
            margin: 20px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }

        .employee-details {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .employee-details p {
            margin: 8px 0;
            font-size: 14px;
        }

        .salary-breakdown {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .salary-breakdown th, .salary-breakdown td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
        }

        .salary-breakdown th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .salary-breakdown tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total {
            font-weight: bold;
            background-color: #f0f0f0 !important;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .period-selector {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f8ff;
            border-radius: 5px;
        }

        .period-selector label {
            font-weight: bold;
            margin-right: 10px;
        }

        .period-selector input[type="month"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .period-selector button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        .period-selector button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="payslip-container">
    <!-- Period Selection Form -->
    <div class="period-selector">
        <form method="get">
            <label for="period">Select Pay Period:</label>
            <input type="month" id="period" name="period" value="<?php echo $pay_period; ?>" required>
            <button type="submit">Generate Payslip</button>
        </form>
    </div>

    <!-- Header Section -->
    <div class="header">
        <h2>Employee Payslip</h2>
        <p>Pay Period: <?php echo date('F Y', strtotime($pay_period)); ?></p>
    </div>

    <!-- Employee Details -->
    <div class="employee-details">
        <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
        <p><strong>Employee ID:</strong> <?php echo $user_id; ?></p>
        <p><strong>Position:</strong> Web Developer</p>
        <p><strong>Date Issued:</strong> <?php echo date('F j, Y'); ?></p>
        <p><strong>Pay Date:</strong> <?php echo date('F j, Y', strtotime('+3 days')); ?></p>
    </div>

    <!-- Salary Breakdown -->
    <table class="salary-breakdown">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (₱)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Earnings -->
            <tr>
                <td>Basic Salary</td>
                <td>₱<?php echo number_format($basic_salary, 2); ?></td>
            </tr>
            <tr>
                <td>Regular Hours (<?php echo round($total_regular_hours, 2); ?> hrs)</td>
                <td>₱<?php echo number_format($attendance_pay, 2); ?></td>
            </tr>
            <tr>
                <td>Overtime Pay</td>
                <td>₱<?php echo number_format($total_overtime_pay, 2); ?></td>
            </tr>
            
            <!-- Deductions -->
            <tr class="total">
                <td><strong>Gross Pay</strong></td>
                <td><strong>₱<?php echo number_format($basic_salary + $total_overtime_pay, 2); ?></strong></td>
            </tr>
            <tr>
                <td>Late Deduction (<?php echo floor($total_late_minutes / 30); ?> instances)</td>
                <td>-₱<?php echo number_format($late_deduction, 2); ?></td>
            </tr>
            <tr>
                <td>SSS Contribution</td>
                <td>-₱<?php echo number_format($sss_deduction, 2); ?></td>
            </tr>
            <tr>
                <td>Health Insurance</td>
                <td>-₱<?php echo number_format($insurance_deduction, 2); ?></td>
            </tr>
            <tr class="total">
                <td><strong>Total Deductions</strong></td>
                <td><strong>-₱<?php echo number_format($late_deduction + $sss_deduction + $insurance_deduction, 2); ?></strong></td>
            </tr>
            <tr class="total" style="background-color: #e6f7ff;">
                <td><strong>Net Salary</strong></td>
                <td><strong>₱<?php echo number_format($net_salary, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Details -->
    <div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-radius: 5px;">
        <p><strong>Payment Method:</strong> Direct Deposit</p>
        <p><strong>Bank Account:</strong> **** **** **** 4567</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>**This is a system-generated payslip and does not require a signature.**</p>
        <p>For any discrepancies, please contact HR within 7 days of receipt.</p>
    </div>
</div>

</body>
</html>