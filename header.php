<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Management System</title>
    <style>
        * { box-sizing: border-box;
		margin: 0;
		padding: 0; 
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
        body { 
		background-color: #f4f6f9; 
		color: #333; 
		}
        
        .main-header {
            background-color: #1e1e2f;
            color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo-area h2 {
            font-size: 20px;
            color: #ffc107;
            letter-spacing: 1px;
        }
        .center-title h1 {
            font-size: 22px;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .user-nav a {
            color: #fff;
            text-decoration: none;
            background: #dc3545;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
        }
        .user-nav a:hover {
			background: #b02a37;
			}

        /* Navbar Links */
        .nav-links {
            background: #ffffff;
            padding: 10px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 25px;
        }
        .nav-links a {
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: 0.2s;
         }
        .nav-links a:hover {
            background: #007bff;
            color: white;
        }
        .main-content {
            padding: 0 20px 40px 20px;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo-area">
            <h2> HOTEL ELITE</h2>
        </div>
        <div class="center-title">
            <h1>Hotel Management System</h1>
        </div>
        <div class="user-nav">
            <?php
			if(isset($_SESSION['admin'])) 
			{
				?>
                <a href="logout.php">Logout</a>
            <?php
			} 
			?>
        </div>
    </header>

    <div class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="room_management.php">Rooms</a>
    <a href="booking_management.php">Bookings</a>
    <a href="food_orders.php">Food Orders</a>
    <a href="reports.php">Reports</a> 
</div>
    </div>

    <div class="main-content">