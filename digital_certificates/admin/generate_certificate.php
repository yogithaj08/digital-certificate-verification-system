<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../db.php";
require "../fpdf/fpdf.php";
require "../fpdi/src/autoload.php";

use setasign\Fpdi\Fpdi;

$message = "";
$showPopup = false;
$download_link = "";

if (isset($_POST['revoke'])) {

    $register_number = $_POST['register_number'];

    $student = mysqli_query($conn, "
SELECT Student_ID FROM STUDENT 
WHERE Register_Number='$register_number'
");

    if (mysqli_num_rows($student) > 0) {

        $row = mysqli_fetch_assoc($student);
        $student_id = $row['Student_ID'];

        mysqli_query($conn, "
UPDATE CERTIFICATE
SET Status='Revoked'
WHERE Student_ID='$student_id'
AND Certificate_Type='{$_POST['certificate_type']}'
");
        $message = "Certificate Revoked Successfully";
        $showPopup = true;
    } else {
        $message = "Register Number Not Found";
        $showPopup = true;
    }
}

if (isset($_POST['generate'])) {

    $student_name     = strtoupper($_POST['student_name']);
    $register_number  = $_POST['register_number'];
    $course           = strtoupper($_POST['course']);
    $year             = $_POST['year'];
    $institution      = $_POST['institution'];
    $certificate_type = $_POST['certificate_type'];

    $check_student = mysqli_query(
        $conn,
        "SELECT Student_ID FROM STUDENT WHERE Register_Number='$register_number'"
    );

    if (mysqli_num_rows($check_student) == 0) {
        mysqli_query($conn, "
            INSERT INTO STUDENT 
            (Student_Name, Register_Number, Course, Year, Institution)
            VALUES 
            ('$student_name','$register_number','$course','$year','$institution')
        ");
        $student_id = mysqli_insert_id($conn);
    } else {
        $row = mysqli_fetch_assoc($check_student);
        $student_id = $row['Student_ID'];
    }

    $duplicate_check = mysqli_query($conn, "
    SELECT *
    FROM CERTIFICATE c
    JOIN STUDENT s ON c.Student_ID = s.Student_ID
    WHERE s.Register_Number = '$register_number'
    AND c.Certificate_Type = '$certificate_type'
    AND c.Status = 'Valid'
    ");

    if (mysqli_num_rows($duplicate_check) > 0) {
        $message = "Certificate Already Exists For This Student";
        $showPopup = true;
    } else {

        mysqli_query($conn, "
    INSERT INTO CERTIFICATE 
    (Student_ID, Certificate_Type, Issue_Date, Certificate_File_Path, Status)
    VALUES
    ('$student_id','$certificate_type',CURDATE(),'','Valid')
    ");

        $auto_id = mysqli_insert_id($conn);
    }

    if (!isset($auto_id)) {
    } else {

        if (isset($auto_id)) {
            $certificate_number = "CERT-" . date("Y") . "-" . str_pad($auto_id, 3, "0", STR_PAD_LEFT);
            $pdf_path = "../certificates/" . $certificate_number . ".pdf";
            mysqli_query($conn, "
        UPDATE CERTIFICATE 
        SET Certificate_File_Path='certificates/$certificate_number.pdf'
        WHERE Certificate_ID='$auto_id'
        ");

            $verify_url = "http://localhost/digital_certificates/verify.php?id=" . $auto_id;

            $qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="
                . urlencode($verify_url);

            $qr_path = "../qrcodes/" . $certificate_number . ".png";
            file_put_contents($qr_path, file_get_contents($qr_api));

            mysqli_query($conn, "
    INSERT INTO QR_CODE (Certificate_ID, QR_Image, Verification_URL)
    VALUES ('$auto_id','qrcodes/$certificate_number.png','$verify_url')
    ");

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile("../templates/certificate_template.pdf");
            $tpl = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($tpl);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height']);

            $pdf->SetFont("Arial", "", 10);
            $pdf->SetXY(25, 15);
            $pdf->Cell(80, 8, "Certificate ID: " . $certificate_number, 0, 1, "L");

            $pdf->SetFont("Times", "", 17);
            $pdf->SetXY(80, 75);
            $pdf->MultiCell(140, 6, "This certificate is proudly presented to ", 0, "C");

            $pdf->SetFont("Courier", "B", 28);
            $pdf->SetXY(80, 90);
            $pdf->MultiCell(140, 6, $student_name, 0, "C");

            $pdf->SetFont("Times", "", 17);
            $pdf->SetXY(60, 105);

            $text =
                "bearing Register Number " . $register_number .
                " for " . $certificate_type .
                " during the academic year " . $year .
                " in the Department of " . $course .
                " at " . $institution .
                ". This certificate is issued in recognition of the student's achievement " .
                "and its authenticity is subject to verification through the embedded QR code.";

            $pdf->MultiCell(180, 6, $text, 0, "C");

            $pdf->SetFont("Arial", "", 11);
            $pdf->SetXY(30, 150);
            $pdf->Cell(80, 8, "Date: " . date("d-m-Y"), 0, 1);

            $pdf->Image($qr_path, 235, 140, 30);
            $pdf->Link(235, 140, 30, 30, $verify_url);

            $pdf->Output("F", $pdf_path);

            $file_hash = hash_file("sha256", $pdf_path);

            mysqli_query($conn, "
    UPDATE CERTIFICATE
    SET File_Hash='$file_hash'
    WHERE Certificate_ID='$auto_id'
    ");

            $download_link = "../certificates/" . $certificate_number . ".pdf";
            $message = "Certificate Generated Successfully";
            $showPopup = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            background-image: url("1.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgb(250, 249, 253);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px gray;
            text-align: center;
            width: 450px;
        }

        input,
        select {
            width: 95%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        select {
            color: black;
        }

        .generate-btn {
            width: 95%;
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .revoke-btn {
            width: 95%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px gray;
            text-align: center;
            z-index: 999;
        }
    </style>
</head>

<body>

    <?php if ($showPopup) { ?>
        <div class="popup">
            <h3><?php echo $message; ?></h3>
            <br>
            <?php if ($download_link != "") { ?>
                <a href="<?php echo $download_link; ?>" target="_blank" class="generate-btn">
                    Download Certificate
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="container">

        <h2>Certificate Management</h2>

        <form method="POST">

            Student Name:<br>
            <input type="text" name="student_name" placeholder="Enter Student Name"><br><br>

            Register Number:<br>
            <input type="text" name="register_number" placeholder="Enter Register Number" required><br><br>

            Course:<br>
            <input type="text" name="course" placeholder="Enter Course"><br><br>

            Academic Year:<br>
            <input type="text" name="year" placeholder="Enter Academic Year"><br><br>

            Institution:<br>
            <select name="institution" required
                style="width:95%; padding:10px; color:gray;"
                onchange="this.style.color='black'">
                <option value="" disabled selected hidden>Select Institution</option>
                <?php
                $inst = mysqli_query($conn, "SELECT * FROM INSTITUTION ORDER BY Institution_Name ASC");
                while ($row = mysqli_fetch_assoc($inst)) {
                    echo "<option value='" . $row['Institution_Name'] . "'>" . $row['Institution_Name'] . "</option>";
                }
                ?>
            </select><br><br>

            Certificate For:<br>
            <input type="text" name="certificate_type" placeholder="Enter Certificate Type"><br><br>

            <button type="submit" name="generate" class="generate-btn">
                Generate Certificate
            </button>

            <br><br>

            <button type="submit" name="revoke" class="revoke-btn">
                Revoke Certificate
            </button>

        </form>

    </div>

</body>

</html>