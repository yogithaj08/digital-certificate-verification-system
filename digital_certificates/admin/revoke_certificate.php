<?php
include "../db.php";

if (!isset($_GET['id'])) {
    echo "Invalid Request";
    exit;
}

$certificate_id = $_GET['id'];

mysqli_query($conn, "
UPDATE CERTIFICATE
SET Status = 'Revoked'
WHERE Certificate_ID = '$certificate_id'
");

echo "<h3>Certificate Revoked Successfully</h3>";
echo "<a href='generate_certificate.php'>Back</a>";
?>