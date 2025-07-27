<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fields = ['date', 'amount', 'category', 'description', 'administrative', 'board', 'infrastructure', 'financial', 'member', 'regulatory', 'salary', 'other'];
    $updates = [];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $conn->real_escape_string($_POST[$field]);
            $updates[] = "$field = '$value'";
        }
    }

    $sql = "UPDATE expenses SET " . implode(', ', $updates) . " WHERE id = '$id'";

    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
