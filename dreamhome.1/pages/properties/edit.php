<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$property_no = $_GET['property_no'];

// Get property details
$sql = "SELECT * FROM Property WHERE property_no = :property_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':property_no', $property_no);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "<div class='alert alert-danger'>Property not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get branches for dropdown
$branch_sql = "SELECT branch_no, city FROM Branch ORDER BY city";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $type = $_POST['type'];
    $rooms = $_POST['rooms'];
    $monthly_rent = $_POST['monthly_rent'];
    $staff_no = $_POST['staff_no'];
    $branch_no = $_POST['branch_no'];
    $status = $_POST['status'];
    $date_withdrawn = ($status == 'withdrawn' && $_POST['date_withdrawn']) ? $_POST['date_withdrawn'] : null;
    
    $sql = "UPDATE Property SET street=:street, area=:area, city=:city, postcode=:postcode, 
            type=:type, rooms=:rooms, monthly_rent=:monthly_rent, staff_no=:staff_no, 
            branch_no=:branch_no, status=:status, date_withdrawn=:date_withdrawn 
            WHERE property_no=:property_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':rooms', $rooms);
    $stmt->bindParam(':monthly_rent', $monthly_rent);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':date_withdrawn', $date_withdrawn);
    $stmt->bindParam(':property_no', $property_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Property updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?property_no=$property_no'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating property!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Property: <?php echo $property['property_no']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property Number</label>
                    <input type="text" class="form-control" value="<?php echo $property['property_no']; ?>" readonly disabled>
                    <small class="text-muted">Property number cannot be changed</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property Type *</label>
                    <select class="form-select" name="type" required>
                        <option value="Flat" <?php echo $property['type'] == 'Flat' ? 'selected' : ''; ?>>Flat</option>
                        <option value="House" <?php echo $property['type'] == 'House' ? 'selected' : ''; ?>>House</option>
                        <option value="Apartment" <?php echo $property['type'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="Studio" <?php echo $property['type'] == 'Studio' ? 'selected' : ''; ?>>Studio</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" value="<?php echo $property['street']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area" value="<?php echo $property['area']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" value="<?php echo $property['city']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" value="<?php echo $property['postcode']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Rooms *</label>
                    <input type="number" class="form-control" name="rooms" value="<?php echo $property['rooms']; ?>" required min="1" max="20">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="monthly_rent" value="<?php echo $property['monthly_rent']; ?>" required step="0.01" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Managing Staff *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>" <?php echo $property['staff_no'] == $member['staff_no'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['branch_no']; ?>" <?php echo $property['branch_no'] == $branch['branch_no'] ? 'selected' : ''; ?>>
                            <?php echo $branch['city']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status *</label>
                    <select class="form-select" name="status" id="statusSelect" required>
                        <option value="available" <?php echo $property['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="rented" <?php echo $property['status'] == 'rented' ? 'selected' : ''; ?>>Rented</option>
                        <option value="withdrawn" <?php echo $property['status'] == 'withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="withdrawnDateDiv" style="display: <?php echo $property['status'] == 'withdrawn' ? 'block' : 'none'; ?>">
                    <label class="form-label">Date Withdrawn</label>
                    <input type="date" class="form-control" name="date_withdrawn" value="<?php echo $property['date_withdrawn']; ?>">
                    <small class="text-muted">Required if status is Withdrawn</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Property</button>
            <a href="view.php?property_no=<?php echo $property['property_no']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
// Show/hide withdrawn date field based on status
document.getElementById('statusSelect').addEventListener('change', function() {
    const withdrawnDiv = document.getElementById('withdrawnDateDiv');
    if (this.value === 'withdrawn') {
        withdrawnDiv.style.display = 'block';
    } else {
        withdrawnDiv.style.display = 'none';
    }
});
</script>

<?php include '../../includes/footer.php'; ?>