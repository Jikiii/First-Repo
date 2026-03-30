<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType FROM Property ORDER BY PropertyNo";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

$renter_sql = "SELECT RenterNo, CONCAT(FName, ' ', LName) as name FROM Renter ORDER BY FName";
$renter_stmt = $db->prepare($renter_sql);
$renter_stmt->execute();
$renters = $renter_stmt->fetchAll(PDO::FETCH_ASSOC);

$preset_property = isset($_GET['property_no']) ? $_GET['property_no'] : '';
$preset_renter = isset($_GET['renter_no']) ? $_GET['renter_no'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $renter_no = $_POST['renter_no'];
    $view_date = $_POST['view_date'];
    $remarks = $_POST['remarks'];
    
    $sql = "INSERT INTO Viewing (PropertyNo, RenterNo, ViewDate, Remarks) 
            VALUES (:property_no, :renter_no, :view_date, :remarks)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':renter_no', $renter_no);
    $stmt->bindParam(':view_date', $view_date);
    $stmt->bindParam(':remarks', $remarks);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Viewing recorded successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error recording viewing!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Record New Property Viewing</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property *</label>
                    <select class="form-select" name="property_no" required>
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['PropertyNo']; ?>" <?php echo $preset_property == $property['PropertyNo'] ? 'selected' : ''; ?>>
                            <?php echo $property['PropertyNo'] . ' - ' . $property['PropertyType'] . ' - ' . $property['StreetName'] . ', ' . $property['City']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Renter *</label>
                    <select class="form-select" name="renter_no" required>
                        <option value="">Select Renter</option>
                        <?php foreach ($renters as $renter): ?>
                        <option value="<?php echo $renter['RenterNo']; ?>" <?php echo $preset_renter == $renter['RenterNo'] ? 'selected' : ''; ?>>
                            <?php echo $renter['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Viewing Date *</label>
                    <input type="date" class="form-control" name="view_date" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks / Feedback</label>
                    <textarea class="form-control" name="remarks" rows="4" placeholder="Enter renter's feedback, observations, or comments about the property..."></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Viewing Record</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>