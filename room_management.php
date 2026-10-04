<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) 
{
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_room'])) {
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    
    $conn->query("INSERT INTO rooms (room_no, category, price, occupancy_status, cleanliness_status) VALUES ('$room_no', '$category', $price, 'Vacant', 'Clean')");
    header("Location: room_management.php");
    exit();
}

if (isset($_POST['update_room'])) 
{
    $id = (int)$_POST['room_id'];
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    $occupancy_status = mysqli_real_escape_string($conn, $_POST['occupancy_status']);
    $cleanliness_status = mysqli_real_escape_string($conn, $_POST['cleanliness_status']);
    
    $conn->query("UPDATE rooms SET room_no='$room_no', category='$category', price=$price, occupancy_status='$occupancy_status', cleanliness_status='$cleanliness_status' WHERE room_id=$id");
    header("Location: room_management.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM rooms WHERE room_id = $id");
    header("Location: room_management.php");
    exit();
}

$search = "";
if (isset($_GET['search']))
	{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM rooms WHERE room_no LIKE '%$search%' OR category LIKE '%$search%' ORDER BY room_id DESC";
}
 else
	 {
    $query = "SELECT * FROM rooms ORDER BY room_id DESC";
}
$result = $conn->query($query);

include('header.php');
?>

<style>
    .page-container {
		max-width: 1100px;
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
    
    .badge-vacant { 
	background: #28a745;
	color: white;
	padding: 3px 6px; 
	border-radius: 3px; 
	}
    .badge-occupied { 
	background: #dc3545; 
	color: white;

	padding: 3px 6px; 
	border-radius: 3px;
	}
    .badge-clean { 
	background: #17a2b8;
	color: white; 
	padding: 3px 6px;
	border-radius: 3px;
	}
    .badge-dirty { 
	background: #ffc107; 
	color: black; 
	padding: 3px 6px; 
	border-radius: 3px; 
	}

    /* Modal CSS */
    .modal { 
	display: none;
	position: fixed; 
	z-index: 1000;
	left: 0; top: 0; 
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
	width: 400px;
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
    <h2 class="page-title">Room Management & Status Control</h2>

    <div class="action-bar">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search room no or category..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <?php if(!empty($search)) { echo "<a href='room_management.php' class='btn' style='background:#6c757d; padding:8px;'>Reset</a>"; } ?>
        </form>
        <button class="btn-open-modal" onclick="openAddModal()">+ Add New Room</button>
    </div>

    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Room No</th>
            <th>Category</th>
            <th>Price</th>
            <th>Occupancy</th>
            <th>Cleanliness</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0)
			{
            while ($row = $result->fetch_assoc()) 
			{
                $occ_badge = ($row['occupancy_status'] == 'Vacant') ? 'badge-vacant' : 'badge-occupied';
                $clean_badge = ($row['cleanliness_status'] == 'Clean') ? 'badge-clean' : 'badge-dirty';

                echo "<tr>
                        <td>{$row['room_id']}</td>
                        <td><b>Room {$row['room_no']}</b></td>
                        <td>{$row['category']}</td>
                        <td>₹{$row['price']}</td>
                       <td><span class='{$occ_badge}'>{$row['occupancy_status']}</span></td>
                        <td><span class='{$clean_badge}'>{$row['cleanliness_status']}</span></td>
                        <td>
                            <button class='btn btn-edit' onclick='openEditModal({$row['room_id']}, \"{$row['room_no']}\", \"{$row['category']}\", {$row['price']}, \"{$row['occupancy_status']}\", \"{$row['cleanliness_status']}\")'>Edit Status</button>
                            <a href='room_management.php?action=delete&id={$row['room_id']}' class='btn btn-delete' onclick='return confirm(\"Delete this room?\")'>Delete</a>
                        </td>
                      </tr>";
            }
        } 
		else
			{
            echo "<tr><td colspan='7'>No rooms found.</td></tr>";
        }
        ?>
    </table>
</div>

<div id="roomModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle" style="margin-bottom: 15px; color: #333;">Add New Room</h3>
        <form method="POST" id="modalForm">
            <input type="hidden" name="room_id" id="room_id">
            
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" name="room_no" id="room_no" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" id="category" required>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Deluxe Suite">Deluxe Suite</option>
                </select>
            </div>
            <div class="form-group">
                <label>Price per Night (₹)</label>
                <input type="number" step="0.01" name="price" id="price" required>
            </div>
            <div class="form-group" id="occupancyGroup" style="display:none;">
                <label>Occupancy Status</label>
                <select name="occupancy_status" id="occupancy_status">
                    <option value="Vacant">Vacant (Available)</option>
                    <option value="Occupied">Occupied</option>
                </select>
            </div>
            <div class="form-group" id="cleanGroup" style="display:none;">
                <label>Cleanliness Status</label>
                <select name="cleanliness_status" id="cleanliness_status">
                    <option value="Clean">Clean</option>
                    <option value="Dirty">Dirty</option>
                </select>
            </div>
            
            <button type="submit" name="add_room" id="submitBtn" class="btn-open-modal" style="width:100%; margin-top:10px;">Save Room</button>
        </form>
    </div>
</div>

<script>
    function openAddModal()
	{
        document.getElementById('modalTitle').innerText = "Add New Room";
        document.getElementById('modalForm').reset();
        document.getElementById('room_id').value = "";
        document.getElementById('occupancyGroup').style.display = "none";
        document.getElementById('cleanGroup').style.display = "none";
        document.getElementById('submitBtn').name = "add_room";
        document.getElementById('submitBtn').innerText = "Add Room";
        document.getElementById('roomModal').style.display = "flex";
    }

    function openEditModal(id, roomNo, category, price, occupancy, cleanliness) 
	{
        document.getElementById('modalTitle').innerText = "Edit Room & Status";
        document.getElementById('room_id').value = id;
        document.getElementById('room_no').value = roomNo;
        document.getElementById('category').value = category;
        document.getElementById('price').value = price;
        document.getElementById('occupancy_status').value = occupancy;
        document.getElementById('cleanliness_status').value = cleanliness;
        
        document.getElementById('occupancyGroup').style.display = "block";
        document.getElementById('cleanGroup').style.display = "block";
        document.getElementById('submitBtn').name = "update_room";
        document.getElementById('submitBtn').innerText = "Update Room";
        document.getElementById('roomModal').style.display = "flex";
    }

    function closeModal() 
	{
        document.getElementById('roomModal').style.display = "none";
    }
</script>

<?php include('footer.php'); ?>