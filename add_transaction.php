<?php
require_once('../config/db.php');
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    die("Access denied.");
}

$membership_no = $_GET['membership_no'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membership_no = $_POST['membership_no'];
    $transaction_date = $_POST['transaction_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $amount_deposited = floatval($_POST['amount_deposited']);
    $savings = floatval($_POST['savings']);
    $christmas_fund = floatval($_POST['christmas_fund']);
    $loan_amount = floatval($_POST['loan_amount']);
    $loan_principal = floatval($_POST['loan_principal']);
    $interest_paid = floatval($_POST['interest_paid']);
    $loan_balance = floatval($_POST['loan_balance']);
    $others = floatval($_POST['others'] ?? 0);

    // Validation
    if (!isset($transaction_date) || trim($transaction_date) === '') {
        $error = "Transaction date is required.";
    } else {
        $sum_parts = $savings + $christmas_fund + $loan_principal + $interest_paid + $others;


        // Only check matching totals if deposit is greater than 0
        
if ($amount_deposited > 0 && abs($amount_deposited - $sum_parts) > 0.01) {
    $error = "If amount is deposited, it must equal the sum of: Savings + Christmas Fund + Principal Paid + Interest + Others.";
}


        else {
            $stmt = $conn->prepare("INSERT INTO transactions 
                (membership_no, transaction_date, description, amount_deposited, savings, christmas_fund, loan_amount, loan_principal, interest_paid, loan_balance, others)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdddddddd",
                $membership_no, $transaction_date, $description,
                $amount_deposited, $savings, $christmas_fund, $loan_amount,
                $loan_principal, $interest_paid, $loan_balance, $others
            );

           if ($stmt->execute()) {
    header("Location: member_statement.php?membership_no=" . urlencode($membership_no) . "&msg=Transaction+added+successfully");
    exit;
}



            else {
                $error = "Failed to save transaction: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Transaction</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        label { display: block; margin-top: 10px; }
        input[type=text], input[type=number], input[type=date] {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        .btn { margin-top: 15px; padding: 8px 16px; background-color: #28a745; color: white; border: none; cursor: pointer; }
        .btn:hover { background-color: #218838; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Add Transaction</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="membership_no" value="<?= htmlspecialchars($membership_no) ?>">

        <label for="transaction_date">Transaction Date:</label>
        <input type="date" name="transaction_date" required>

        <label for="description">Description:</label>
        <input type="text" name="description" placeholder="e.g. Monthly contribution">

        <label for="amount_deposited">Amount Deposited:</label>
        <input type="number" step="0.01" name="amount_deposited" >

        <label for="savings">Savings:</label>
        <input type="number" step="0.01" name="savings" required>

        <label for="christmas_fund">Christmas Fund:</label>
        <input type="number" step="0.01" name="christmas_fund" required>

        <label for="loan_amount">Issued Loan:</label>
        <input type="number" step="0.01" name="loan_amount" value="0">

        <label for="loan_principal">Loan Principal Paid:</label>
        <input type="number" step="0.01" name="loan_principal" required>

        <label for="interest_paid">Interest Paid:</label>
        <input type="number" step="0.01" name="interest_paid" required>

        <label for="loan_balance">Loan Balance:</label>
        <input type="number" step="0.01" name="loan_balance" required>

        <label for="others">Others (if any):</label>
        <input type="number" step="0.01" name="others" value="0">

        <button class="btn" type="submit">Save Transaction</button>
    </form>
</body>
</html>
