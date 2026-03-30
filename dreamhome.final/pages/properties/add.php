<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$branch_sql = "SELECT BranchNo, BranchName FROM Branch";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $street_name = $_POST['street_name'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $property_type = $_POST['property_type'];
    $rooms = $_POST['rooms'];
    $rent_amount = $_POST['rent_amount'];
    $staff_no = $_POST['staff_no'];
    $branch_no = $_POST['branch_no'];
    $date_available = $_POST['date_available'];
    
    $sql = "INSERT INTO Property (PropertyNo, StreetName, District, City, PostCode, PropertyType, Rooms, RentAmount, StaffNo, BranchNo, Status, DateAvailable) 
            VALUES (:property_no, :street_name, :district, :city, :postcode, :property_type, :rooms, :rent_amount, :staff_no, :branch_no, 'Available', :date_available)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':street_name', $street_name);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':property_type', $property_type);
    $stmt->bindParam(':rooms', $rooms);
    $stmt->bindParam(':rent_amount', $rent_amount);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':date_available', $date_available);
    
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Property Number *</label>
                    <input type="text" class="form-control" name="property_no" required maxlength="10">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Property Type *</label>
                    <select class="form-select" name="property_type" required>
                        <option value="Flat">Flat</option>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Studio">Studio</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date Available</label>
                    <input type="date" class="form-control" name="date_available">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street Name *</label>
                    <input type="text" class="form-control" name="street_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">District</label>
                    <input type="text" class="form-control" name="district">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Number of Rooms *</label>
                    <input type="number" class="form-control" name="rooms" required min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="rent_amount" required step="0.01" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Managing Staff *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $s): ?>
                        <option value="<?php echo $s['StaffNo']; ?>"><?php echo $s['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $b): ?>
                        <option value="<?php echo $b['BranchNo']; ?>"><?php echo $b['BranchName']; ?></option>
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