<?php
session_start();
require_once('../config/db.php');

// ✅ Access control
$role = strtolower($_SESSION['role'] ?? '');
if (!in_array($role, ['admin', 'staff'])) {
    echo "<h3 style='color:red; text-align:center;'>Access denied.</h3>";
    exit;
}

// ✅ Form submission handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $categories = $_POST['category'] ?? [];
    $category = implode(', ', $categories); // Store multiple categories as one string
    $description = trim($_POST['description'] ?? '');
    $admin = floatval($_POST['admin'] ?? 0);
    $board = floatval($_POST['board'] ?? 0);
    $infra = floatval($_POST['infra'] ?? 0);
    $finance = floatval($_POST['finance'] ?? 0);
    $member = floatval($_POST['member'] ?? 0);
    $regulatory = floatval($_POST['regulatory'] ?? 0);
    $salary = floatval($_POST['salary'] ?? 0);
    $other = floatval($_POST['other'] ?? 0);
    $dbal = floatval($_POST['dbal'] ?? 0);

    // ✅ Validation
    if (empty($date) || $amount <= 0 || empty($categories)) {
        $error = "Please enter a valid date, amount, and at least one category.";
    } else {
        $stmt = $conn->prepare("INSERT INTO expenses 
            (date, amount, category, description, administrative, board, infrastructure, financial, member, regulatory, salary, other, dbal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssddddddddd", $date, $amount, $category, $description, $admin, $board, $infra, $finance, $member, $regulatory, $salary, $other, $dbal);
        
        if ($stmt->execute()) {
            header("Location: expenses.php?success=1");
            exit;
        } else {
            $error = "Error saving expense: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .form-container {
            max-width: 750px;
            margin: auto;
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        label { font-weight: bold; display: block; margin-top: 15px; }
        input, textarea, select {
            width: 100%; padding: 10px; margin-top: 5px;
            border: 1px solid #ccc; border-radius: 4px;
        }

        select[multiple] {
            height: 160px;
        }

        button {
            margin-top: 20px;
            padding: 10px 18px;
            background: #28a745;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .back-btn {
            background-color: #007bff;
            margin-left: 10px;
        }

        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Add New Expense</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <label>Date</label>
        <input type="date" name="date" required>

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" required>

        <label>Category (hold Ctrl or Cmd to select multiple)</label>

        <select name="category[]" multiple required>
            

            <optgroup label="FINANCIAL EXPENSES">
                <option>F - Bank Charges</option>
                <option>F - Loan Interest (Sacco Have Borrowed)</option>
            </optgroup>
            <optgroup label="STAFF EXPENSES">
                <option>S - Salaries & Wages</option>
                <option>S – Allowances</option>
                <option>S - Transport</option>
            </optgroup>
            <optgroup label="ADMINISTRATIVE EXPENSES">
                <option>A – Audit fee</option>
                <option>A - Expenses</option>
                <option>A - Depreciation & Amortisation</option>
                <option>A - Printing & Stationeries</option>
                <option>A – Telephone</option>
                <option>A - Internet Expenses</option>
                <option>A - Marketing Expenses</option>
                <option>A - Professional Services</option>
                <option>A - Office Expenses</option>
                <option>A - Office Repair & Maintenance</option>
                <option>A - Strategic Planning</option>
                <option>A - Loan Guard Insurance</option>
                <option>A - Insurance On Deposits</option>
                <option>A - Fidelity Insurance</option>
                <option>A - Money & Cash In Transit</option>
                <option>A - Staff Travelling</option>
                <option>A - Education</option>
                <option>A - Christmas</option>
                <option>A – Rent</option>
                <option>A - Cleaning</option>
                <option>A - Service Charge</option>
                <option>A - Annual Subscriptions</option>
                <option>A - Corporate Social Responsibility</option>
                <option>A - Corporate Governance</option>
                <option>A - Ict Upgrade & Maintenance</option>
                <option>A - Back Ups</option>
                <option>A - Software Licence & Maintenance</option>
                <option>A - Internal Audit</option>
                <option>A - Water & Electricity</option>
                <option>A - Increase In Provision For Bad Debt</option>
                <option>A – Tea</option>
                <option>A – Repair & Maitanance</option>
            </optgroup>
            <optgroup label="REGULATORY EXPENSES">
                <option>R - RSRARA Board Authorization</option>
                <option>R - RSRARA Annual Levy</option>
                <option>R - R-Tax Account</option>
            </optgroup>
            <optgroup label="MEMBERS EXPENSES">
                <option>M - Member Training</option>
                <option>M - AGM Expenses</option>
                <option>M - SGM Expenses</option>
                <option>M - Corporate Wear</option>
                <option>M - Cooperate Development</option>
                <option>M - Interest On Deposits</option>
                <option>M - Dividend</option>
            </optgroup>
            <optgroup label="BOARD MEETING EXPENSES">
                <option>B - Sitting Allowances</option>
                <option>B - Subsistence Allowances</option>
                <option>B - Training & Development</option>
            </optgroup>
            <optgroup label="CAPITAL EXPENDITURE">
                <option>C - Infrastructure</option>
                <option>C - Computer & Printer</option>
                <option>C - Depreciation</option>
                <option>C - Office Equipment</option>
            </optgroup>
            <optgroup label="OTHERS">
                <option>Other</option>
            </optgroup>
        </select>

        <label>Description</label>
        <textarea name="description" rows="2" required></textarea>

        <label>Administrative</label>
        <input type="number" step="0.01" name="admin" value="0">

        <label>Board</label>
        <input type="number" step="0.01" name="board" value="0">

        <label>Infrastructure</label>
        <input type="number" step="0.01" name="infra" value="0">

        <label>Financial</label>
        <input type="number" step="0.01" name="finance" value="0">

        <label>Member</label>
        <input type="number" step="0.01" name="member" value="0">

        <label>Regulatory</label>
        <input type="number" step="0.01" name="regulatory" value="0">

        <label>Salary</label>
        <input type="number" step="0.01" name="salary" value="0">

        <label>Other</label>
        <input type="number" step="0.01" name="other" value="0">

        <label>DBal (Closing Balance)</label>
        <input type="number" step="0.01" name="dbal" value="0">

        <button type="submit">Save Expense</button>
        <a href="expenses.php"><button type="button" class="back-btn">⬅ Back</button></a>
    </form>
</div>
</body>
</html>
