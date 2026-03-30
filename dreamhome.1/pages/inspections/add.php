<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get properties for dropdown
$property_sql = "SELECT property_no, street, city, type FROM Property WHERE status IN ('available', 'rented')";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

// Pre-select property if passed via GET
$preset_property = isset($_GET['property_no']) ? $_GET['property_no'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $staff_no = $_POST['staff_no'];
    $inspection_date = $_POST['inspection_date'];
    $comments = $_POST['comments'];
    
    $sql = "INSERT INTO PropertyInspection (property_no, staff_no, inspection_date, comments) 
            VALUES (:property_no, :staff_no, :inspection_date, :comments)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':inspection_date', $inspection_date);
    $stmt->bindParam(':comments', $comments);
    
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
                        <option value="<?php echo $property['property_no']; ?>" <?php echo $preset_property == $property['property_no'] ? 'selected' : ''; ?>>
                            <?php echo $property['type'] . ' - ' . $property['street'] . ', ' . $property['city']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspector (Staff) *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>"><?php echo $member['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspection Date *</label>
                    <input type="date" class="form-control" name="inspection_date" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Comments *</label>
                    <textarea class="form-control" name="comments" rows="4" required placeholder="Describe the condition of the property, any issues found, repairs needed, etc."></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Inspection</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>