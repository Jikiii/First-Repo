<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$inspection_id = $_GET['id'];

$sql = "SELECT i.*, p.StreetName, p.District, p.City, p.PostCode, p.PropertyType,
        CONCAT(s.FName, ' ', s.LName) as inspector_name
        FROM Inspection i 
        JOIN Property p ON i.PropertyNo = p.PropertyNo
        JOIN Staff s ON i.StaffNo = s.StaffNo
        WHERE i.InspectionID = :inspection_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':inspection_id', $inspection_id);
$stmt->execute();
$inspection = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inspection) {
    echo "<div class='alert alert-danger'>Inspection record not found!</div>";
    include '../../includes/footer.php';
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Inspection Details</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Property Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Property Number</th><td><?php echo $inspection['PropertyNo']; ?></td></tr>
                    <tr><th>Property Type</th><td><?php echo $inspection['PropertyType']; ?></td></tr>
                    <tr><th>Address</th><td>
                        <?php echo $inspection['StreetName']; ?><br>
                        <?php echo $inspection['District']; ?><br>
                        <?php echo $inspection['City']; ?><br>
                        <?php echo $inspection['PostCode']; ?>
                    </td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Inspection Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Inspection ID</th><td><?php echo $inspection['InspectionID']; ?></td></tr>
                    <tr><th>Inspection Date</th><td><?php echo formatDate($inspection['InspectDate']); ?></td></tr>
                    <tr><th>Inspector</th><td><?php echo $inspection['inspector_name']; ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h4>Inspection Notes</h4>
                <div class="card">
                    <div class="card-body">
                        <?php echo nl2br($inspection['Notes']); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?id=<?php echo $inspection['InspectionID']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/view.php?property_no=<?php echo $inspection['PropertyNo']; ?>" class="btn btn-info">View Property</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>