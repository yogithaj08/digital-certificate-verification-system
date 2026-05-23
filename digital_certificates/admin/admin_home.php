<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            background-image: url("1.png");
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px gray;
        }

        .btn {
            display: block;
            width: 250px;
            padding: 12px;
            margin: 15px;
            background: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn2 {
            background: #3498db;
        }
    </style>
</head>

<body>

    <div class="box">

        <h2>Admin Panel</h2>

        <a class="btn" href="generate_certificate.php">
            Generate Certificate (College Details)
        </a>

        <a class="btn btn2" href="upload_external.php">
            Upload External Certificate
        </a>

    </div>

</body>

</html>