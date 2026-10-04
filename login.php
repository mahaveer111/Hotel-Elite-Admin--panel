<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['login'])) 
{
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if ($user === 'Ayush' && $pass === 'Vyas1111')
		{
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
        exit();
    } 
	else 
	{
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Hotel Management</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            font-size: 14px;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: #dc3545;
            font-size: 13px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>🏨 Admin Login</h2>
    <?php
	if (!empty($error)) 
	{ 
echo "<div class='error'>$error</div>";
 } 
 ?>
    <form method="POST" autocomplete="off">
        <label>Username</label>
        <input type="text" name="username" placeholder="Ayush" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Vyas1111" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>