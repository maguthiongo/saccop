<?php
require_once('../config/db.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membership_No = $_POST['membership_No'];
    $full_name     = $_POST['full_name'];
    $email         = $_POST['email'];
    $phone         = $_POST['phone'];
    $id_number     = $_POST['id_number'];
    $join_date     = $_POST['join_date'];
    $reg_fee       = $_POST['reg_fee'];
    $share_capital = $_POST['share_capital'];

    // Ensure numeric values
    $reg_fee = is_numeric($reg_fee) ? $reg_fee : 0.00;
    $share_capital = is_numeric($share_capital) ? $share_capital : 0.00;

    $sql = "INSERT INTO members (membership_no, full_name, email, phone, id_number, join_date, reg_fee, share_capital) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssdd", $membership_No, $full_name, $email, $phone, $id_number, $join_date, $reg_fee, $share_capital);

    if (mysqli_stmt_execute($stmt)) {
        $msg = "✅ Member added successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff0f5;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #d63384;
        }
        form {
            max-width: 500px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        input, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background-color: #d63384;
            color: white;
            border: none;
            cursor: pointer;
        }
        .msg {
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #d63384;
        }
    </style>
</head>
<body>

<h2>Add New Member</h2>

<?php if ($msg): ?>
    <p class="msg"><?= $msg ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="membership_No" placeholder="Membership Number" required>
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email (optional)">
    <input type="text" name="phone" placeholder="Phone (optional)">
    <input type="text" name="id_number" placeholder="ID Number" required>
    <input type="date" name="join_date" required value="<?= date('Y-m-d') ?>">
    <input type="number" step="0.01" name="reg_fee" placeholder="Registration Fee (e.g. 500)" required>
    <input type="number" step="0.01" name="share_capital" placeholder="Share Capital (e.g. 1000)" required>
    <button type="submit">Add Member</button>
</form>

<a href="members.php" class="back-link">← Back to Member List</a>

</body>
</html>
