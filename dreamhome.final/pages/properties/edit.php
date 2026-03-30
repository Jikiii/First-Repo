<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$property_no = $_GET['property_no'];

$sql = "SELECT * FROM Property WHERE PropertyNo = :property_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':property_no', $property_no);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "<div class='alert alert-danger'>Property not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$branch_sql = "SELECT BranchNo, BranchName FROM Branch";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $street_name = $_POST['street_name'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $property_type = $_POST['property_type'];
    $rooms = $_POST['rooms'];
    $rent_amount = $_POST['rent_amount'];
    $staff_no = $_POST['staff_no'];
    $branch_no = $_POST['branch_no'];
    $status = $_POST['status'];
    $date_available = $_POST['date_available'];
    
    $sql = "UPDATE Property SET StreetName=:street_name, District=:district, City=:city, 
            PostCode=:postcode, PropertyType=:property_type, Rooms=:rooms, RentAmount=:rent_amount, 
            StaffNo=:staff_no, BranchNo=:branch_no, Status=:status, DateAvailable=:date_available 
            WHERE PropertyNo=:property_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':street_name', $street_name);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':property_type', $property_type);
    $stmt->bindParam(':rooms', $rooms);
    $stmt->bindParam(':rent_amount', $rent_amount);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':date_available', $date_available);
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
        <h2 class="mb-0">Edit Property: <?php echo $property['PropertyNo']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property Number</label>
                    <input type="text" class="form-control" value="<?php echo $property['PropertyNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property Type *</label>
                    <select class="form-select" name="property_type" required>
                        <option value="Flat" <?php echo $property['PropertyType'] == 'Flat' ? 'selected' : ''; ?>>Flat</option>
                        <option value="House" <?php echo $property['PropertyType'] == 'House' ? 'selected' : ''; ?>>House</option>
                        <option value="Apartment" <?php echo $property['PropertyType'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="Studio" <?php echo $property['PropertyType'] == 'Studio' ? 'selected' : ''; ?>>Studio</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street Name *</label>
                    <input type="text" class="form-control" name="street_name" value="<?php echo $property['StreetName']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">District</label>
                    <input type="text" class="form-control" name="district" value="<?php echo $property['District']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" value="<?php echo $property['City']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" value="<?php echo $property['PostCode']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Number of Rooms *</label>
                    <input type="number" class="form-control" name="rooms" value="<?php echo $property['Rooms']; ?>" required min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="rent_amount" value="<?php echo $property['RentAmount']; ?>" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Managing Staff *</label>
                    <select class="form-select" name="staff_no" required>
                        <?php foreach ($staff as $s): ?>
                        <option value="<?php echo $s['StaffNo']; ?>" <?php echo $property['StaffNo'] == $s['StaffNo'] ? 'selected' : ''; ?>>
                            <?php echo $s['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <?php foreach ($branches as $b): ?>
                        <option value="<?php echo $b['BranchNo']; ?>" <?php echo $property['BranchNo'] == $b['BranchNo'] ? 'selected' : ''; ?>>
                            <?php echo $b['BranchName']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select class="form-select" name="status" required>
                        <option value="Available" <?php echo $property['Status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                        <option value="Rented" <?php echo $property['Status'] == 'Rented' ? 'selected' : ''; ?>>Rented</option>
                        <option value="Withdrawn" <?php echo $property['Status'] == 'Withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date Available</label>
                    <input type="date" class="form-control" name="date_available" value="<?php echo $property['DateAvailable']; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Property</button>
            <a href="view.php?property_no=<?php echo $property['PropertyNo']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>