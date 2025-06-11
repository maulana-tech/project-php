<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';

// Ensure user is logged in
if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Initialize variables
$message = '';
$messageType = '';

try {
    // Get user data
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("User not found");
    }

    // Get transaction statistics
    $stats_query = "SELECT 
        COUNT(*) as total_transactions,
        COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
        COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expenses
        FROM transactions 
        WHERE user_id = ?";
    $stats_stmt = $conn->prepare($stats_query);
    if (!$stats_stmt) {
        throw new Exception("Stats prepare failed: " . $conn->error);
    }
    $stats_stmt->bind_param("i", $user_id);
    if (!$stats_stmt->execute()) {
        throw new Exception("Stats execute failed: " . $stats_stmt->error);
    }
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            
            if (empty($username) || empty($email)) {
                throw new Exception("Username dan email harus diisi.");
            }

            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Check for duplicate username/email
                $check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $check->bind_param("ssi", $username, $email, $user_id);
                $check->execute();
                $check_result = $check->get_result();
                
                if ($check_result->num_rows > 0) {
                    throw new Exception("Username atau email sudah digunakan.");
                }
                
                // Update profile
                $update = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $update->bind_param("ssi", $username, $email, $user_id);
                
                if (!$update->execute()) {
                    throw new Exception("Gagal memperbarui profil.");
                }
                
                $conn->commit();
                $message = "Profil berhasil diperbarui!";
                $messageType = 'success';
                
                // Refresh user data
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        }
        
        if (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password !== $confirm_password) {
                throw new Exception("Password baru tidak cocok dengan konfirmasi password.");
            }
            
            if (strlen($new_password) < 6) {
                throw new Exception("Password baru harus minimal 6 karakter.");
            }
            
            if (!password_verify($current_password, $user['password'])) {
                throw new Exception("Password saat ini tidak valid.");
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $password_stmt->bind_param("si", $hashed_password, $user_id);
            
            if (!$password_stmt->execute()) {
                throw new Exception("Gagal memperbarui password.");
            }
            
            $message = "Password berhasil diperbarui!";
            $messageType = 'success';
        }
    }
} catch (Exception $e) {
    $message = $e->getMessage();
    $messageType = 'error';
    error_log("Profile Error: " . $e->getMessage());
    
    // Initialize empty stats if there was an error
    if (!isset($stats)) {
        $stats = [
            'total_transactions' => 0,
            'total_income' => 0,
            'total_expenses' => 0
        ];
    }
}

// Set page title
$page_title = 'Profil Pengguna';
include_once '../includes/header.php';
?>

<div class="flex-1 min-h-0 overflow-auto bg-gray-100">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if ($message): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Header -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-32"></div>
                <div class="relative px-6 pb-6">
                    <div class="flex flex-col sm:flex-row items-center">
                        <div class="absolute -top-16">
                            <div class="bg-white p-2 rounded-full">
                                <div class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                                    <i class="fas fa-user-circle text-6xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-20 sm:mt-0 sm:ml-40 text-center sm:text-left">
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h1>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Transactions -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                            <i class="fas fa-exchange-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Transaksi</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['total_transactions']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Income -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <i class="fas fa-arrow-up text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Pemasukan</p>
                            <p class="text-2xl font-semibold text-gray-900">Rp <?php echo number_format($stats['total_income']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Expenses -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-500">
                            <i class="fas fa-arrow-down text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Pengeluaran</p>
                            <p class="text-2xl font-semibold text-gray-900">Rp <?php echo number_format($stats['total_expenses']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Settings -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Update Profile Form -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Update Profil</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <button type="submit" name="update_profile" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Ganti Password</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <button type="submit" name="change_password" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alert messages after 5 seconds
    const alertMessage = document.querySelector('[role="alert"]');
    if (alertMessage) {
        setTimeout(() => {
            alertMessage.style.transition = 'opacity 1s ease-out';
            alertMessage.style.opacity = '0';
            setTimeout(() => alertMessage.remove(), 1000);
        }, 5000);
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>
