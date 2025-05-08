<?php
session_start();
include 'connect.php';
// Spam prevention (30-second cooldown)

if (!isset($_SESSION['last_attempt'])) {

    $_SESSION['last_attempt'] = 0; // Initialize
}

$current_time = time();
$cooldown = 10; // 30 seconds

// Check if user is spamming (if last attempt was <30s ago)
if ($current_time - $_SESSION['last_attempt'] < $cooldown) {
    $remaining = $cooldown - ($current_time - $_SESSION['last_attempt']);
    die("Please wait $remaining seconds before trying again.");
}

// Only update last_attempt AFTER successful form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $_SESSION['last_attempt'] = $current_time; // Update timestamp
    // Rest of your registration logic...
}


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
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link rel="stylesheet" href="styled.css">
    <style>
        .login-options {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .login-options button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background: #f0f0f0;
        transition: all 0.3s;
    }

    .login-options button.active {
        background: #4CAF50;
        color: white;
    }

    #qrLoginForm {
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    #reader {
        width: 500px;
        margin: 0 auto;
    }

    #qrMessage {
        margin-top: 15px;
        font-weight: bold;
        text-align: center;
    }
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
    </style>
</head>
<body>
    <div class="logo-section">
        <img src="Logo.png" alt="Company Logo">
    </div>

    <section class="form-login">
    <div class="login-options">
        <button id="manualLoginBtn" class="active"><i class="fas fa-keyboard"></i> Manual Login</button>
        <button id="qrLoginBtn"><i class="fas fa-qrcode"></i> QR Code Login</button>
    </div>
    <form action="login-ad-use.php" method="post" id="manualLoginForm">
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
                <p>&nbsp;<i class="fas fa-user-check"></i>&nbsp;&nbsp;Not Registered? <a href="index.php">&nbsp;Create Account</a></p>
            </div>
        </form>
        <div id="qrLoginForm" style="display: none; text-align: center;">
        <div id="reader"></div>
        <div id="qrMessage"></div>
    </div>
    </section>
    <script>
let html5QrcodeScanner;

function initQRScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
    
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            rememberLastUsedCamera: true
        }
    );
        
    html5QrcodeScanner.render((qrCodeMessage) => {
        // On success
        fetch('verify-qr.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ qr_secret: qrCodeMessage })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'home.php';
            } else {
                document.getElementById('qrMessage').innerHTML = 
                    '<p style="color: red;">Invalid QR Code</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('qrMessage').innerHTML = 
                '<p style="color: red;">Error scanning QR code</p>';
        });
    }, (error) => {
        console.warn(`QR error: ${error}`);
    });
}

// Switch between login methods
document.getElementById('qrLoginBtn').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('manualLoginBtn').classList.remove('active');
    document.getElementById('manualLoginForm').style.display = 'none';
    document.getElementById('qrLoginForm').style.display = 'block';
    initQRScanner();
});

document.getElementById('manualLoginBtn').addEventListener('click', function() {
    this.classList.add('active');
    document.getElementById('qrLoginBtn').classList.remove('active');
    document.getElementById('manualLoginForm').style.display = 'block';
    document.getElementById('qrLoginForm').style.display = 'none';
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
});
</script>


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

