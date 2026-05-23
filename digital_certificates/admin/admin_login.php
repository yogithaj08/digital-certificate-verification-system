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
            background: rgb(253, 253, 255);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px gray;
            text-align: center;
            width: 380px;
        }

        input {
            width: 90%;
            padding: 8px;
        }

        .login-btn {
            width: 90%;
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: darkgreen;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Admin Login</h2>

        <form action="admin_login_check.php" method="POST">

            Username:<br>
            <input type="text" name="username" required><br><br>

            Password:<br>
            <input type="password" name="password" required><br><br>

            <button type="submit" class="login-btn">Login</button>

        </form>

    </div>

</body>

</html>