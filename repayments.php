<?php
session_start();
require_once('../config/db.php');

// Access control
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    echo "Access denied.";
    exit;
}

// Fetch repayment records
$sql = "
    SELECT 
        r.id, 
        r.loan_id, 
        r.payment_date, 
        r.amount_paid, 
        l.loan_no, 
        l.membership_no, 
        m.full_name 
    FROM repayments r
    LEFT JOIN loans l ON r.loan_id = l.id
    LEFT JOIN members m ON l.membership_no = m.membership_no
    ORDER BY r.payment_date DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Repayments - VOV SACCO</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f4f4f4; }
        h2 { text-align: center; color: #333; }
        table { border-collapse: collapse; width: 100%; background: white; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #eee; }
        .actions a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
        .controls { margin-bottom: 20px; text-align: center; }
        .controls button {
            margin: 5px;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }
        .controls button:hover { background: #0056b3; }
    </style>
    <script>
        function printPage() {
            window.print();
        }

        function downloadCSV() {
            let csv = 'Loan No,Membership No,Member Name,Amount Paid,Payment Date\n';
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                let data = [];
                for (let i = 1; i <= 5; i++) {
                    data.push('"' + cols[i].innerText + '"');
                }
                csv += data.join(',') + "\n";
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'repayments.csv';
            link.click();
        }
    </script>
</head>
<body>

<h2>Loan Repayments - VOV SACCO</h2>

<div class="controls">
    <button onclick="printPage()">🖨️ Print</button>
    <button onclick="downloadCSV()">⬇️ Download CSV</button>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Loan No</th>
            <th>Membership No</th>
            <th>Member Name</th>
            <th>Amount Paid</th>
            <th>Payment Date</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $count = 1;
    $total_paid = 0;
    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)):
            $total_paid += $row['amount_paid'];
    ?>
        <tr>
            <td><?= $count++ ?></td>
            <td><?= htmlspecialchars($row['loan_no']) ?></td>
            <td><?= htmlspecialchars($row['membership_no']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= number_format($row['amount_paid'], 2) ?></td>
            <td><?= htmlspecialchars($row['payment_date']) ?></td>
        </tr>
    <?php endwhile; else: ?>
        <tr><td colspan="6">No repayments found.</td></tr>
    <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <td colspan="4">TOTAL</td>
            <td><?= number_format($total_paid, 2) ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
