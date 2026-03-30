<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$inspection_id = $_GET['id'];
$sql = "SELECT i.*, p.street, p.area, p.city, p.postcode, p.type as property_type,
        CONCAT(s.first_name, ' ', s.last_name) as inspector_name
        FROM PropertyInspection i 
        JOIN Property p ON i.property_no = p.property_no
        JOIN Staff s ON i.staff_no = s.staff_no
        WHERE i.inspection_id = :inspection_id";
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
                    <tr><th width="200">Property Number</th><td><?php echo $inspection['property_no']; ?></td></tr>
                    <tr><th>Property Type</th><td><?php echo $inspection['property_type']; ?></td></tr>
                    <tr><th>Address</th><td>
                        <?php echo $inspection['street']; ?><br>
                        <?php echo $inspection['area']; ?><br>
                        <?php echo $inspection['city']; ?><br>
                        <?php echo $inspection['postcode']; ?>
                    </td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Inspection Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Inspection ID</th><td><?php echo $inspection['inspection_id']; ?></td></tr>
                    <tr><th>Inspection Date</th><td><?php echo $inspection['inspection_date']; ?></td></tr>
                    <tr><th>Inspector</th><td><?php echo $inspection['inspector_name']; ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h4>Inspection Comments</h4>
                <div class="card">
                    <div class="card-body">
                        <?php echo nl2br($inspection['comments']); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?id=<?php echo $inspection['inspection_id']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/view.php?property_no=<?php echo $inspection['property_no']; ?>" class="btn btn-info">View Property</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>