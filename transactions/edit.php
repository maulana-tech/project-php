<?php
// Include configuration file
require_once '../includes/config.php';

// Check if user is logged in, if not redirect to login page
if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Check if transaction ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = 'Invalid transaction ID.';
    $_SESSION['message_type'] = 'error';
    redirect(SITE_URL . '/transactions/index.php');
}

$transaction_id = $_GET['id'];

// Check if transaction belongs to user
$check_query = "SELECT t.*, c.type as category_type FROM transactions t 
              JOIN categories c ON t.category_id = c.id 
              WHERE t.id = {$transaction_id} AND t.user_id = {$user_id}";
$check_result = $conn->query($check_query);

if ($check_result->num_rows === 0) {
    $_SESSION['message'] = 'You do not have permission to edit this transaction.';
    $_SESSION['message_type'] = 'error';
    redirect(SITE_URL . '/transactions/index.php');
}

$transaction = $check_result->fetch_assoc();

// Set page title
$page_title = 'Edit Transaction';

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY type, name";
$categories_result = $conn->query($categories_query);

// Process form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category_id = sanitize_input($_POST['category_id']);
    $amount = sanitize_input($_POST['amount']);
    $description = sanitize_input($_POST['description']);
    $transaction_date = sanitize_input($_POST['transaction_date']);
    
    // Validate input
    if (empty($category_id) || empty($amount) || empty($transaction_date)) {
        $error = 'Please fill in all required fields';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Amount must be a positive number';
    } else {
        // Update transaction
        $update_query = "UPDATE transactions 
                        SET category_id = {$category_id}, 
                            amount = {$amount}, 
                            description = '{$description}', 
                            transaction_date = '{$transaction_date}' 
                        WHERE id = {$transaction_id} AND user_id = {$user_id}";
        
        if ($conn->query($update_query) === TRUE) {
            // Set success message
            $_SESSION['message'] = 'Transaction updated successfully.';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to transactions page
            redirect(SITE_URL . '/transactions/index.php');
        } else {
            $error = 'Error updating transaction: ' . $conn->error;
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Transaction</h1>
        <a href="<?php echo SITE_URL; ?>/transactions/index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Back to Transactions
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category *</label>
                    <select id="category_id" name="category_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select Category</option>
                        <?php 
                        // Group categories by type
                        $income_categories = [];
                        $expense_categories = [];
                        
                        $categories_result->data_seek(0);
                        while ($category = $categories_result->fetch_assoc()) {
                            if ($category['type'] === 'income') {
                                $income_categories[] = $category;
                            } else {
                                $expense_categories[] = $category;
                            }
                        }
                        ?>
                        
                        <optgroup label="Income">
                            <?php foreach ($income_categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $transaction['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                        
                        <optgroup label="Expense">
                            <?php foreach ($expense_categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $transaction['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                
                <div>
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount (Rp) *</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($transaction['amount']); ?>" required>
                </div>
                
                <div>
                    <label for="transaction_date" class="block text-gray-700 text-sm font-bold mb-2">Date *</label>
                    <input type="date" id="transaction_date" name="transaction_date" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($transaction['transaction_date']); ?>" required>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($transaction['description']); ?></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-save mr-2"></i> Update Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>