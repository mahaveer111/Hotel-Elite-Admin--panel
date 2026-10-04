<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']))
	{
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'update_booking') 
{
    $booking_id = (int)$_GET['id'];
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $conn->query("UPDATE bookings SET booking_status='$status' WHERE booking_id=$booking_id");
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'update_food') 
{
    $order_id = (int)$_GET['id'];
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $conn->query("UPDATE food_orders SET order_status='$status' WHERE order_id=$order_id");
    header("Location: dashboard.php");
    exit();
}

include('header.php');
?>

<style>
    .dashboard-container {
		max-width: 1100px; 
		margin: 0 auto;
		text-align: center; 
		}
    .welcome-banner {
		background: linear-gradient(135deg, #007bff, #0056b3); 
		color: white;
		padding: 25px; 
		border-radius: 8px;
		margin-bottom: 35px;
		box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
		}
    .welcome-banner h1 {
		font-size: 24px;
		margin-bottom: 5px; 
		}
    .welcome-banner p { 
	font-size: 15px; 
	opacity: 0.9; 
	}
    .section-title {
		color: #333;
		margin: 40px 0 20px 0;
		font-size: 20px;
		position: relative; 
		display: inline-block;
		}
    .section-title::after {
		content: '';
		display: block;
		width: 50px;
		height: 3px;
		background: #007bff;
		margin: 6px auto 0 auto;
		border-radius: 2px; 
		}
    .data-table { 
	width: 100%; 
	border-collapse: collapse; 
	background: #fff;
	box-shadow: 0 4px 6px rgba(0,0,0,0.05);
	border-radius: 6px; 
	overflow: hidden; 
	margin-bottom: 20px; 
	text-align: center;
	}
    .data-table th, .data-table td { 
	padding: 12px; 
	border: 1px solid #ddd;
	}
    .data-table th {
		background-color: #007bff;
		color: white;
		font-size: 14px; 
		text-transform: uppercase;
		}
    .data-table tr:nth-child(even) { 
	background-color: #f9f9f9; 
	}
    .btn {
		padding: 6px 12px; 
		text-decoration: none; 
		border-radius: 4px; 
		color: white;
		font-size: 12px; 
		font-weight: bold; 
		display: inline-block;
		margin: 2px;
		}
    .btn-accept {
		background-color: #28a745;
		}
    .btn-reject {
		background-color: #dc3545; 
		}
    .status-badge {
		padding: 5px 10px; 
		border-radius: 20px; 
		font-weight: bold;
		font-size: 11px; 
		display: inline-block;
		}
    .status-Pending { 
	background: #fff3cd;
	color: #856404;
	}
    .status-Confirmed, .status-Delivered {
		background: #d4edda;
		color: #155724;
		}
    .status-Cancelled {
		background: #f8d7da;
		color: #721c24; 
		}
</style>

<div class="dashboard-container">
    <div class="welcome-banner">
        <h1>Welcome Back, Admin <?php echo htmlspecialchars($_SESSION['admin']); ?>! 👋</h1>
        <p>Seamlessly manage your rooms,bookings,and dinining,in,one place.</p>
    </div>

    <h2 class="section-title">🛏️ Rooms Overview</h2>
    <table class="data-table">
        <tr>
            <th>Room ID</th>
            <th>Room No</th>
            <th>Category</th>
            <th>Price</th>
            <th>Occupancy Status</th>
            <th>Cleanliness</th>
        </tr>
        <?php
        $rooms = $conn->query("SELECT * FROM rooms");
        if ($rooms && $rooms->num_rows > 0) 
		{
            while ($row = $rooms->fetch_assoc())
				{
                echo "<tr>
                        <td>{$row['room_id']}</td>
                        <td><b>{$row['room_no']}</b></td>
                        <td>{$row['category']}</td>
                        <td>₹{$row['price']}</td>
                        <td>{$row['occupancy_status']}</td>
                        <td>{$row['cleanliness_status']}</td>
                      </tr>";
            }
        }
		else 
		{
            echo "<tr><td colspan='6'>No rooms found.</td></tr>";
        }
        ?>
    </table>

    <h2 class="section-title">📅 Room Bookings Management</h2>
    <table class="data-table">
        <tr>
            <th>Booking ID</th>
            <th>Customer Name</th>
            <th>Room No</th>
            <th>Check-In / Out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $bookings = $conn->query("SELECT * FROM bookings ORDER BY booking_id DESC");
        if ($bookings && $bookings->num_rows > 0) 
		{
            while ($row = $bookings->fetch_assoc()) 
			{
                echo "<tr>
                        <td>{$row['booking_id']}</td>
                        <td>{$row['customer_name']}</td>
                        <td>{$row['room_no']}</td>
                        <td>{$row['check_in_date']} to {$row['check_out_date']}</td>
                        <td><span class='status-badge status-{$row['booking_status']}'>{$row['booking_status']}</span></td>
                        <td>";
                if ($row['booking_status'] == 'Pending')
					{
                    echo "<a href='dashboard.php?action=update_booking&id={$row['booking_id']}&status=Confirmed' class='btn btn-accept'>Accept</a>";
                    echo "<a href='dashboard.php?action=update_booking&id={$row['booking_id']}&status=Cancelled' class='btn btn-reject'>Reject</a>";
                } 
				else 
				{
                    echo "<span>Completed</span>";
                }
                echo "  </td>
                      </tr>";
            }
        }
		else
			{
            echo "<tr><td colspan='6'>No bookings found.</td></tr>";
        }
        ?>
    </table>

    <h2 class="section-title">🍽️ Food Orders Management</h2>
    <table class="data-table">
        <tr>
            <th>Order ID</th>
            <th>Room No</th>
            <th>Item Name</th>
            <th>Amount & Mode</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $food = $conn->query("SELECT * FROM food_orders ORDER BY order_id DESC");
        if ($food && $food->num_rows > 0)
			{
            while ($row = $food->fetch_assoc())
				{
                echo "<tr>
                        <td>{$row['order_id']}</td>
                        <td>{$row['room_no']}</td>
                        <td><b>{$row['item_name']}</b></td>
                        <td>₹{$row['total_amount']} ({$row['payment_method']})</td>
                        <td><span class='status-badge status-{$row['order_status']}'>{$row['order_status']}</span></td>
                        <td>";
                if ($row['order_status'] == 'Pending')
					{
                    echo "<a href='dashboard.php?action=update_food&id={$row['order_id']}&status=Delivered' class='btn btn-accept'>Deliver</a>";
                    echo "<a href='dashboard.php?action=update_food&id={$row['order_id']}&status=Cancelled' class='btn btn-reject'>Cancel</a>";
                } 
				else
					{
                    echo "<span>Completed</span>";
                }
                echo "  </td>
                      </tr>";
            }
        }
		else
			{
            echo "<tr><td colspan='6'>No food orders found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php 
include('footer.php');
 ?>