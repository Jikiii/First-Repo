<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Fetch all properties for dropdown
$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType FROM Property ORDER BY PropertyNo";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$all_properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected property_no if submitted
$property_no = isset($_GET['property_no']) ? $_GET['property_no'] : null;

if ($property_no) {
    // Fetch property info
    $property_info_sql = "SELECT PropertyNo, StreetName, City, PropertyType, Rooms 
                          FROM Property 
                          WHERE PropertyNo = :property_no";
    $property_stmt = $db->prepare($property_info_sql);
    $property_stmt->bindParam(':property_no', $property_no);
    $property_stmt->execute();
    $property = $property_stmt->fetch(PDO::FETCH_ASSOC);

    if ($property) {
        // Fetch inspections for the property
        $inspections_sql = "SELECT i.*, CONCAT(s.FName, ' ', s.LName) as inspector_name
                            FROM Inspection i
                            JOIN Staff s ON i.StaffNo = s.StaffNo
                            WHERE i.PropertyNo = :property_no
                            ORDER BY i.InspectDate DESC";
        $inspections_stmt = $db->prepare($inspections_sql);
        $inspections_stmt->bindParam(':property_no', $property_no);
        $inspections_stmt->execute();
        $inspections = $inspections_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $property_no = null; // invalid property
    }
}
?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="">
            <label for="property_no" class="form-label">Select Property</label>
            <select class="form-select" name="property_no" id="property_no" onchange="this.form.submit()">
                <option value="">-- Choose Property --</option>
                <?php foreach ($all_properties as $prop): ?>
                    <option value="<?php echo $prop['PropertyNo']; ?>" <?php echo ($prop['PropertyNo'] == $property_no) ? 'selected' : ''; ?>>
                        <?php echo $prop['PropertyNo'] . ' - ' . $prop['PropertyType'] . ' - ' . $prop['StreetName'] . ', ' . $prop['City']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php if ($property_no && $property): ?>
<div class="card">
    <div class="card-header">
        <h2>Inspections for Property <?php echo $property['PropertyNo']; ?></h2>
        <p><?php echo $property['PropertyType'] . ' - ' . $property['StreetName'] . ', ' . $property['City']; ?></p>
    </div>
    <div class="card-body">
        <?php if (!empty($inspections)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Inspection ID</th>
                    <th>Date</th>
                    <th>Inspector</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inspections as $inspection): ?>
                <tr>
                    <td><?php echo $inspection['InspectionID']; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($inspection['InspectDate'])); ?></td>
                    <td><?php echo $inspection['inspector_name']; ?></td>
                    <td><?php echo substr($inspection['Notes'], 0, 50) . (strlen($inspection['Notes']) > 50 ? '...' : ''); ?></td>
                    <td>
                        <a href="view.php?id=<?php echo $inspection['InspectionID']; ?>" class="btn btn-info btn-sm">View</a>
                        <a href="edit.php?id=<?php echo $inspection['InspectionID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?id=<?php echo $inspection['InspectionID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this inspection?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info">No inspections found for this property.</div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Inspections</a>
    </div>
</div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>