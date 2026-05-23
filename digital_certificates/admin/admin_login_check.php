<?php
include "../db.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM ADMIN 
          WHERE Username='$username' AND Password='$password'";

$result = mysqli_query($conn, $query);
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
            font-family: Arial, sans-serif;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px gray;
            text-align: center;
            width: 380px;
        }

        .link-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .link-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>

<body>

    <div class="container">

        <?php
        if (mysqli_num_rows($result) == 1) {
            echo "<h3 style='color:green;'>Login successful</h3>";
            echo "<a class='link-btn' href='admin_home.php'>
            Go to Generate Certificate
          </a>";
        } else {
            echo "<h3 style='color:red;'>Invalid login</h3>";
            echo "<a class='link-btn' href='admin_login.html'>
            Try Again
          </a>";
        }
        ?>

    </div>

</body>

</html>