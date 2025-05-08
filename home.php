<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Error: Login First"; // Store error message in session
    header("Location: login-ad-use.php"); // Redirect to login page
    exit();
}
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styled.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <title>Dashboard</title>
</head>

<body>
    <div class="logo-left">
        <img src="Logo.png" alt="Siglatrolap">
    </div>
    <nav class="navbar">
      <div class="sidebar-toggle">☰</div>
<div class="sidebar">
  <span class="sidebar-close">X</span>
        <?php
        // Check the role stored in the session
        if ($_SESSION['role'] == 'employee') {
        ?>
            <!-- Employee Navigation -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="attendance.php">ATTENDANCE</a></li>
                <li><a href="payroll.php">PAYSLIP</a></li>
                 <li><a href="submit.php">SUBMIT LEAVE</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        <?php } else { ?>
            <!-- Admin Navigation -->
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="records.php">RECORDS</a></li>
                <li><a href="manage.php">MANAGE PAYSLIP</a></li>
                <li><a href="request.php">LEAVE REQUEST</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        <?php } ?>
    </nav>

    <div class="land-text">
        <p>HI <span class="one-name">
                <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>
            </span>!, WELCOME TO SIGLATROLAP</p>
        <h1>THE SPARKS THAT<span class="solo-one">&nbsp;TRANSFORMS</span> IDEAS INTO <span class="solo-two">REALITY</span></h1>
        <button class="land-page-btn">
            <a href="about.html">ABOUT US</a></a>
        </button>
  
  
  
      <header class="header">
            <div class="logo">
      <section id="land-page1" class="landpage1"> <!-- 1st Page 1 -->
            <h1><span style="color: BLUE; font-weight:800;">EASY&nbsp;</span>TO&nbsp;<span
                        style="color: rgb(0, 255, 0); font-weight:800; padding: 20px;">MANAGE&nbsp;</span>YOUR</h1>
            <br><br><br>
            <h1>DAILY&nbsp;<span style="color: blue; font-weight:800; padding: 20px;">TIME IN&nbsp;</span>AND&nbsp;<span
                        style="color: rgb(0, 255, 0); font-weight:800;">TIME OUT</span></h1>
            <img src="image/landpage1.png" alt="">
      </section>



      <section id="land-page2" class="landpage2"> <!-- 2nd Page 2 -->
            <h1>YOU CAN ABLE TO&nbsp;<br><span style="color: BLUE; font-weight:800; padding-left: 10%;">
                        ACCESS&nbsp;</span>YOUR&nbsp;<span
                        style="color: rgb(0, 255, 0); font-weight:800;">PAYROL&nbsp;</span>
            </h1><br><br><br><br><br><br><br><br>
            <h1>TO SEE YOUR &nbsp;<span style="color: red; font-weight:800;">EARNINGS</span></h1>
            <img src="image/landpage2.png" alt="">
      </section>



      <section id="land-page3" class="landpage3"> <!-- 3rd Page 3 -->
            <h1>INFORMING YOUR DAILY TIME RECORD</h1>
            <h2>ACCURATELY WITH NOTIFICATIONS</h2>
            <img src="landpage3.png" alt="">
      </section>
      <footer class="footer"> <!-- Footer -->
            <h1>All Rights Reserved @2025</h1>
            <a href="">Privacy Policy</a>
            <h2>Need Help? Contact Us</h2>
            <h3>Support</h3>
            <h4>siglatrolapinnovations@gmail.com</h4>
      </footer>
    </div>
    
    
    
    
    <script>
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const closeBtn = document.querySelector('.sidebar-close');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('active');
  });

  document.addEventListener('click', (event) => {
    if (!sidebar.contains(event.target) && event.target !== toggle) {
      sidebar.classList.remove('active');
    }
  });
});
</script>
</body>
</html>

