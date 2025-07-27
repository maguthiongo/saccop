<?php
require_once("../config/db.php");

$result = mysqli_query($conn, "SELECT COUNT(*) AS overdue FROM loans WHERE status IN ('Defaulted', 'Poor repayment')");
$data = mysqli_fetch_assoc($result);
$count = intval($data['overdue']);

if ($count > 0) {
    echo "<div class='alert'>⚠️ $count overdue loans. Please follow up.</div>";
} else {
    echo "<div style='color: #0a0; font-weight: bold;'>✅ No overdue loans</div>";
}
