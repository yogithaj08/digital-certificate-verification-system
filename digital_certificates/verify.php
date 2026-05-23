<?php
include "db.php";

if (!isset($_GET['id'])) {
    echo "<h2>Invalid Request</h2>";
    exit;
}

$certificate_id = $_GET['id'];

$check = mysqli_query($conn, "
SELECT 
    c.Certificate_ID,
    c.Certificate_Type,
    c.Issue_Date,
    c.Status,
    c.Certificate_File_Path,
    c.File_Hash,
    s.Student_Name,
    s.Register_Number,
    s.Course,
    s.Year,
    s.Institution
FROM CERTIFICATE c
JOIN STUDENT s 
ON c.Student_ID = s.Student_ID
WHERE c.Certificate_ID = '$certificate_id'
");

if (mysqli_num_rows($check) == 0) {

    mysqli_query($conn, "
    INSERT INTO VERIFICATION 
    (Certificate_ID, Scan_Date, Verification_Result)
    VALUES ('$certificate_id', NOW(), 'Invalid')
    ");

    echo "<h2>Invalid Certificate</h2>";
    exit;
}

$row = mysqli_fetch_assoc($check);

$file_path = $row['Certificate_File_Path'];

if (!file_exists($file_path)) {
    $file_path = "certificates/" . $file_path;
}

$current_hash = hash_file("sha256", $file_path);

if ($current_hash != $row['File_Hash']) {

    mysqli_query($conn, "
INSERT INTO VERIFICATION 
(Certificate_ID, Scan_Date, Verification_Result)
VALUES ('$certificate_id', NOW(), 'Tampered')
");

    echo "<h2>Certificate Tampered</h2>";
    exit;
}

if ($row['Status'] != 'Valid') {

    mysqli_query($conn, "
INSERT INTO VERIFICATION 
(Certificate_ID, Scan_Date, Verification_Result)
VALUES ('$certificate_id', NOW(), 'Invalid')
");

    echo "<h2>Invalid Certificate</h2>";
    exit;
}

mysqli_query($conn, "
INSERT INTO VERIFICATION 
(Certificate_ID, Scan_Date, Verification_Result)
VALUES ('$certificate_id', NOW(), 'Valid')
");

$reg = $row['Register_Number'];
if (strlen($reg) > 5) {
    $masked_reg = substr($reg, 0, 3) .
        str_repeat("*", strlen($reg) - 5) .
        substr($reg, -2);
} else {
    $masked_reg = $reg;
}

$name_parts = explode(" ", $row['Student_Name']);
$masked_name = "";

foreach ($name_parts as $part) {

    if (strlen($part) > 2) {

        $masked_name .= substr($part, 0, 2) .
            str_repeat("*", strlen($part) - 2) . " ";
    } else {

        $masked_name .= $part . " ";
    }
}

$masked_name = trim($masked_name);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Certificate Verification</title>

    <style>
        body {
            font-family: Arial;
            text-align: center;
            padding-top: 50px;
            background: #f4f4f4;
        }

        .container {
            display: inline-block;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px gray;
            width: 400px;
        }

        .valid {
            color: green;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="valid">Certificate Verified</h2>
        <?php if ($row['Certificate_Type'] != 'External') { ?>

            <b>Student Name:</b> <?php echo $masked_name; ?><br>
            <b>Register Number:</b> <?php echo $masked_reg; ?><br>
            <b>Course:</b> <?php echo $row['Course']; ?><br>
            <b>Year:</b> <?php echo $row['Year']; ?><br>
            <b>Institution:</b> <?php echo $row['Institution']; ?><br><br>

        <?php } ?>

        <b>Certificate Type:</b> <?php echo $row['Certificate_Type']; ?><br>
        <b>Issue Date:</b> <?php echo $row['Issue_Date']; ?><br>
        <b>Status:</b> <?php echo $row['Status']; ?><br><br>

        <a href="<?php echo $row['Certificate_File_Path']; ?>" target="_blank">
            View / Download Certificate
        </a>

    </div>
</body>

</html>