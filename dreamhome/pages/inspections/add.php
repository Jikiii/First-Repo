<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType FROM Property WHERE Status IN ('Available', 'Rented')";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff ORDER BY FName";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$preset_property = isset($_GET['property_no']) ? $_GET['property_no'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $staff_no = $_POST['staff_no'];
    $inspect_date = $_POST['inspect_date'];
    $notes = $_POST['notes'];
    
    $sql = "INSERT INTO Inspection (PropertyNo, StaffNo, InspectDate, Notes) 
            VALUES (:property_no, :staff_no, :inspect_date, :notes)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':inspect_date', $inspect_date);
    $stmt->bindParam(':notes', $notes);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inspection recorded successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error recording inspection!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Record Property Inspection</h2>
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
                    <label class="form-label">Inspector (Staff) *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['StaffNo']; ?>"><?php echo $member['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspection Date *</label>
                    <input type="date" class="form-control" name="inspect_date" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes *</label>
                    <textarea class="form-control" name="notes" rows="4" required placeholder="Describe the condition of the property, any issues found, repairs needed, etc."></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Inspection</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>