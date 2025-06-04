<?php
require_once '../includes/config.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/index.php');
}
$page_title = 'Login';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $query = "SELECT id, username, password, role FROM users WHERE username = '{$username}' OR email = '{$username}'";
        $result = $conn->query($query);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Set success message
                $_SESSION['message'] = 'Login successful. Welcome back!';
                $_SESSION['message_type'] = 'success';
                
                // Redirect to dashboard
                redirect(SITE_URL . '/index.php');
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found';
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 py-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Login to Your Account</h1>
                <p class="text-gray-600 mt-2">Enter your credentials to access your dashboard</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username or Email</label>
                    <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        Login
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-gray-600 text-sm">
                        Don't have an account? <a href="<?php echo SITE_URL; ?>/auth/register.php" class="text-indigo-600 hover:text-indigo-800">Register</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>