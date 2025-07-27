<?php
session_start();
require_once('../config/db.php');

$role = $_SESSION['role'] ?? '';
if (strtolower($role) !== 'admin') {
    echo "<h3 style='color: red; text-align: center;'>Access denied. Admin only.</h3>";
    exit;
}

// Fetch summary data grouped by member



$sql = "
    SELECT 
        m.membership_no,
        m.full_name,
        COALESCE(SUM(t.amount_deposited), 0) AS total_deposit,
        COALESCE(SUM(t.savings), 0) AS total_savings,
        COALESCE(SUM(t.christmas_fund), 0) AS total_christmas,
        COALESCE(SUM(t.loan_amount), 0) AS total_loan,
        COALESCE(SUM(t.loan_principal), 0) AS total_principal,
        COALESCE(SUM(t.interest_paid), 0) AS total_interest,
        COALESCE(lb.latest_loan_balance, 0) AS total_loan_balance
    FROM members m
    LEFT JOIN transactions t ON m.membership_no = t.membership_no
    LEFT JOIN (
        SELECT t1.membership_no, t1.loan_balance AS latest_loan_balance
        FROM transactions t1
        INNER JOIN (
            SELECT membership_no, MAX(transaction_date) AS latest_date
            FROM transactions
            GROUP BY membership_no
        ) t2 ON t1.membership_no = t2.membership_no AND t1.transaction_date = t2.latest_date
        GROUP BY t1.membership_no
    ) lb ON m.membership_no = lb.membership_no
    GROUP BY m.membership_no, m.full_name
    ORDER BY m.membership_no ASC
";
       

$result = $conn->query($sql);

$totals = [
    'deposit' => 0,
    'savings' => 0,
    'christmas' => 0,
    'loan' => 0,
    'principal' => 0,
    'interest' => 0,
    'balance' => 0,
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>SAVINGS AND OTHER SUMMARY</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: skyblue; }
        h2 { text-align: center; color: darkgreen; }
        .controls { margin-bottom: 15px; text-align: right; }
        .btn { padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px; }
        .btn:hover { background: #218838; }
        .btn-print { background: #007bff; }
        .btn-print:hover { background: #0056b3; }
        .btn-dashboard { background: red; }
        .btn-dashboard:hover { background: darkred; }
        .scrollable-table { max-height: 500px; overflow-y: auto; border: 1px solid #ccc; background-color: white; }
        table { border-collapse: collapse; width: 100%; min-width: 1000px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        thead th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        tfoot td {
            background-color: #e6ffe6;
            font-weight: bold;
            position: sticky;
            bottom: 0;
            z-index: 1;
        }
        th:first-child, td:first-child,
        th:nth-child(2), td:nth-child(2) {
            position: sticky;
            left: 0;
            background-color: #f2f2f2;
            z-index: 3;
        }
        td:nth-child(2), th:nth-child(2) {
            left: 130px;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
        function downloadCSV() {
            window.location.href = 'export_savings_summary.php?format=csv';
        }
        function goToDashboard() {
            window.location.href = '../dashboard.php';
        }
    </script>
</head>
<body>
    <h2>SAVINGS AND OTHER SUMMARY</h2>

    <div class="controls">
        <button class="btn" onclick="downloadCSV()">Download CSV</button>
        <?php if (strtolower($role) === 'admin'): ?>
            <button class="btn btn-print" onclick="printPage()">Print</button>
        <?php endif; ?>
        <button class="btn btn-dashboard" onclick="goToDashboard()">← Back to Dashboard</button>
    </div>

    <div class="scrollable-table">
        <table>
            <thead>
                <tr>
                    <th>Membership No</th>
                    <th>Full Name</th>
                    <th>Total Deposits</th>
                    <th>Total Savings</th>
                    <th>Christmas Fund</th>
                    <th>Loan Issued</th>
                    <th>Principal Paid</th>
                    <th>Interest Paid</th>
                    <th>Loan Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()):
                $totals['deposit'] += $row['total_deposit'];
                $totals['savings'] += $row['total_savings'];
                $totals['christmas'] += $row['total_christmas'];
                $totals['loan'] += $row['total_loan'];
                $totals['principal'] += $row['total_principal'];
                $totals['interest'] += $row['total_interest'];
                $totals['balance'] += $row['total_loan_balance'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['membership_no']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= number_format($row['total_deposit'], 2) ?></td>
                    <td><?= number_format($row['total_savings'], 2) ?></td>
                    <td><?= number_format($row['total_christmas'], 2) ?></td>
                    <td><?= number_format($row['total_loan'], 2) ?></td>
                    <td><?= number_format($row['total_principal'], 2) ?></td>
                    <td><?= number_format($row['total_interest'], 2) ?></td>
                    <td><?= number_format($row['total_loan_balance'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTALS</td>
                    <td><?= number_format($totals['deposit'], 2) ?></td>
                    <td><?= number_format($totals['savings'], 2) ?></td>
                    <td><?= number_format($totals['christmas'], 2) ?></td>
                    <td><?= number_format($totals['loan'], 2) ?></td>
                    <td><?= number_format($totals['principal'], 2) ?></td>
                    <td><?= number_format($totals['interest'], 2) ?></td>
                    <td><?= number_format($totals['balance'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
