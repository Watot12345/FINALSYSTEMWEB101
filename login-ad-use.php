<?php
session_start();
include 'con.php';
// Function to confirm password
function confirmPassword($password, $confirmPassword) {
    return $password === $confirmPassword;
}

// Display error message if exists
if (isset($_SESSION['error'])) {
    echo '<p style="color: red; text-align: center; font-weight: bold;">' . htmlspecialchars($_SESSION['error']) . '</p>';
    unset($_SESSION['error']); // Clear after displaying
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // ✅ Validate empty fields
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: login-ad-use.php"); // Redirect back to login page
        exit();
    }

    // ✅ Check if passwords match
    if (!confirmPassword($password, $confirmPassword)) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: login-ad-use.php"); // Redirect back to login page
        exit();
    }

    // ✅ Check user in database
    $stmt = $con->prepare("SELECT id, full_name, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        // ✅ Verify password
        if (password_verify($password, $hashed_password)) {
            // ✅ Set Session Variables
            $_SESSION["user_id"] = $id;
            $_SESSION["full_name"] = $full_name;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role; // Store the role

            // ✅ Redirect to dashboard after login
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password!";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }

    $stmt->close();
    $con->close();
    header("Location: login-ad-use.php"); // Redirect back to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <link rel="stylesheet" href="styled.css">
    <style>
      /* Password container styling */
.password-container {
    position: relative;
    margin-bottom: 20px; /* Adjust spacing as needed */
}

/* Eye icon styling */
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: rgb(0, 255, 0);
    z-index: 2;
}

/* Adjust input padding to prevent text under icon */
.password-container input[type="password"],
.password-container input[type="text"] {
    padding-right: 40px; /* Make space for the icon */
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
}
/* Username container - matches password container */
.username-container {
    position: relative;
    margin-bottom: 20px;
}

/* Username icon - matches password toggle style */
.username-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: rgb(0, 255, 0);
    font-size: 16px;
}

/* Input field styling - identical to password fields */
.username-container input[type="text"] {
    width: 100%;
    padding: 12px 40px 12px 15px; /* Extra right padding for icon */
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: border 0.3s;
}

.username-container input[type="text"]:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}
    </style>
</head>
<body>
    <div class="logo-section">
        <img src="Logo.png" alt="Company Logo">
    </div>

    <section class="form-login">
        <form action="login-ad-use.php" method="post">
            <div>
 <div class="input-container">
    <label for="username">Username:</label>
    <div class="username-container">
        <input type="text" id="username" name="username" placeholder="Enter Username..." autocomplete="off">
        <i class="fas fa-user username-icon"></i>
    </div>
</div>
                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter Password..." autocomplete="off" >
                    <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>

                <label for="confirm_password">Confirm Password:</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password..." autocomplete="off" >
                    <i class="fas fa-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                </div>
                
                <a href="forgot.php" class="forgot-password">Forgot password?</a>
            </div>

            <div class="form-button">
                <button type="submit" name="submit" value="Login"><i class="fas fa-user-plus"></i>&nbsp;Log In</button>
                <br><br>
                <hr><br>
                <button type="button"><i class="fab fa-google"></i>&nbsp;&nbsp;Connect to Gmail</button>
                <p>&nbsp;<i class="fas fa-user-check"></i>&nbsp;&nbsp;Not Registered? <a href="signup-ad-use.php">&nbsp;Create Account</a></p>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const confirmPassword = document.querySelector('#confirm_password');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle the icon
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });

            toggleConfirmPassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                
                // Toggle the icon
                this.classList.toggle('fa-eye-slash');
                this.classList.toggle('fa-eye');
            });
        });
    </script>
</body>
</html>

