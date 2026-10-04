<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) 
{
    header("Location: login.php");
    exit();
}

$error_msg = "";
$success_msg = "";

if (isset($_POST['add_booking'])) 
{
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in_date']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out_date']);
    $total_amount = (float)$_POST['total_amount'];
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    $conflict_check = $conn->query("SELECT * FROM bookings WHERE room_no = '$room_no' AND booking_status != 'Cancelled' AND ((check_in_date <= '$check_out') AND (check_out_date >= '$check_in'))");

    if ($conflict_check->num_rows > 0)
		{
        $error_msg = "Error: Room {$room_no} is already booked for these dates!";
    }
	else 
	{
        $sql = "INSERT INTO bookings (customer_name, room_no, check_in_date, check_out_date, total_amount, booking_status, payment_status, payment_method) 
                VALUES ('$customer_name', '$room_no', '$check_in', '$check_out', $total_amount, 'Confirmed', '$payment_status', '$payment_method')";
        if ($conn->query($sql))
			{
            $conn->query("UPDATE rooms SET occupancy_status = 'Occupied' WHERE room_no = '$room_no'");
            $success_msg = "Booking added successfully!";
        }
    }
}

if (isset($_POST['update_booking'])) 
{
    $id = (int)$_POST['booking_id'];
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in_date']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out_date']);
    $total_amount = (float)$_POST['total_amount'];
    $booking_status = mysqli_real_escape_string($conn, $_POST['booking_status']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    $conn->query("UPDATE bookings SET customer_name='$customer_name', room_no='$room_no', check_in_date='$check_in', check_out_date='$check_out', total_amount=$total_amount, booking_status='$booking_status', payment_status='$payment_status', payment_method='$payment_method' WHERE booking_id=$id");
    
    if ($booking_status == 'Cancelled' || $booking_status == 'Checked-Out')
		{
        $conn->query("UPDATE rooms SET occupancy_status = 'Vacant', cleanliness_status = 'Dirty' WHERE room_no = '$room_no'");
    }

    header("Location: booking_management.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT room_no FROM bookings WHERE booking_id = $id");
    if($res->num_rows > 0) 
	{
        $row = $res->fetch_assoc();
        $r_no = $row['room_no'];
        $conn->query("UPDATE rooms SET occupancy_status = 'Vacant' WHERE room_no = '$r_no'");
    }
    $conn->query("DELETE FROM bookings WHERE booking_id = $id");
    header("Location: booking_management.php");
    exit();
}

$search = "";
if (isset($_GET['search'])) 
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM bookings WHERE customer_name LIKE '%$search%' OR room_no LIKE '%$search%' ORDER BY booking_id DESC";
} 
else 
{
    $query = "SELECT * FROM bookings ORDER BY booking_id DESC";
}
$result = $conn->query($query);

include('header.php');
?>

<style>
    .page-container {
        max-width: 1150px;
        margin: 0 auto;
        text-align: center;
    }
    .page-title {
        color: #333;
        margin-bottom: 20px;
        font-size: 20px;
        font-weight: bold;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        border: 1px solid #f5c6cb;
        font-size: 13px;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        border: 1px solid #c3e6cb;
        font-size: 13px;
    }
    .action-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .search-form input {
        padding: 8px;
        width: 250px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .search-form button, .btn-open-modal {
        padding: 8px 15px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-radius: 6px;
        overflow: hidden;
        text-align: center;
    }
    .data-table th, .data-table td {
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 13px;
    }
    .data-table th {
        background-color: #007bff;
        color: white;
        text-transform: uppercase;
    }
    .btn {
        padding: 5px 8px;
        text-decoration: none;
        border-radius: 4px;
        color: white;
        font-size: 11px;
        font-weight: bold;
        display: inline-block;
        margin: 2px;
        border: none;
        cursor: pointer;
    }
    .btn-edit {
        background-color: #ffc107;
        color: #000;
    }
    .btn-delete {
        background-color: #dc3545;
    }
    .badge-confirm {
        background: #28a745;
        padding: 3px 6px;
        border-radius: 3px;
        color: white;
    }
    .badge-cancel {
        background: #dc3545;
        padding: 3px 6px;
        border-radius: 3px;
        color: white;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background: white;
        padding: 25px;
        border-radius: 8px;
        width: 420px;
        text-align: left;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .form-group {
        margin-bottom: 12px;
    }
    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-weight: bold;
        font-size: 13px;
        color: #555;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .close-btn {
        float: right;
        font-size: 20px;
        cursor: pointer;
        color: #aaa;
    }
</style>

<div class="page-container">
    <h2 class="page-title">Booking Management & Payment Details</h2>

    <?php 
	if(!empty($error_msg))
		{
			echo "<div class='alert-danger'>$error_msg</div>"; 
			} 
			?>
    <?php 
	if(!empty($success_msg))
		{ 
	echo "<div class='alert-success'>$success_msg</div>";
	}
	?>

    <div class="action-bar">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search customer or room..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <?php
			if(!empty($search))
				{
					echo "<a href='booking_management.php' class='btn' style='background:#6c757d; padding:8px;'>Reset</a>";
					} 
					?>
        </form>
        <button class="btn-open-modal" onclick="openAddModal()">+ New Booking</button>
    </div>

    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Room No</th>
            <th>Dates</th>
            <th>Amount & Payment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) 
		{
            while ($row = $result->fetch_assoc())
				{
                $status_badge = ($row['booking_status'] == 'Confirmed') ? 'badge-confirm' : 'badge-cancel';
                $amt = isset($row['total_amount']) ? $row['total_amount'] : '0.00';
                $pay_status = isset($row['payment_status']) ? $row['payment_status'] : 'Pending';
                $pay_method = isset($row['payment_method']) ? $row['payment_method'] : 'Cash';

                echo "<tr>
                        <td>{$row['booking_id']}</td>
                        <td><b>{$row['customer_name']}</b></td>
                        <td>Room {$row['room_no']}</td>
                        <td>{$row['check_in_date']} <br>to {$row['check_out_date']}</td>
                        <td>₹{$amt}<br><small><b>Pay:</b> {$pay_status} ({$pay_method})</small></td>
                        <td><span class='{$status_badge}'>{$row['booking_status']}</span></td>
                        <td>
                            <button class='btn btn-edit' onclick='openEditModal({$row['booking_id']}, \"{$row['customer_name']}\", \"{$row['room_no']}\", \"{$row['check_in_date']}\", \"{$row['check_out_date']}\", {$amt}, \"{$row['booking_status']}\", \"{$pay_status}\", \"{$pay_method}\")'>Edit</button>
                            <a href='booking_management.php?action=delete&id={$row['booking_id']}' class='btn btn-delete' onclick='return confirm(\"Cancel/Delete this booking?\")'>Delete</a>
                        </td>
                      </tr>";
            }
        } 
		else 
		{
            echo "<tr><td colspan='7'>No bookings found.</td></tr>";
        }
        ?>
    </table>
</div>

<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle" style="margin-bottom: 15px; color: #333;">New Booking</h3>
        <form method="POST" id="modalForm">
            <input type="hidden" name="booking_id" id="booking_id">
            
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" required>
            </div>
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" name="room_no" id="room_no" placeholder="Enter Room No" required>
            </div>
            <div class="form-group">
                <label>Check-In Date</label>
                <input type="date" name="check_in_date" id="check_in_date" required>
            </div>
            <div class="form-group">
                <label>Check-Out Date</label>
                <input type="date" name="check_out_date" id="check_out_date" required>
            </div>
            <div class="form-group">
                <label>Total Amount (₹)</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" required>
            </div>
            <div class="form-group" id="statusGroup" style="display:none;">
                <label>Booking Status</label>
                <select name="booking_status" id="booking_status">
                    <option value="Confirmed">Confirmed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Checked-Out">Checked-Out</option>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Status</label>
                <select name="payment_status" id="payment_status">
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" id="payment_method">
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>
            
            <button type="submit" name="add_booking" id="submitBtn" class="btn-open-modal" style="width:100%; margin-top:10px;">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
    function openAddModal() 
	{
        document.getElementById('modalTitle').innerText = "New Room Booking";
        document.getElementById('modalForm').reset();
        document.getElementById('booking_id').value = "";
        document.getElementById('statusGroup').style.display = "none";
        document.getElementById('submitBtn').name = "add_booking";
        document.getElementById('submitBtn').innerText = "Confirm Booking";
        document.getElementById('bookingModal').style.display = "flex";
    }

    function openEditModal(id, name, room, checkin, checkout, amount, bstatus, pstatus, pmethod) {
        document.getElementById('modalTitle').innerText = "Edit Booking";
        document.getElementById('booking_id').value = id;
        document.getElementById('customer_name').value = name;
        document.getElementById('room_no').value = room;
        document.getElementById('check_in_date').value = checkin;
        document.getElementById('check_out_date').value = checkout;
        document.getElementById('total_amount').value = amount;
        document.getElementById('booking_status').value = bstatus;
        document.getElementById('payment_status').value = pstatus;
        document.getElementById('payment_method').value = pmethod;
        
        document.getElementById('statusGroup').style.display = "block";
        document.getElementById('submitBtn').name = "update_booking";
        document.getElementById('submitBtn').innerText = "Update Booking";
        document.getElementById('bookingModal').style.display = "flex";
    }

    function closeModal() 
	{
        document.getElementById('bookingModal').style.display = "none";
    }
</script>

<?php
 include('footer.php');
 ?>