<?php
include "../db.php";

$total_cert = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM CERTIFICATE"
));

$valid_cert = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM CERTIFICATE WHERE Status='Valid'"
));

$revoked_cert = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM CERTIFICATE WHERE Status='Revoked'"
));

$total_verify = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM VERIFICATION"
));
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            background-image: url("1.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial;
            text-align: center;
            padding-top: 40px;
        }

        .box {
            display: inline-block;
            background: white;
            padding: 25px;
            margin: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px gray;
            width: 200px;
        }

        h2 {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <h2>Admin Dashboard</h2>

    <div class="box">
        <h3>Total Certificates</h3>
        <p><?php echo $total_cert['total']; ?></p>
    </div>

    <div class="box">
        <h3>Valid Certificates</h3>
        <p><?php echo $valid_cert['total']; ?></p>
    </div>

    <div class="box">
        <h3>Revoked Certificates</h3>
        <p><?php echo $revoked_cert['total']; ?></p>
    </div>

    <div class="box">
        <h3>Total Verifications</h3>
        <p><?php echo $total_verify['total']; ?></p>
    </div>

</body>

</html>