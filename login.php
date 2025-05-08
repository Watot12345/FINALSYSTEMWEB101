<?php
session_start();  // Start session at the very top
include 'db.php'; // Include your database connection

// Function to validate input (from your existing code)
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which form was submitted
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
        // LOGIN LOGIC
        $email = validate_input($_POST['email']);
        $password = validate_input($_POST['password']);

        // Check for empty fields
        if (empty($email)) {
            $em = "Email is required";
            header("Location: login.php?error=$em");
            exit();
        } else if (empty($password)) {
            $em = "Password is required";
            header("Location: login.php?error=$em");
            exit();
        } else {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $users = $result->fetch_assoc();
                $emaildb = $users["email"];
                $passworddb = $users["password"];
                $role = $users['role'];
                $id = $users['id'];

                // Verify password
                if (password_verify($password, $passworddb)) {
                    $_SESSION['role'] = $role;
                    $_SESSION['id'] = $id;
                    $_SESSION['email'] = $emaildb;

                    // Redirect based on role
                    if ($role == "admin") {
                        header("Location: index.php");
                        exit();
                    } else if ($role == 'employee') {
                        header("Location: index.php");
                        exit();
                    } else {
                        $em = "Incorrect username or password";
                        header("Location: login.php?error=$em");
                        exit();
                    }
                } else {
                    $em = "Incorrect username or password";
                    header("Location: login.php?error=$em");
                    exit();
                }
            } else {
                $em = "Incorrect email or password";
                header("Location: login.php?error=$em");
                exit();
            }
        }
    } 
    elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'register') {
        // REGISTRATION LOGIC
        $email = validate_input($_POST['email']);
        $password = validate_input($_POST['password']);
        $confirmPassword = validate_input($_POST['confirmPassword']);

        // Check if passwords match
        if ($password !== $confirmPassword) {
            header("Location: login.php?error=Passwords do not match");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into database
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'employee')");
        $stmt->bind_param("ss", $email, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: login.php?success=Account registered successfully");
        } else {
            header("Location: login.php?error=Registration failed. Email might already be in use.");
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGLATROLAP - Authentication Portal</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleForms() {
            var loginForm = document.getElementById('loginForm');
            var signupForm = document.getElementById('signupForm');

            if (loginForm.classList.contains('hidden')) {
                loginForm.classList.remove('hidden');
                signupForm.classList.add('hidden');
            } else {
                loginForm.classList.add('hidden');
                signupForm.classList.remove('hidden');
            }
        }
    </script>
</head>
<body>
    <div class="background">
        <!-- LOGIN FORM -->
        <div class="login-container" id="loginForm">
            <h1>SIGLATROLAP</h1>
            <h2>LOGIN</h2>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo stripslashes($_GET['error']); ?>   
                </div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo stripslashes($_GET['success']); ?>   
                </div>
            <?php } ?>

            <form method="POST" action="login.php">
                <!-- Identify the form type as 'login' -->
                <input type="hidden" name="form_type" value="login">

                <div class="form-group">
                    <label for="loginEmail">Email:</label>
                    <input type="email" id="loginEmail" name="email" >
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password:</label>
                    <input type="password" id="loginPassword" name="password" >
                </div>
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" id="rememberMe"> Remember Me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password</a>
                </div>
                <button type="submit" class="submit-btn">LOG IN</button>
                <p class="switch-form">Not Register?&nbsp;<a href="#" onclick="toggleForms()">Register Now</a></p>
            </form>
        </div>

        <!-- REGISTRATION FORM -->
        <div class="signup-container hidden" id="signupForm">
            <h1>SIGLATROLAP</h1>
            <h2>ACCOUNT REGISTRATION</h2>
            <form method="POST" action="login.php">
                <!-- Identify the form type as 'register' -->
                <input type="hidden" name="form_type" value="register">

                <div class="form-group">
                    <label for="signupEmail">EMAIL ADDRESS:</label>
                    <input type="email" id="signupEmail" name="email" >
                </div>
                <div class="form-group">
                    <label for="createPassword">ENTER PASSWORD:</label>
                    <input type="password" id="createPassword" name="password" >
                </div>
                <div class="form-group">
                    <label for="confirmPassword">CONFIRM PASSWORD:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" >
                </div>
                <button type="submit" class="submit-btn">REGISTER ACCOUNT</button>
                <p class="switch-form">Have Account?&nbsp;<a href="#" onclick="toggleForms()">Login Now</a></p>
            </form>
        </div>
    </div>
</body>
</html>
