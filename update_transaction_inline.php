<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $fields = [
        'amount_deposited', 'loan_principal_paid', 'interest_paid', 'christmas_fund',
        'savings', 'others', 'received_loan', 'loan_balance', 'transaction_date'
    ];

    $updates = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $field === 'transaction_date' ? "'" . $_POST[$field] . "'" : floatval($_POST[$field]);
            $updates[] = "$field = $value";
        }
    }

    if (empty($updates)) {
        echo "No fields to update.";
        exit;
    }

    $sql = "UPDATE transactions SET " . implode(', ', $updates) . " WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "DB error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
