<?php
require_once("../config/db.php");

$response = [
    'members' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM members"))['total'],
    'loan_amount' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(loan_amount) AS total FROM loans"))['total'] ?? 0,
    'loan_balance' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(loan_balance) AS total FROM loans"))['total'] ?? 0,
    'savings' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) AS total FROM savings"))['total'] ?? 0
];

header('Content-Type: application/json');
echo json_encode($response);
