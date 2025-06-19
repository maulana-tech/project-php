<?php
require_once '../includes/config.php';

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Set page title
$page_title = 'Financial Reports';

// Handle form submission for generating reports
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$category_type = isset($_GET['category_type']) ? $_GET['category_type'] : 'all';
$category_id = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'summary';

// Get categories for filter dropdown
$categories_query = "SELECT * FROM categories ORDER BY type, name";
$categories_result = $conn->query($categories_query);

// Build the query conditions
$conditions = ["t.user_id = {$user_id}", "t.transaction_date BETWEEN '{$start_date}' AND '{$end_date}'"];

if ($category_type && $category_type !== 'all') {
    $conditions[] = "c.type = '{$category_type}'";
}

if ($category_id) {
    $conditions[] = "t.category_id = {$category_id}";
}

$where_clause = implode(' AND ', $conditions);

// Get transactions for the report
$transactions_query = "SELECT t.*, c.name as category_name, c.type as category_type 
                      FROM transactions t 
                      JOIN categories c ON t.category_id = c.id 
                      WHERE {$where_clause} 
                      ORDER BY t.transaction_date DESC";
$transactions_result = $conn->query($transactions_query);

// Calculate summary data
$total_income = 0;
$total_expense = 0;
$transactions = [];

while ($row = $transactions_result->fetch_assoc()) {
    $transactions[] = $row;
    
    if ($row['category_type'] === 'income') {
        $total_income += $row['amount'];
    } else {
        $total_expense += $row['amount'];
    }
}

$balance = $total_income - $total_expense;

// Get income by category
$income_by_category_query = "SELECT c.name, SUM(t.amount) as total 
                           FROM transactions t 
                           JOIN categories c ON t.category_id = c.id 
                           WHERE t.user_id = {$user_id} 
                           AND c.type = 'income' 
                           AND t.transaction_date BETWEEN '{$start_date}' AND '{$end_date}' 
                           GROUP BY c.name 
                           ORDER BY total DESC";
$income_by_category_result = $conn->query($income_by_category_query);

// Get expense by category
$expense_by_category_query = "SELECT c.name, SUM(t.amount) as total 
                            FROM transactions t 
                            JOIN categories c ON t.category_id = c.id 
                            WHERE t.user_id = {$user_id} 
                            AND c.type = 'expense' 
                            AND t.transaction_date BETWEEN '{$start_date}' AND '{$end_date}' 
                            GROUP BY c.name 
                            ORDER BY total DESC";
$expense_by_category_result = $conn->query($expense_by_category_query);

// --- Handle export functionality (Updated Section) ---
$export_format = isset($_GET['export']) ? $_GET['export'] : '';

if ($export_format) {
    $filename = 'financial_report_' . date('Y-m-d') . '.' . $export_format;
    
    // Pastikan output tidak ada sebelum header dikirim
    ob_clean(); 

    if ($export_format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Date', 'Category', 'Type', 'Amount', 'Description']);
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction['transaction_date'],
                $transaction['category_name'],
                $transaction['category_type'],
                $transaction['amount'],
                $transaction['description']
            ]);
        }
        
        fputcsv($output, []);
        fputcsv($output, ['Total Income', $total_income]);
        fputcsv($output, ['Total Expense', $total_expense]);
        fputcsv($output, ['Balance', $balance]);
        
        fclose($output);
        exit;
    } elseif ($export_format === 'xlsx') { 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Category');
        $sheet->setCellValue('C1', 'Type');
        $sheet->setCellValue('D1', 'Amount');
        $sheet->setCellValue('E1', 'Description');

        // Isi data transaksi
        $row_num = 2;
        foreach ($transactions as $transaction) {
            $sheet->setCellValue('A' . $row_num, $transaction['transaction_date']);
            $sheet->setCellValue('B' . $row_num, $transaction['category_name']);
            $sheet->setCellValue('C' . $row_num, $transaction['category_type']);
            $sheet->setCellValue('D' . $row_num, $transaction['amount']);
            $sheet->setCellValue('E' . $row_num, $transaction['description']);
            $row_num++;
        }
        $row_num++;
        // Tambah ringkasan
        $sheet->setCellValue('C' . $row_num, 'Total Income');
        $sheet->setCellValue('D' . $row_num, $total_income);
        $row_num++;
        $sheet->setCellValue('C' . $row_num, 'Total Expense');
        $sheet->setCellValue('D' . $row_num, $total_expense);
        $row_num++;
        $sheet->setCellValue('C' . $row_num, 'Balance');
        $sheet->setCellValue('D' . $row_num, $balance);
        
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } elseif ($export_format === 'pdf') {
        ob_start(); 
        ?>
        <style>
            body { font-family: sans-serif; font-size: 10pt; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .summary-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; display: inline-block; width: 30%; margin-right: 1%; }
            .text-green-600 { color: #28a745; }
            .text-red-600 { color: #dc3545; }
            .text-blue-600 { color: #007bff; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
        </style>
        <h1>Financial Report</h1>
        <p><strong>Period:</strong> <?php echo $start_date; ?> to <?php echo $end_date; ?></p>
        <p><strong>Report Type:</strong> <?php echo ucfirst($report_type); ?></p>

        <h2>Financial Summary</h2>
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div class="summary-box" style="background-color: #e6ffe6;">
                <h3>Total Income</h3>
                <p class="text-green-600" style="font-size: 1.5em; font-weight: bold;"><?php echo number_format($total_income, 2); ?></p>
            </div>
            <div class="summary-box" style="background-color: #ffe6e6;">
                <h3>Total Expenses</h3>
                <p class="text-red-600" style="font-size: 1.5em; font-weight: bold;"><?php echo number_format($total_expense, 2); ?></p>
            </div>
            <div class="summary-box" style="background-color: #e6f2ff;">
                <h3>Balance</h3>
                <p class="<?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>" style="font-size: 1.5em; font-weight: bold;">
                    <?php echo number_format($balance, 2); ?>
                </p>
            </div>
        </div>

        <?php if ($total_income > 0 || $total_expense > 0): ?>
        <h2>Category Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Income Amount</th>
                    <th class="text-right">Income %</th>
                    <th class="text-right">Expense Amount</th>
                    <th class="text-right">Expense %</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Merge income and expense categories for display
                $all_categories = [];
                $income_by_category_result->data_seek(0);
                while ($row = $income_by_category_result->fetch_assoc()) {
                    $all_categories[$row['name']]['income_total'] = $row['total'];
                }
                $expense_by_category_result->data_seek(0);
                while ($row = $expense_by_category_result->fetch_assoc()) {
                    $all_categories[$row['name']]['expense_total'] = $row['total'];
                }

                ksort($all_categories);
                
                foreach ($all_categories as $category_name => $data):
                    $income_amount = isset($data['income_total']) ? $data['income_total'] : 0;
                    $expense_amount = isset($data['expense_total']) ? $data['expense_total'] : 0;
                ?>
                <tr>
                    <td><?php echo $category_name; ?></td>
                    <td class="text-right"><?php echo number_format($income_amount, 2); ?></td>
                    <td class="text-right">
                        <?php echo $total_income > 0 ? number_format(($income_amount / $total_income) * 100, 1) : 0; ?>%
                    </td>
                    <td class="text-right"><?php echo number_format($expense_amount, 2); ?></td>
                    <td class="text-right">
                        <?php echo $total_expense > 0 ? number_format(($expense_amount / $total_expense) * 100, 1) : 0; ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if ($report_type === 'detailed' && !empty($transactions)): ?>
        <h2>Transaction Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['transaction_date']; ?></td>
                    <td><?php echo $transaction['category_name']; ?></td>
                    <td><?php echo $transaction['description']; ?></td>
                    <td class="text-right" style="color: <?php echo $transaction['category_type'] === 'income' ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo number_format($transaction['amount'], 2); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php 
        $html = ob_get_clean(); // Ambil output HTML dan bersihkan buffer

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        // Render HTML to PDF
        $dompdf->render();

        // Output PDF ke browser
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Financial Reports</h1>
        
        <?php if (!empty($transactions)): ?>
        <button id="saveReportBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-save mr-2"></i> Save Report
        </button>
        <?php endif; ?>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Generate Report</h2>
        
        <form action="" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="category_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                    <select id="category_type" name="category_type" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="all" <?php echo $category_type === 'all' ? 'selected' : ''; ?>>All Types</option>
                        <option value="income" <?php echo $category_type === 'income' ? 'selected' : ''; ?>>Income</option>
                        <option value="expense" <?php echo $category_type === 'expense' ? 'selected' : ''; ?>>Expense</option>
                    </select>
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="0">All Categories</option>
                        <optgroup label="Income Categories">
                            <?php 
                            $categories_result->data_seek(0);
                            while ($category = $categories_result->fetch_assoc()): 
                                if ($category['type'] === 'income'):
                            ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                        </optgroup>
                        <optgroup label="Expense Categories">
                            <?php 
                            $categories_result->data_seek(0);
                            while ($category = $categories_result->fetch_assoc()): 
                                if ($category['type'] === 'expense'):
                            ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id === (int)$category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                        </optgroup>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select id="report_type" name="report_type" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="summary" <?php echo $report_type === 'summary' ? 'selected' : ''; ?>>Summary</option>
                        <option value="detailed" <?php echo $report_type === 'detailed' ? 'selected' : ''; ?>>Detailed</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-filter mr-2"></i> Generate Report
                    </button>
                </div>
                
                <?php if (!empty($transactions)): ?>
                <div class="flex items-end space-x-2">
                    <a href="<?php echo SITE_URL; ?>/reports/index.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-file-csv mr-2"></i> CSV
                    </a>
                    <a href="<?php echo SITE_URL; ?>/reports/index.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'xlsx'])); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-file-excel mr-2"></i> XLSX
                    </a>
                    <a href="<?php echo SITE_URL; ?>/reports/index.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'pdf'])); ?>" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <?php if (!empty($transactions)): ?>
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Financial Summary</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-100 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-green-800">Total Income</h3>
                <p class="text-2xl font-bold text-green-600"><?php echo number_format($total_income, 2); ?></p>
            </div>
            
            <div class="bg-red-100 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-red-800">Total Expenses</h3>
                <p class="text-2xl font-bold text-red-600"><?php echo number_format($total_expense, 2); ?></p>
            </div>
            
            <div class="bg-blue-100 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800">Balance</h3>
                <p class="text-2xl font-bold <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo number_format($balance, 2); ?>
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-2">Income by Category</h3>
                <div class="overflow-hidden bg-white rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($income_by_category_result->num_rows > 0): ?>
                                <?php while ($row = $income_by_category_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $row['name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right"><?php echo number_format($row['total'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <?php echo $total_income > 0 ? number_format(($row['total'] / $total_income) * 100, 1) : 0; ?>%
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No income data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div>
                <h3 class="text-md font-medium text-gray-700 mb-2">Expense by Category</h3>
                <div class="overflow-hidden bg-white rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($expense_by_category_result->num_rows > 0): ?>
                                <?php while ($row = $expense_by_category_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $row['name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right"><?php echo number_format($row['total'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <?php echo $total_expense > 0 ? number_format(($row['total'] / $total_expense) * 100, 1) : 0; ?>%
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No expense data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($report_type === 'detailed'): ?>
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Transaction Details</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $transaction['transaction_date']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $transaction['category_type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $transaction['category_name']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo $transaction['description']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium <?php echo $transaction['category_type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo number_format($transaction['amount'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="bg-white shadow-md rounded-lg p-6 mb-6 text-center">
        <p class="text-gray-500">No transactions found for the selected period. Please adjust your filters and try again.</p>
    </div>
    <?php endif; ?>
</div>

<div id="saveReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4">Save Report</h2>
        
        <form id="saveReportForm" method="POST" action="save_report.php" class="space-y-4">
            <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
            <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
            <input type="hidden" name="category_type" value="<?php echo $category_type; ?>">
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
            
            <div>
                <label for="report_title" class="block text-sm font-medium text-gray-700 mb-1">Report Title</label>
                <input type="text" id="report_title" name="report_title" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            
            <div>
                <label for="report_description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea id="report_description" name="report_description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelSaveReport" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Save Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Save Report Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const saveReportBtn = document.getElementById('saveReportBtn');
        const saveReportModal = document.getElementById('saveReportModal');
        const cancelSaveReport = document.getElementById('cancelSaveReport');
        const modalOverlay = document.getElementById('saveReportModal');
        
        if (saveReportBtn) {
            saveReportBtn.addEventListener('click', function() {
                saveReportModal.classList.remove('hidden');
            });
        }
        
        if (cancelSaveReport) {
            cancelSaveReport.addEventListener('click', function() {
                saveReportModal.classList.add('hidden');
            });
        }
        
        // Close modal when clicking outside
        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) {
                    saveReportModal.classList.add('hidden');
                }
            });
        }
    });
</script>

<?php include_once '../includes/footer.php'; ?>