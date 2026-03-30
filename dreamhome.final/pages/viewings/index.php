<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['delete'])) {
    $viewing_id = $_GET['delete'];
    $sql = "DELETE FROM Viewing WHERE ViewingID = :viewing_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':viewing_id', $viewing_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Viewing record deleted successfully!</div>";
    }
}

$sql = "SELECT v.ViewingID, v.PropertyNo, v.RenterNo, v.ViewDate, v.Remarks,
        p.StreetName as property_street, p.City as property_city, p.PropertyType as property_type,
        CONCAT(r.FName, ' ', r.LName) as renter_name
        FROM Viewing v
        LEFT JOIN Property p ON v.PropertyNo = p.PropertyNo
        LEFT JOIN Renter r ON v.RenterNo = r.RenterNo
        ORDER BY v.ViewDate DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$viewings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Property Viewings</h2>
        <div>
            <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Viewing</button>
            <button class="btn btn-light" onclick="location.href='v.report.php'"> View Report</button>
        </div>

    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Viewing ID</th>
                        <th>Property</th>
                        <th>Renter</th>
                        <th>View Date</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($viewings as $viewing): ?>
                    <tr>
                        <td><?php echo $viewing['ViewingID']; ?></td>
                        <td>
                            <strong><?php echo $viewing['PropertyNo']; ?></strong><br>
                            <small class="text-muted"><?php echo $viewing['property_type'] . ' - ' . $viewing['property_street']; ?></small>
                        </td>
                        <td><?php echo $viewing['renter_name']; ?></td>
                        <td><?php echo formatDate($viewing['ViewDate']); ?></td>
                        <td><?php echo substr($viewing['Remarks'], 0, 50) . (strlen($viewing['Remarks']) > 50 ? '...' : ''); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?id=<?php echo $viewing['ViewingID']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?id=<?php echo $viewing['ViewingID']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this viewing record?')) location.href='index.php?delete=<?php echo $viewing['ViewingID']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>