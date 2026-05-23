<?php
include "../db.php";

$result = mysqli_query($conn, "
SELECT 
    v.Verification_ID,
    v.Certificate_ID,
    v.Scan_Date,
    v.Verification_Result,
    s.Student_Name,
    s.Register_Number
FROM VERIFICATION v
JOIN CERTIFICATE c ON v.Certificate_ID = c.Certificate_ID
JOIN STUDENT s ON c.Student_ID = s.Student_ID
ORDER BY v.Scan_Date DESC
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verification Logs</title>
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

        table {
            background-color: white;
            border-collapse: collapse;
            margin: auto;
            width: 80%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #2ecc71;
            color: white;
        }
    </style>
</head>

<body>

    <h2>Verification Logs</h2>

    <table>
        <tr>
            <th>Certificate ID</th>
            <th>Student Name</th>
            <th>Register Number</th>
            <th>Scan Date</th>
            <th>Result</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <tr>
                <td><?php echo $row['Certificate_ID']; ?></td>
                <td><?php echo $row['Student_Name']; ?></td>
                <td><?php echo $row['Register_Number']; ?></td>
                <td><?php echo $row['Scan_Date']; ?></td>
                <td><?php echo $row['Verification_Result']; ?></td>
            </tr>

        <?php } ?>

    </table>

</body>

</html>