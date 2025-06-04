<?php
require_once '../includes/config.php';
// Check if user is already logged in, if yes redirect to dashboard
if (is_logged_in()) {
    redirect(SITE_URL . '/index.php');
}

// Set page title
$page_title = 'Register';

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = '{$username}' OR email = '{$email}'";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $insert_query = "INSERT INTO users (username, email, password) VALUES ('{$username}', '{$email}', '{$hashed_password}')";
            
            if ($conn->query($insert_query) === TRUE) {
                $success = true;
                
                // Set success message
                $_SESSION['message'] = 'Registration successful! You can now login.';
                $_SESSION['message_type'] = 'success';
                
                // Redirect to login page
                redirect(SITE_URL . '/auth/login.php');
            } else {
                $error = 'Registration failed: ' . $conn->error;
            }
        }
    }
}

include_once '../includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 py-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create an Account</h1>
                <p class="text-gray-600 mt-2">Fill in the form below to register</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>Registration successful! You can now <a href="<?php echo SITE_URL; ?>/auth/login.php" class="font-bold underline">login</a>.</p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                        <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <p class="text-gray-500 text-xs mt-1">Password must be at least 6 characters long</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                            Register
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">
                            Already have an account? <a href="<?php echo SITE_URL; ?>/auth/login.php" class="text-indigo-600 hover:text-indigo-800">Login</a>
                        </p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>