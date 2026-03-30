<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get branches for dropdown
$branch_sql = "SELECT branch_no, city FROM Branch";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $type = $_POST['type'];
    $rooms = $_POST['rooms'];
    $monthly_rent = $_POST['monthly_rent'];
    $staff_no = $_POST['staff_no'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "INSERT INTO Property (property_no, street, area, city, postcode, type, rooms, monthly_rent, staff_no, branch_no, status) 
            VALUES (:property_no, :street, :area, :city, :postcode, :type, :rooms, :monthly_rent, :staff_no, :branch_no, 'available')";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':rooms', $rooms);
    $stmt->bindParam(':monthly_rent', $monthly_rent);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':branch_no', $branch_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Property added successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding property!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Add New Property</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property Number *</label>
                    <input type="text" class="form-control" name="property_no" required maxlength="5">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type *</label>
                    <select class="form-select" name="type" required>
                        <option value="Flat">Flat</option>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Studio">Studio</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Rooms *</label>
                    <input type="number" class="form-control" name="rooms" required min="1" max="20">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="monthly_rent" required step="0.01" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Managing Staff *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $s): ?>
                        <option value="<?php echo $s['staff_no']; ?>"><?php echo $s['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $b): ?>
                        <option value="<?php echo $b['branch_no']; ?>"><?php echo $b['city']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Property</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>