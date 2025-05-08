<?php
session_start();
include 'connect.php';

// Spam prevention (30-second cooldown)
if (!isset($_SESSION['last_attempt'])) {
    $_SESSION['last_attempt'] = 0; // Initialize
}

$current_time = time();
$cooldown = 30; // 30 seconds

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

// Register
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input fields
    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        echo '<p style="color: red;">All fields are required!</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p style="color: red;">Invalid email format!</p>';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,64}$/', $password)) {
    echo '<p style="color : red; class="error-message">Password must be 8-64 chars with 1 uppercase, 1 lowercase, 1 number, and 1 special character!</p>';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username already exists
        $stmt = $con->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo '<p style="color: red;position: absolute; margin-left: 49.6%; margin-top: 24%;">Username taken!</p>';
            $stmt->close();
        } else {
            $stmt->close();

            // Check if email already exists
            $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo '<p style="color: red; position: absolute; margin-left: 47.8%; margin-top: 19%;">Email already in use!</p>';
            } else {
                // Modify the insert user query to include qr_secret
if ($stmt->num_rows > 0) {
    echo '<p style="color: red; position: absolute; margin-left: 47.8%; margin-top: 19%;">Email already in use!</p>';
} else {
    $stmt->close();

    // Generate QR secret
    $qr_secret = bin2hex(random_bytes(16));

    // Insert new user with QR secret
    $stmt = $con->prepare("INSERT INTO users (full_name, username, password, email, qr_secret, user_id, leave_type, start_date, end_date, stat, reason) VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL)");
    $stmt->bind_param("sssss", $name, $username, $hashed_password, $email, $qr_secret);

    if ($stmt->execute()) {
        $_SESSION['qr_secret'] = $qr_secret;
        
        echo '<p style="color: green; position: absolute; margin-left: 50.6%; margin-top: 18%;">Successfully registered!</p>';
        echo '<script>
            // Wait for DOM to be ready
            setTimeout(function() {
                // Show QR container
                var qrDiv = document.getElementById("qrcode");
                qrDiv.style.display = "block";
                
                // Clear any existing QR code
                qrDiv.innerHTML = "";
                
                // Generate new QR code
                new QRCode(qrDiv, {
                    text: "' . $qr_secret . '",
                    width: 128,
                    height: 128,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }, 100); // Small delay to ensure DOM is ready
        </script>';
    } else {
        echo '<p style="color: red;">Error: ' . $stmt->error . '</p>';
    }
    $stmt->close();
}

                
            }
        }
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Login</title>
    <link rel="stylesheet" href="styled.css">
    <link rel="shortcut icon" href="Icon.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
   
    
   #qrcode {
        background: white;
        padding: 15px;
        border-radius: 8px;
        width: fit-content;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    #qrcode img {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        height: auto;
    }
        /* Additional styles for icons */
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        
        .input-group i:not(.password-toggle) {
            position: absolute;
            left: 10px;
            top: 38px;
            color: rgb(0, 255, 0);
        }
        
        .input-group input {
            padding-left: 35px !important;
            padding-right: 35px !important; /* Added for toggle icon */
        }
        
        /* Password toggle on the right side */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 45px;
            cursor: pointer;
            color: rgb(0, 255, 0);
        }
        
        /* Special case for password field - lock on left, eye on right */
        .input-group.password-field i.fa-lock {
            left: 10px;
            right: auto;
        }
    </style>
</head>

<body>
    <div class="logo-sections">
        <img src="Logo.png" alt="">
    </div>

    <!-- Form Sign in -->
    <section class="form-signup">
        <form action="index.php" method="POST">
            <div>
            
                <div class="input-group">
                    <label for="name">Name:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Name...">
                </div>
                
                <div class="input-group">
                    <label for="email">Email:</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter email...">
                </div>
                
                <div class="input-group">
                    <label for="username">Username:</label>
                    <i class="fas fa-user-tag"></i>
                    <input type="text" id="username" name="username" placeholder="Enter Username...">
                </div>
                
                <div class="input-group password-field">
                    <label for="password">Password:</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter Password...">
                    <i class="fas fa-eye-slash password-toggle" id="togglePassword"></i>
                </div>
            </div>
            <div class="signup-button">
                <button type="submit" name="register">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
                <br><br>
                <hr>
                <br>
                <button type="submit">
                    <i class="fab fa-google"></i> Connect to Gmail
                </button>
                <p>&nbsp;<i class="fas fa-user-check"></i> Have An Account?<a href="login-ad-use.php">&nbsp;Log In Here</a></p>
            </div>
        </form>
        <div id="qrcode" style="margin: 20px auto; text-align: center; display: none;"></div>
    </section>
    <script>
    // ...existing script code...
    
    // Add this debugging code
    window.onload = function() {
        console.log("QR Container:", document.getElementById('qrcode'));
        <?php if(isset($_SESSION['qr_secret'])): ?>
        console.log("Generating QR for:", "<?php echo $_SESSION['qr_secret']; ?>");
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $_SESSION['qr_secret']; ?>",
            width: 128,
            height: 128,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        document.getElementById("qrcode").style.display = "block";
        <?php endif; ?>
    }
</script>

    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
        // After showing the error, update countdown every second
let remaining = <?php echo $remaining ?? 30; ?>;
const timer = setInterval(() => {
    remaining--;
    document.getElementById("error-message").textContent = `Please wait ${remaining} seconds...`;
    if (remaining <= 0) {
        clearInterval(timer);
        location.reload(); // Refresh to allow submission
    }
}, 1000);
    </script>
</body>
</html>
