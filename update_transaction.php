<?php
session_start();
require_once('../config/db.php');

// ✅ Only allow Admin or Staff roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

// ✅ Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo "Invalid transaction ID.";
        exit;
    }

    // Define allowed fields for update
    $fields = [
        'transaction_date',
        'description',
        'amount_deposited',
        'loan_principal_paid',
        'interest_paid',
        'christmas_fund',
        'savings',
        'others',
        'received_loan',
        'loan_balance'
    ];

    $updates = [];

    // Loop through allowed fields and prepare sanitized updates
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if (in_array($field, ['transaction_date', 'description'])) {
                $value = "'" . mysqli_real_escape_string($conn, $_POST[$field]) . "'";
            } else {
                $value = floatval($_POST[$field]);
            }
            $updates[] = "$field = $value";
        }
    }

    if (empty($updates)) {
        echo "No data to update.";
        exit;
    }

    // Build SQL update statement
    $sql = "UPDATE transactions SET " . implode(', ', $updates) . " WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "DB error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}
?>
