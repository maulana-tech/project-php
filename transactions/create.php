<?php
require_once '../includes/config.php';

if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Set page title
$page_title = 'Add Transaction';

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
        // Insert transaction
        $insert_query = "INSERT INTO transactions (user_id, category_id, amount, description, transaction_date) 
                        VALUES ({$user_id}, {$category_id}, {$amount}, '{$description}', '{$transaction_date}')";
        
        if ($conn->query($insert_query) === TRUE) {
            // Set success message
            $_SESSION['message'] = 'Transaction added successfully.';
            $_SESSION['message_type'] = 'success';
            
            // Redirect to transactions page
            redirect(SITE_URL . '/transactions/index.php');
        } else {
            $error = 'Error adding transaction: ' . $conn->error;
        }
    }
}

include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Transaction</h1>
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
                                <option value="<?php echo $category['id']; ?>" <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                        
                        <optgroup label="Expense">
                            <?php foreach ($expense_categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                
                <div>
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount (Rp) *</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>" required>
                </div>
                
                <div>
                    <label for="transaction_date" class="block text-gray-700 text-sm font-bold mb-2">Date *</label>
                    <input type="date" id="transaction_date" name="transaction_date" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($_POST['transaction_date']) ? htmlspecialchars($_POST['transaction_date']) : date('Y-m-d'); ?>" required>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-save mr-2"></i> Save Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>