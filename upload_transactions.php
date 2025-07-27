<?php
require_once('../config/db.php');
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membership_no = $_POST['membership_no'] ?? '';

    if (!$membership_no || !isset($_FILES['csv_file'])) {
        die("Membership No or file missing.");
    }

    $file = $_FILES['csv_file']['tmp_name'];

    if (!in_array(mime_content_type($file), ['text/plain', 'text/csv', 'application/vnd.ms-excel'])) {
        die("Only CSV files are allowed.");
    }

    $handle = fopen($file, "r");
    fgetcsv($handle); // skip header

    $inserted = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $date_raw = trim($data[1]);
        $transaction_date = date('Y-m-d', strtotime($date_raw));
        if (!$transaction_date || $transaction_date === '1970-01-01') {
            $transaction_date = date('Y-m-d');
        }

        $description = $data[2];
        $amount_deposited = floatval($data[3]);
        $savings = floatval($data[4]);
        $christmas_fund = floatval($data[5]);
        $loan_amount = floatval($data[6]);
        $loan_principal = floatval($data[7]);
        $interest_paid = floatval($data[8]);
        $loan_balance = floatval($data[9]);
        $others = floatval($data[10] ?? 0);

        $stmt = $conn->prepare("INSERT INTO transactions (
            membership_no, transaction_date, description, amount_deposited,
            savings, christmas_fund, loan_amount, loan_principal,
            interest_paid, loan_balance, others
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssdddddddd", $membership_no, $transaction_date, $description, $amount_deposited,
            $savings, $christmas_fund, $loan_amount, $loan_principal, $interest_paid, $loan_balance, $others);

        if ($stmt->execute()) {
            $inserted++;
        }
    }

    fclose($handle);
    header("Location: member_statement.php?membership_no=$membership_no&msg=Uploaded+$inserted+transactions");
    exit;
}
?>
