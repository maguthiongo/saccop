<?php
require_once("config/db.php");

$inserted = 0;
$skipped = 0;

$members = $conn->query("SELECT membership_no, full_name, phone FROM members");

while ($m = $members->fetch_assoc()) {
    $username = $m['phone'];
    $password = password_hash($username, PASSWORD_DEFAULT); // use phone as password
    $full_name = $m['full_name'];
    $membership_no = $m['membership_no'];

    // Check if this phone number already exists in users
    $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $skipped++;
        continue; // skip existing users
    }

    $sql = "INSERT INTO users (username, full_name, password, role, membership_no)
            VALUES ('$username', '$full_name', '$password', 'Member', '$membership_no')";
    if ($conn->query($sql)) {
        $inserted++;
    } else {
        echo "❌ Error inserting $username: " . $conn->error . "<br>";
    }
}

echo "✅ Done: $inserted members added. $skipped already existed.";
?>
