<?php
include "db.php";

$showResult = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_name = strtoupper($_POST['student_name']);
    $register_number = $_POST['register_number'];

    $student_query = mysqli_query($conn, "
    SELECT Student_ID, Student_Name
    FROM STUDENT
    WHERE UPPER(Student_Name) = '$student_name'
    AND Register_Number = '$register_number'
    ");

    if (mysqli_num_rows($student_query) > 0) {

        $student_data = mysqli_fetch_assoc($student_query);
        $student_id = $student_data['Student_ID'];

        $cert_query = mysqli_query($conn, "
        SELECT *
        FROM CERTIFICATE
        WHERE Student_ID = '$student_id'
        ORDER BY Certificate_ID DESC
        ");

        $showResult = true;
    } else {
        $error = "Invalid Details";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <style>
        body {
            background-image: url("admin/1.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial;
            text-align: center;
            padding-top: 40px;
        }

        .container {
            display: inline-block;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px gray;
            width: 450px;
            background: white;
        }

        input {
            width: 90%;
            padding: 8px;
            margin: 5px;
        }

        button {
            padding: 8px 15px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .valid {
            color: green;
        }

        .revoked {
            color: red;
        }

        .tampered {
            color: orange;
        }

        .cert-box {
            margin-top: 15px;
            padding: 10px;
            border-top: 1px solid #ccc;
            word-wrap: break-word;
        }
    </style>

</head>

<body>

    <div class="container">

        <h2>Student Certificate Verification</h2>

        <form method="POST">

            Student Name:<br>
            <input type="text" name="student_name" required><br>

            Register Number:<br>
            <input type="text" name="register_number" required><br><br>

            <button type="submit">Verify</button>

        </form>

        <?php if (isset($error)) { ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php } ?>

        <?php if ($showResult) { ?>

            <hr>
            <h3>Certificate Details</h3>

            <?php

            $valid_count = 0;
            $revoked_count = 0;
            $tampered_count = 0;
            $total = 0;

            while ($data = mysqli_fetch_assoc($cert_query)) {

                $pdf_path = $data['Certificate_File_Path'];

                if (file_exists($pdf_path)) {

                    $current_hash = hash_file("sha256", $pdf_path);

                    if ($current_hash != $data['File_Hash']) {
                        $data['Status'] = "Tampered";
                    }
                }

                $total++;

                if ($data['Status'] == "Valid") {
                    $valid_count++;
                } else if ($data['Status'] == "Revoked") {
                    $revoked_count++;
                } else if ($data['Status'] == "Tampered") {
                    $tampered_count++;
                }

            ?>

                <div class="cert-box">

                    <b>Certificate ID:</b> <?php echo $data['Certificate_ID']; ?><br>

                    <div style="word-wrap:break-word;">
                        <b>Certificate Type:</b> <?php echo $data['Certificate_Type']; ?>
                    </div>

                    <b>Issue Date:</b> <?php echo $data['Issue_Date']; ?><br>

                    <b>Status:</b>
                    <span class="<?php echo strtolower($data['Status']); ?>">
                        <?php echo $data['Status']; ?>
                    </span>
                    <br><br>
                    <?php if ($data['Status'] == 'Valid') { ?>
                        <a href="<?php echo $data['Certificate_File_Path']; ?>" target="_blank">
                            Download Certificate
                        </a>

                    <?php } elseif ($data['Status'] == 'Revoked') { ?>
                        <p class="revoked"><b>Download Disabled (Certificate Revoked)</b></p>
                    <?php } elseif ($data['Status'] == 'Tampered') { ?>
                        <p class="tampered"><b>Certificate Tampered - Download Disabled</b></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <hr>
            <h3>Certificate Summary</h3>

            <p><b>Total Certificates:</b> <?php echo $total; ?></p>
            <p class="valid"><b>Valid Certificates:</b> <?php echo $valid_count; ?></p>
            <p class="revoked"><b>Revoked Certificates:</b> <?php echo $revoked_count; ?></p>
            <p class="tampered"><b>Tampered Certificates:</b> <?php echo $tampered_count; ?></p>

        <?php } ?>
    </div>
</body>

</html>