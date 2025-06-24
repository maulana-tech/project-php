<?php
require_once '../includes/config.php';

if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Set page title
$page_title = 'Transactions';

// Handle transaction deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $transaction_id = $_GET['delete'];
    
    // Check if transaction belongs to user
    $check_query = "SELECT id FROM transactions WHERE id = {$transaction_id} AND user_id = {$user_id}";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Delete transaction
        $delete_query = "DELETE FROM transactions WHERE id = {$transaction_id}";
        if ($conn->query($delete_query) === TRUE) {
            $_SESSION['message'] = 'Transaction deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error deleting transaction: ' . $conn->error;
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'You do not have permission to delete this transaction.';
        $_SESSION['message_type'] = 'error';
    }
    
    // Redirect to remove the delete parameter from URL
    redirect(SITE_URL . '/transactions/index.php');
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filtering options
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build the query conditions
$conditions = ["t.user_id = {$user_id}"];

if ($filter_type) {
    $conditions[] = "c.type = '{$filter_type}'";
}

if ($filter_category) {
    $conditions[] = "t.category_id = {$filter_category}";
}

if ($filter_date_from) {
    $conditions[] = "t.transaction_date >= '{$filter_date_from}'";
}

if ($filter_date_to) {
    $conditions[] = "t.transaction_date <= '{$filter_date_to}'";
}

if ($search) {
    $conditions[] = "(t.description LIKE '%{$search}%' OR c.name LIKE '%{$search}%')";
}

$where_clause = implode(' AND ', $conditions);

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM transactions t JOIN categories c ON t.category_id = c.id WHERE {$where_clause}";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get transactions with pagination
$transactions_query = "SELECT t.*, c.name as category_name, c.type as category_type 
                      FROM transactions t 
                      JOIN categories c ON t.category_id = c.id 
                      WHERE {$where_clause} 
                      ORDER BY t.transaction_date DESC 
                      LIMIT {$offset}, {$records_per_page}";
$transactions_result = $conn->query($transactions_query);

// Get categories for filter dropdown
$categories_query = "SELECT * FROM categories ORDER BY type, name";
$categories_result = $conn->query($categories_query);

include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Transactions</h1>
        <a href="<?php echo SITE_URL; ?>/transactions/create.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i> Add Transaction
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Transactions</h2>
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Type</label>
                <select id="type" name="type" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All Types</option>
                    <option value="income" <?php echo $filter_type === 'income' ? 'selected' : ''; ?>>Income</option>
                    <option value="expense" <?php echo $filter_type === 'expense' ? 'selected' : ''; ?>>Expense</option>
                </select>
            </div>
            
            <div>
                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <select id="category" name="category" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All Categories</option>
                    <?php 
                    $categories_result->data_seek(0);
                    while ($category = $categories_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $filter_category == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?> (<?php echo ucfirst($category['type']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-gray-700 text-sm font-bold mb-2">Date From</label>
                <input type="date" id="date_from" name="date_from" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $filter_date_from; ?>">
            </div>
            
            <div>
                <label for="date_to" class="block text-gray-700 text-sm font-bold mb-2">Date To</label>
                <input type="date" id="date_to" name="date_to" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $filter_date_to; ?>">
            </div>
            
            <div class="md:col-span-2">
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Search</label>
                <input type="text" id="search" name="search" placeholder="Search by description or category" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="<?php echo SITE_URL; ?>/transactions/index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-times mr-2"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>
    
    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if ($transactions_result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($transaction = $transactions_result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($transaction['category_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($transaction['description']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $transaction['category_type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $transaction['category_type'] === 'income' ? '+' : '-'; ?> Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo SITE_URL; ?>/transactions/edit.php?id=<?php echo $transaction['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/transactions/index.php?delete=<?php echo $transaction['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span class="font-medium"><?php echo min($offset + $records_per_page, $total_records); ?></span> of <span class="font-medium"><?php echo $total_records; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?><?php echo $filter_type ? '&type=' . $filter_type : ''; ?><?php echo $filter_category ? '&category=' . $filter_category : ''; ?><?php echo $filter_date_from ? '&date_from=' . $filter_date_from : ''; ?><?php echo $filter_date_to ? '&date_to=' . $filter_date_to : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?><?php echo $filter_type ? '&type=' . $filter_type : ''; ?><?php echo $filter_category ? '&category=' . $filter_category : ''; ?><?php echo $filter_date_from ? '&date_from=' . $filter_date_from : ''; ?><?php echo $filter_date_to ? '&date_to=' . $filter_date_to : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?><?php echo $filter_type ? '&type=' . $filter_type : ''; ?><?php echo $filter_category ? '&category=' . $filter_category : ''; ?><?php echo $filter_date_from ? '&date_from=' . $filter_date_from : ''; ?><?php echo $filter_date_to ? '&date_to=' . $filter_date_to : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="p-6 text-center text-gray-500">
                No transactions found.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>