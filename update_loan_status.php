<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $allowed = ['Active', 'Poor repayment', 'Defaulted', 'Cleared', 'Others'];
    if (!in_array($status, $allowed)) {
        http_response_code(400);
        echo "Invalid status.";
        exit;
    }

    $query = "UPDATE loans SET loan_status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo "Status updated successfully.";
    } else {
        http_response_code(500);
        echo "Database error: " . mysqli_error($conn);
    }
}
?>
