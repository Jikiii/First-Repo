<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Fetch properties with their RentAmount
$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType, RentAmount FROM Property ORDER BY PropertyNo";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $media_source = $_POST['media_source'];
    $publish_date = $_POST['publish_date'];
    
    $sql = "INSERT INTO Advertisement (PropertyNo, MediaSource, PublishDate) 
            VALUES (:property_no, :media_source, :publish_date)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':media_source', $media_source);
    $stmt->bindParam(':publish_date', $publish_date);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Advertisement added successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding advertisement!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Add New Advertisement</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property *</label>
                    <select class="form-select" name="property_no" id="propertySelect" required onchange="updateRent()">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['PropertyNo']; ?>" 
                                data-rent="<?php echo number_format($property['RentAmount'], 2); ?>">
                            <?php echo $property['PropertyNo'] . ' - ' . $property['PropertyType'] . ' - ' . $property['StreetName'] . ', ' . $property['City']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Monthly Rent (£)</label>
                    <input type="text" id="rentAmount" class="form-control" readonly placeholder="Select a property to see rent">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Media Source (Newspaper/Website) *</label>
                    <input type="text" class="form-control" name="media_source" required placeholder="e.g., Glasgow Times, Daily Record, Online Portal">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Publish Date *</label>
                    <input type="date" class="form-control" name="publish_date" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Advertisement</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
function updateRent() {
    const select = document.getElementById('propertySelect');
    const rentInput = document.getElementById('rentAmount');
    const selectedOption = select.options[select.selectedIndex];
    rentInput.value = selectedOption.getAttribute('data-rent') || '';
}
</script>

<?php include '../../includes/footer.php'; ?>