<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

$required_tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    'transactions' => "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        category_id INT NOT NULL,
        type ENUM('income', 'expense') NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        description TEXT,
        transaction_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($required_tables as $table => $create_sql) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        echo "Creating table: $table<br>";
        if ($conn->query($create_sql)) {
            echo "Table $table created successfully<br>";
        } else {
            echo "Error creating table $table: " . $conn->error . "<br>";
        }
    } else {
        echo "Table $table already exists<br>";
    }
}

// Add type column to transactions if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM transactions LIKE 'type'");
if ($result->num_rows == 0) {
    $alter_sql = "ALTER TABLE transactions ADD COLUMN type ENUM('income', 'expense') NOT NULL AFTER category_id";
    if ($conn->query($alter_sql)) {
        echo "Added type column to transactions table<br>";
    } else {
        echo "Error adding type column: " . $conn->error . "<br>";
    }
}

echo "Database check complete.";
