<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$page_title = 'Dashboard';

include_once 'includes/header.php';

// Get financial summary data
$current_month = date('m');
$current_year = date('Y');
$first_day = date('Y-m-01');
$last_day = date('Y-m-t');

// Get total income for current month
$income_query = "SELECT SUM(t.amount) as total_income 
               FROM transactions t 
               JOIN categories c ON t.category_id = c.id 
               WHERE t.user_id = {$user_id} 
               AND c.type = 'income' 
               AND t.transaction_date BETWEEN '{$first_day}' AND '{$last_day}'";
$income_result = $conn->query($income_query);
$income_data = $income_result->fetch_assoc();
$total_income = $income_data['total_income'] ?: 0;

// Get total expenses for current month
$expense_query = "SELECT SUM(t.amount) as total_expense 
                FROM transactions t 
                JOIN categories c ON t.category_id = c.id 
                WHERE t.user_id = {$user_id} 
                AND c.type = 'expense' 
                AND t.transaction_date BETWEEN '{$first_day}' AND '{$last_day}'";
$expense_result = $conn->query($expense_query);
$expense_data = $expense_result->fetch_assoc();
$total_expense = $expense_data['total_expense'] ?: 0;

// Calculate balance
$balance = $total_income - $total_expense;

// Get recent transactions
$recent_transactions_query = "SELECT t.*, c.name as category_name, c.type as category_type 
                            FROM transactions t 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.user_id = {$user_id} 
                            ORDER BY t.transaction_date DESC 
                            LIMIT 5";
$recent_transactions_result = $conn->query($recent_transactions_query);

// Get expense by category for pie chart
$expense_by_category_query = "SELECT c.name, SUM(t.amount) as total 
                            FROM transactions t 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.user_id = {$user_id} 
                            AND c.type = 'expense' 
                            AND t.transaction_date BETWEEN '{$first_day}' AND '{$last_day}' 
                            GROUP BY c.name";
$expense_by_category_result = $conn->query($expense_by_category_query);

// Prepare data for charts
$expense_categories = [];
$expense_amounts = [];

while ($row = $expense_by_category_result->fetch_assoc()) {
    $expense_categories[] = $row['name'];
    $expense_amounts[] = $row['total'];
}

// Get monthly income/expense for the last 6 months for line chart
$months = [];
$monthly_income = [];
$monthly_expense = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('m', strtotime("-{$i} month"));
    $year = date('Y', strtotime("-{$i} month"));
    $month_name = date('M Y', strtotime("{$year}-{$month}-01"));
    
    $months[] = $month_name;
    
    $start_date = "{$year}-{$month}-01";
    $end_date = date('Y-m-t', strtotime($start_date));
    
    // Get monthly income
    $monthly_income_query = "SELECT SUM(t.amount) as total 
                           FROM transactions t 
                           JOIN categories c ON t.category_id = c.id 
                           WHERE t.user_id = {$user_id} 
                           AND c.type = 'income' 
                           AND t.transaction_date BETWEEN '{$start_date}' AND '{$end_date}'";
    $monthly_income_result = $conn->query($monthly_income_query);
    $monthly_income_data = $monthly_income_result->fetch_assoc();
    $monthly_income[] = $monthly_income_data['total'] ?: 0;
    
    // Get monthly expense
    $monthly_expense_query = "SELECT SUM(t.amount) as total 
                            FROM transactions t 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.user_id = {$user_id} 
                            AND c.type = 'expense' 
                            AND t.transaction_date BETWEEN '{$start_date}' AND '{$end_date}'";
    $monthly_expense_result = $conn->query($monthly_expense_query);
    $monthly_expense_data = $monthly_expense_result->fetch_assoc();
    $monthly_expense[] = $monthly_expense_data['total'] ?: 0;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <div class="text-sm text-gray-600">
            <span>Current Month: <?php echo date('F Y'); ?></span>
        </div>
    </div>
    
    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Income Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Total Income</h3>
                    <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_income, 0, ',', '.'); ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Expense Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Total Expense</h3>
                    <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_expense, 0, ',', '.'); ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Balance Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium">Balance</h3>
                    <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($balance, 0, ',', '.'); ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Expense by Category Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense by Category</h3>
            <div class="h-64">
                <canvas id="expensePieChart"></canvas>
            </div>
        </div>
        
        <!-- Income vs Expense Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Income vs Expense (Last 6 Months)</h3>
            <div class="h-64">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
            <a href="<?php echo SITE_URL; ?>/transactions/index.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">View All</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($recent_transactions_result->num_rows > 0): ?>
                        <?php while ($transaction = $recent_transactions_result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($transaction['category_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($transaction['description']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $transaction['category_type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $transaction['category_type'] === 'income' ? '+' : '-'; ?> Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No transactions found. <a href="<?php echo SITE_URL; ?>/transactions/create.php" class="text-blue-500 hover:underline">Add a transaction</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Expense Pie Chart
const expenseCtx = document.getElementById('expensePieChart').getContext('2d');
const expensePieChart = new Chart(expenseCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($expense_categories); ?>,
        datasets: [{
            data: <?php echo json_encode($expense_amounts); ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                '#8AC249', '#EA5F89', '#00D8B6', '#FFB88C', '#955196', '#DD5182'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Income vs Expense Line Chart
const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
const incomeExpenseChart = new Chart(incomeExpenseCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [
            {
                label: 'Income',
                data: <?php echo json_encode($monthly_income); ?>,
                borderColor: '#4BC0C0',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Expense',
                data: <?php echo json_encode($monthly_expense); ?>,
                borderColor: '#FF6384',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>