<?php
session_start();
include('db.php');
if (!isset($_SESSION['admin']))
	{
    header("Location: login.php");
    exit();
}

include('header.php');
?>

<style>
    .report-container {
        max-width: 1100px;
        margin: 0 auto;
        text-align: center;
      }
     .report-title {
        color: #333;
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: bold;
       }
     .card-box {
        background: #fff;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        text-align: left;
      }
    .card-box h3 {
        margin-bottom: 15px;
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        margin-bottom: 20px;
        text-align: center;
	 }
     .data-table th, .data-table td {
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 13px;
    }
    .data-table th {
        background-color: #343a40;
        color: white;
        text-transform: uppercase;
    }
    .print-btn {
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        margin-bottom: 20px;
        float: right;
    }
    @media print {
        .main-header, .nav-links, .print-btn {
            display: none;
        }
    }
</style>

<div class="report-container">
    <h2 class="report-title">Hotel Management System - Comprehensive Reports</h2>
    
    <button class="print-btn" onclick="window.print()"> Print Report</button>
    
    <div style="clear: both;"></div>
    <div class="card-box">
        <h3>Room Bookings Report</h3>
        <table class="data-table">
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Room No</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Amount (₹)</th>
                <th>Status</th>
            </tr>
            <?php
             $bookings = $conn->query("SELECT * FROM bookings ORDER BY booking_id DESC");
            if ($bookings && $bookings->num_rows > 0) 
			{
                 while ($row = $bookings->fetch_assoc())
					{
                             echo "<tr>
                            <td>{$row['booking_id']}</td>
                            <td><b>{$row['customer_name']}</b></td>
                            <td>{$row['room_no']}</td>
                            <td>{$row['check_in_date']}</td>
                            <td>{$row['check_out_date']}</td>
                            <td>₹{$row['total_amount']}</td>
                            <td>{$row['booking_status']}</td>
                          </tr>";
                }
            } 
			else 
			 {
                echo "<tr><td colspan='7'>No booking records found.</td></tr>";
            }
            ?>
        </table>
    </div>
    <div class="card-box">
        <h3>Food Orders Report</h3>
        <table class="data-table">
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Room No</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Total (₹)</th>
                <th>Status</th>
            </tr>
            <?php
            $food = $conn->query("SELECT * FROM food_orders ORDER BY order_id DESC");
            if ($food && $food->num_rows > 0) 
                 {
                while ($row = $food->fetch_assoc())
       			 		{
                             echo "<tr>
                            <td>{$row['order_id']}</td>
                            <td><b>{$row['customer_name']}</b></td>
                            <td>{$row['room_no']}</td>
                            <td>{$row['item_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>₹{$row['total_amount']}</td>
                            <td>{$row['order_status']}</td>
                          </tr>";
                }
            } 
			else 
			 {
                echo "<tr><td colspan='7'>No food order records found.</td></tr>";
            }
            ?>
        </table>
    </div>

</div>

<?php
include('footer.php');
?>