<?php
session_start();
require_once('../config/db.php');

// Access control
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    echo "Access denied.";
    exit;
}

// Function to generate monthly schedule
function generateSchedule($loan) {
    $schedule = [];
    $balance = $loan['loan_amount'];
    $monthly_installment = $loan['monthly_installment'];
    $monthly_interest = $loan['monthly_interest'];
    $issued_date = new DateTime($loan['issued_date']);

    $count = 1;
    while ($balance > 0 && $count <= 12) {
        $principal = $monthly_installment - $monthly_interest;
        if ($balance < $principal) {
            $principal = $balance;
            $monthly_installment = $principal + $monthly_interest;
        }

        $schedule[] = [
            'installment_no' => $count,
            'payment_date' => $issued_date->format('Y-m-d'),
            'amount_paid' => round($monthly_installment, 2),
            'interest' => round($monthly_interest, 2),
            'balance' => round($balance - $principal, 2)
        ];

        $balance -= $principal;
        $issued_date->modify('+1 month');
        $count++;
    }
    return $schedule;
}

// Fetch two loans only (sample for V001 and V002)
$sql = "SELECT l.*, m.full_name FROM loans l 
        LEFT JOIN members m ON l.membership_no = m.membership_no 
        WHERE l.membership_no IN ('V001', 'V002') 
        ORDER BY l.issued_date ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Repayment Schedule - VOV SACCO</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f5f5f5; }
        h2 { text-align: center; }
        .controls { margin-bottom: 15px; text-align: center; }
        .controls button {
            margin: 5px;
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .controls button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; background: white; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #eee; }
    </style>
    <script>
        function printPage() { window.print(); }

        function downloadCSV() {
            let csv = "Loan No,Membership No,Member Name,Installment,Date,Amount Paid,Interest,Balance\n";
            document.querySelectorAll('table').forEach(table => {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let cols = row.querySelectorAll('td');
                    let rowData = [];
                    cols.forEach(td => rowData.push('"' + td.innerText + '"'));
                    csv += rowData.join(',') + "\n";
                });
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'repayment_schedule.csv';
            link.click();
        }
    </script>
</head>
<body>

<h2>Repayment Schedule - VOV SACCO</h2>
<div class="controls">
    <button onclick="printPage()">🖨️ Print</button>
    <button onclick="downloadCSV()">⬇️ Download CSV</button>
</div>

<?php
while ($loan = mysqli_fetch_assoc($result)):
    $schedule = generateSchedule($loan);
?>
    <h3>Loan: <?= htmlspecialchars($loan['loan_no']) ?> | Member: <?= htmlspecialchars($loan['full_name']) ?> (<?= $loan['membership_no'] ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>Installment #</th>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Interest</th>
                <th>Balance Remaining</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($schedule as $s): ?>
            <tr>
                <td><?= $s['installment_no'] ?></td>
                <td><?= $s['payment_date'] ?></td>
                <td><?= number_format($s['amount_paid'], 2) ?></td>
                <td><?= number_format($s['interest'], 2) ?></td>
                <td><?= number_format($s['balance'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endwhile; ?>

</body>
</html>
