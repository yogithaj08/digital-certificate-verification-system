<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../db.php";

require_once __DIR__ . '/../fpdf/fpdf.php';
require_once __DIR__ . '/../fpdi/src/autoload.php';

use setasign\Fpdi\Fpdi;

$message = "";

if (isset($_POST['upload'])) {

    $get_student = mysqli_query($conn, "
SELECT Student_ID 
FROM STUDENT 
WHERE Student_Name='External Student'
LIMIT 1
");

    if (mysqli_num_rows($get_student) == 0) {

        mysqli_query($conn, "
INSERT INTO STUDENT
(Student_Name, Register_Number, Course, Year, Institution)
VALUES
('External Student','EXT" . time() . "','External','External','External')
");

        $student_id = mysqli_insert_id($conn);
    } else {
        $row = mysqli_fetch_assoc($get_student);
        $student_id = $row['Student_ID'];
    }

    $certificate_number = "EXT-" . date("Y") . "-" . time();
    $pdf_path = "../certificates/" . $certificate_number . ".pdf";

    mysqli_query($conn, "
INSERT INTO CERTIFICATE
(Student_ID, Certificate_Type, Issue_Date, Certificate_File_Path, Status)
VALUES
('$student_id','External',CURDATE(),'certificates/$certificate_number.pdf','Valid')
");

    $auto_id = mysqli_insert_id($conn);

    $verify_url = "http://localhost/digital_certificates/verify.php?id=" . $auto_id;

    $qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="
        . urlencode($verify_url);

    $qr_path = "../qrcodes/" . $certificate_number . ".png";

    file_put_contents($qr_path, file_get_contents($qr_api));

    mysqli_query($conn, "
INSERT INTO QR_CODE
(Certificate_ID, QR_Image, Verification_URL)
VALUES
('$auto_id','qrcodes/$certificate_number.png','$verify_url')
");

    $tmp_pdf = $_FILES['external_pdf']['tmp_name'];

    $pdf = new Fpdi();

    $pageCount = $pdf->setSourceFile($tmp_pdf);

    $tpl = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($tpl);

    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

    $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height']);

    $pdf->Image($qr_path, 235, 140, 30);

    $pdf->Output("F", $pdf_path);

    $file_hash = hash_file("sha256", $pdf_path);

    mysqli_query($conn, "
UPDATE CERTIFICATE
SET File_Hash='$file_hash'
WHERE Certificate_ID='$auto_id'
");

    $message = "External Certificate Uploaded Successfully";
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
            font-family: Arial;
            text-align: center;
            padding-top: 80px;
        }

        .container {
            display: inline-block;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px gray;
            width: 400px;
        }

        input {
            width: 90%;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>

</head>

<body>

    <div class="container">

        <h2>Upload External Certificate</h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="file" name="external_pdf" accept="application/pdf" required>
            <br><br>

            <button type="submit" name="upload">Upload Certificate</button>

        </form>

        <br>

        <?php
        if ($message != "") {
            echo "<b>" . $message . "</b>";
        }
        ?>

    </div>

</body>

</html>