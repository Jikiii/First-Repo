<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['delete'])) {
    $inspection_id = $_GET['delete'];
    $sql = "DELETE FROM Inspection WHERE InspectionID = :inspection_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':inspection_id', $inspection_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inspection deleted successfully!</div>";
    }
}

$sql = "SELECT i.*, p.StreetName, p.City, p.PropertyType,
        CONCAT(s.FName, ' ', s.LName) as inspector_name
        FROM Inspection i 
        JOIN Property p ON i.PropertyNo = p.PropertyNo
        JOIN Staff s ON i.StaffNo = s.StaffNo
        ORDER BY i.InspectDate DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$inspections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Property Inspections</h2>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Inspection</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>Address</th>
                        <th>Inspection Date</th>
                        <th>Inspector</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($inspections as $inspection): ?>
                    <tr>
                        <td><?php echo $inspection['InspectionID']; ?></td>
                        <td><?php echo $inspection['PropertyType']; ?></td>
                        <td><?php echo $inspection['StreetName'] . ', ' . $inspection['City']; ?></td>
                        <td><?php echo formatDate($inspection['InspectDate']); ?></td>
                        <td><?php echo $inspection['inspector_name']; ?></td>
                        <td><?php echo substr($inspection['Notes'], 0, 50) . (strlen($inspection['Notes']) > 50 ? '...' : ''); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?id=<?php echo $inspection['InspectionID']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?id=<?php echo $inspection['InspectionID']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this inspection?')) location.href='index.php?delete=<?php echo $inspection['InspectionID']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>