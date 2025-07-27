<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    echo "Access denied.";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membership_no = mysqli_real_escape_string($conn, $_POST['membership_no']);
    $loan_no = mysqli_real_escape_string($conn, $_POST['loan_no']);
    $loan_amount = floatval($_POST['loan_amount']);
    $issued_date = mysqli_real_escape_string($conn, $_POST['issued_date']);
    $monthly_installment = floatval($_POST['monthly_installment']);
    $monthly_interest = floatval($_POST['monthly_interest']);
    $loan_balance = floatval($_POST['loan_balance']);
    $loan_status = mysqli_real_escape_string($conn, $_POST['loan_status']);
    $no_of_installments = intval($_POST['no_of_installments']);

    // Uniqueness check
    $check = mysqli_query($conn, "SELECT id FROM loans WHERE loan_no = '$loan_no'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<p style='color: red;'>Error: Loan No <strong>$loan_no</strong> already exists.</p>";
    } else {
        $sql = "INSERT INTO loans (membership_no, loan_no, loan_amount, issued_date, monthly_installment, monthly_interest, loan_balance, loan_status, no_of_installments)
                VALUES ('$membership_no', '$loan_no', $loan_amount, '$issued_date', $monthly_installment, $monthly_interest, $loan_balance, '$loan_status', $no_of_installments)";
        if (mysqli_query($conn, $sql)) {
            header("Location: loans.php?success=1");
            exit;
        } else {
            $message = "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Loan</title></head>
<body>
    <h2>Add New Loan</h2>
    <form method="POST">
        <label>Membership No</label><input type="text" name="membership_no" required><br>
        <label>Loan No</label><input type="text" name="loan_no" required><br>
        <label>Loan Amount</label><input type="number" step="0.01" name="loan_amount" required><br>
        <label>Issued Date</label><input type="date" name="issued_date" required><br>
        <label>Monthly Installment</label><input type="number" step="0.01" name="monthly_installment" required><br>
        <label>Monthly Interest</label><input type="number" step="0.01" name="monthly_interest" required><br>
        <label>Loan Balance</label><input type="number" step="0.01" name="loan_balance" required><br>
        <label>Installment(s)</label><input type="number" name="no_of_installments" required><br>
        <label>Loan Status</label>
        <select name="loan_status" required>
            <option>Active</option>
            <option>Poor repayment</option>
            <option>Defaulted</option>
            <option>Cleared</option>
            <option>Others</option>
        </select><br><br>
        <button type="submit">Save Loan</button>
    </form>
    <?= $message ?>
</body>
</html>
