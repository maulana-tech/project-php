<?php
// Include configuration file
require_once '../includes/config.php';

// Check if user is logged in, if not redirect to login page
if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Set page title
$page_title = 'Saved Reports';

// Handle report deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $report_id = $_GET['delete'];
    
    // Check if report belongs to user
    $check_query = "SELECT id FROM reports WHERE id = {$report_id} AND user_id = {$user_id}";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        // Delete report
        $delete_query = "DELETE FROM reports WHERE id = {$report_id}";
        if ($conn->query($delete_query) === TRUE) {
            $_SESSION['message'] = 'Report deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error deleting report: ' . $conn->error;
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'You do not have permission to delete this report.';
        $_SESSION['message_type'] = 'error';
    }
    
    // Redirect to remove the delete parameter from URL
    redirect(SITE_URL . '/reports/saved_reports.php');
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM reports WHERE user_id = {$user_id}";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get saved reports with pagination
$reports_query = "SELECT * FROM reports 
                WHERE user_id = {$user_id} 
                ORDER BY created_at DESC 
                LIMIT {$offset}, {$records_per_page}";
$reports_result = $conn->query($reports_query);

// Include header
include_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Saved Reports</h1>
        <a href="<?php echo SITE_URL; ?>/reports/index.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-chart-bar mr-2"></i> Generate New Report
        </a>
    </div>
    
    <?php if ($reports_result->num_rows > 0): ?>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($report = $reports_result->fetch_assoc()): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $report['title']; ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo $report['description'] ? $report['description'] : 'No description'; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('M d, Y', strtotime($report['start_date'])); ?> - 
                        <?php echo date('M d, Y', strtotime($report['end_date'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('M d, Y', strtotime($report['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo SITE_URL; ?>/reports/index.php?start_date=<?php echo $report['start_date']; ?>&end_date=<?php echo $report['end_date']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="<?php echo SITE_URL; ?>/reports/saved_reports.php?delete=<?php echo $report['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this report?');">
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
    <div class="flex justify-center mt-6">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Previous</span>
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Next</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="bg-white shadow-md rounded-lg p-6 text-center">
        <p class="text-gray-500 mb-4">You don't have any saved reports yet.</p>
        <a href="<?php echo SITE_URL; ?>/reports/index.php" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-chart-bar mr-2"></i> Generate a Report
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>