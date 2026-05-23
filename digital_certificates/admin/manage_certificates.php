<?php
include "../db.php";

$result = mysqli_query($conn, "
SELECT 
    c.Certificate_ID,
    c.Certificate_Type,
    c.Issue_Date,
    c.Status,
    s.Student_Name,
    s.Register_Number
FROM CERTIFICATE c
JOIN STUDENT s
ON c.Student_ID = s.Student_ID
ORDER BY c.Certificate_ID DESC
");
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            padding: 40px;
        }

        table {
            margin: auto;
            border-collapse: collapse;
            width: 90%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background: #f2f2f2;
        }

        .valid {
            color: green;
            font-weight: bold;
        }

        .revoked {
            color: red;
            font-weight: bold;
        }

        button {
            padding: 6px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .revoke-btn {
            background: red;
            color: white;
        }
    </style>
</head>

<body>

    <h2>Manage Certificates</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Register No</th>
            <th>Type</th>
            <th>Issue Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <tr>
                <td><?php echo $row['Certificate_ID']; ?></td>
                <td><?php echo $row['Student_Name']; ?></td>
                <td><?php echo $row['Register_Number']; ?></td>
                <td><?php echo $row['Certificate_Type']; ?></td>
                <td><?php echo $row['Issue_Date']; ?></td>

                <td class="<?php echo strtolower($row['Status']); ?>">
                    <?php echo $row['Status']; ?>
                </td>

                <td>
                    <?php if ($row['Status'] == 'Valid') { ?>
                        <a href="revoke_certificate.php?id=<?php echo $row['Certificate_ID']; ?>">
                            <button class="revoke-btn">Revoke</button>
                        </a>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>

            </tr>

        <?php } ?>

    </table>

    <br><br>
    <a href="generate_certificate.php">Back to Generate</a>

</body>

</html>