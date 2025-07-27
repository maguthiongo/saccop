<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    echo "Access denied.";
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users - VOV SACCO</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #eef2f5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #004080; color: white; }
        a.add-btn {
            background: #004080;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Users</h2>
    <a href="add_user.php" class="add-btn" >➕ Add New User</a>
    <a href="../dashboard.php" style="
    display: inline-block;
    background-color: pink;
    color: black;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 10px;
    font-weight: bold;
">
    ⬅️ Back to Dashboard
</a>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" style="color: blue;">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
