<?php
// Include configuration file
require_once '../includes/config.php';

// Check if user is logged in, if not redirect to login page
if (!is_logged_in()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user data
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $report_title = sanitize_input($_POST['report_title']);
    $report_description = isset($_POST['report_description']) ? sanitize_input($_POST['report_description']) : '';
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);
    $category_type = sanitize_input($_POST['category_type']);
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    
    // Validate input
    if (empty($report_title) || empty($start_date) || empty($end_date)) {
        $_SESSION['message'] = 'Please fill in all required fields.';
        $_SESSION['message_type'] = 'error';
        redirect(SITE_URL . '/reports/index.php');
    }
    
    // Insert report into database
    $insert_query = "INSERT INTO reports (user_id, title, description, start_date, end_date, created_at) 
                    VALUES ({$user_id}, '{$report_title}', '{$report_description}', '{$start_date}', '{$end_date}', NOW())";
    
    if ($conn->query($insert_query) === TRUE) {
        $_SESSION['message'] = 'Report saved successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error saving report: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
    }
    
    // Redirect back to reports page with the same filters
    $redirect_params = http_build_query([
        'start_date' => $start_date,
        'end_date' => $end_date,
        'category_type' => $category_type,
        'category_id' => $category_id
    ]);
    
    redirect(SITE_URL . '/reports/index.php?' . $redirect_params);
} else {
    // If not POST request, redirect to reports page
    redirect(SITE_URL . '/reports/index.php');
}