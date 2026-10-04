<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) 
{
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_order']))
	{
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $total_amount = $quantity * $price;
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $conn->query("INSERT INTO food_orders (customer_name, room_no, item_name, quantity, price, total_amount, order_status, payment_status, payment_method) 
                  VALUES ('$customer_name', '$room_no', '$item_name', $quantity, $price, $total_amount, 'Pending', '$payment_status', '$payment_method')");
    header("Location: food_orders.php");
    exit();
}

if (isset($_POST['update_order'])) 
{
    $id = (int)$_POST['order_id'];
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $total_amount = $quantity * $price;
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $conn->query("UPDATE food_orders SET customer_name='$customer_name', room_no='$room_no', item_name='$item_name', quantity=$quantity, price=$price, total_amount=$total_amount, order_status='$order_status', payment_status='$payment_status', payment_method='$payment_method' WHERE order_id=$id");
    header("Location: food_orders.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') 
{
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM food_orders WHERE order_id = $id");
    header("Location: food_orders.php");
    exit();
}

$search = "";
if (isset($_GET['search'])) 
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM food_orders WHERE customer_name LIKE '%$search%' OR room_no LIKE '%$search%' OR item_name LIKE '%$search%' ORDER BY order_id DESC";
} 
else
	{
    $query = "SELECT * FROM food_orders ORDER BY order_id DESC";
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
    .badge-pending {
        background: #ffc107;
        color: black;
        padding: 3px 6px;
        border-radius: 3px;
    }
    .badge-preparing {
        background: #17a2b8;
        color: white;
        padding: 3px 6px;
        border-radius: 3px;
    }
    .badge-delivered {
        background: #28a745;
        color: white;
        padding: 3px 6px;
        border-radius: 3px;
    }
    .badge-cancelled {
        background: #dc3545;
        color: white;
        padding: 3px 6px;
        border-radius: 3px;
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
    <h2 class="page-title">Food Orders Management</h2>

    <div class="action-bar">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search customer, room or item..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <?php
			if(!empty($search)) 
			{ 
		echo "<a href='food_orders.php' class='btn' style='background:#6c757d; padding:8px;'>Reset</a>";
		} 
		?>
        </form>
        <button class="btn-open-modal" onclick="openAddModal()">+ Add New Food Order</button>
    </div>

    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Room No</th>
            <th>Item Name</th>
            <th>Qty & Price</th>
            <th>Total</th>
            <th>Status & Payment</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0)
			{
            while ($row = $result->fetch_assoc())
				{
                $status = $row['order_status'];
                $badge_class = 'badge-pending';
                if($status == 'Preparing') $badge_class = 'badge-preparing';
                elseif($status == 'Delivered') $badge_class = 'badge-delivered';
                elseif($status == 'Cancelled') $badge_class = 'badge-cancelled';

                $pay_method = isset($row['payment_method']) ? $row['payment_method'] : 'Cash';
                $total_amt = isset($row['total_amount']) ? $row['total_amount'] : ($row['quantity'] * $row['price']);

                echo "<tr>
                        <td>{$row['order_id']}</td>
                        <td><b>{$row['customer_name']}</b></td>
                        <td>Room {$row['room_no']}</td>
                        <td>{$row['item_name']}</td>
                        <td>{$row['quantity']} x ₹{$row['price']}</td>
                        <td><b>₹{$total_amt}</b></td>
                        <td>
                            <span class='{$badge_class}'>{$status}</span><br>
                            <small><b>Pay:</b> {$row['payment_status']} ({$pay_method})</small>
                        </td>
                        <td>
                            <button class='btn btn-edit' onclick='openEditModal({$row['order_id']}, \"{$row['customer_name']}\", \"{$row['room_no']}\", \"{$row['item_name']}\", {$row['quantity']}, {$row['price']}, \"{$status}\", \"{$row['payment_status']}\", \"{$pay_method}\")'>Edit</button>
                            <a href='food_orders.php?action=delete&id={$row['order_id']}' class='btn btn-delete' onclick='return confirm(\"Delete this food order?\")'>Delete</a>
                        </td>
                      </tr>";
            }
        }
		else
			{
            echo "<tr><td colspan='8'>No food orders found.</td></tr>";
        }
        ?>
    </table>
</div>

<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle" style="margin-bottom: 15px; color: #333;">Add Food Order</h3>
        <form method="POST" id="modalForm">
            <input type="hidden" name="order_id" id="order_id">
            
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" required>
            </div>
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" name="room_no" id="room_no" required>
            </div>
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" id="item_name" placeholder="e.g. Paneer Tikka" required>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" id="quantity" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label>Price per Unit (₹)</label>
                <input type="number" step="0.01" name="price" id="price" required>
            </div>
            <div class="form-group" id="statusGroup" style="display:none;">
                <label>Order Status</label>
                <select name="order_status" id="order_status">
                    <option value="Pending">Pending</option>
                    <option value="Preparing">Preparing</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
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
            
            <button type="submit" name="add_order" id="submitBtn" class="btn-open-modal" style="width:100%; margin-top:10px;">Save Order</button>
        </form>
    </div>
</div>

<script>
    function openAddModal()
	{
        document.getElementById('modalTitle').innerText = "Add Food Order";
        document.getElementById('modalForm').reset();
        document.getElementById('order_id').value = "";
        document.getElementById('statusGroup').style.display = "none";
        document.getElementById('submitBtn').name = "add_order";
        document.getElementById('submitBtn').innerText = "Save Order";
        document.getElementById('orderModal').style.display = "flex";
    }

    function openEditModal(id, name, room, item, qty, price, status, payment, method)
	{
        document.getElementById('modalTitle').innerText = "Edit Food Order";
        document.getElementById('order_id').value = id;
        document.getElementById('customer_name').value = name;
        document.getElementById('room_no').value = room;
        document.getElementById('item_name').value = item;
        document.getElementById('quantity').value = qty;
        document.getElementById('price').value = price;
        document.getElementById('order_status').value = status;
        document.getElementById('payment_status').value = payment;
        document.getElementById('payment_method').value = method;
        
        document.getElementById('statusGroup').style.display = "block";
        document.getElementById('submitBtn').name = "update_order";
        document.getElementById('submitBtn').innerText = "Update Order";
        document.getElementById('orderModal').style.display = "flex";
    }

    function closeModal() 
	{
        document.getElementById('orderModal').style.display = "none";
    }
</script>

<?php
 include('footer.php');
 ?>