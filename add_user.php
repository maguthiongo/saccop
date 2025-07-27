<?php
session_start();
require_once('../config/db.php');

// Role access control
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    echo "<h3 style='color: red;'>Access denied. Admin or Staff only.</h3>";
    exit;
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validation
    if (empty($username) || empty($password) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    // Password strength validation
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must be at least 8 characters long and include both letters and numbers.";
    }

    // Check for existing username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username already exists.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $errors[] = "Failed to add user. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User - VOV SACCO</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 20px; }
        .form-box { background: #fff; padding: 20px; border-radius: 8px; width: 400px; margin: auto; box-shadow: 0 0 10px #ccc; }
        input, select { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #004080; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #003060; }
        .error { color: red; margin-bottom: 10px; }
        .success { color: green; margin-bottom: 10px; }
        a.back { display: inline-block; margin-top: 10px; color: #004080; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Add New User</h2>

        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="">--Select Role--</option>
                <option value="Admin">Admin</option>
                <option value="Staff">Staff</option>
                <option value="Member">Member</option>
            </select>

            <button type="submit">Add User</button>
        </form>

        <a href="users.php" class="back">⬅️ Back to Users</a>
    </div>
</body>
</html>
