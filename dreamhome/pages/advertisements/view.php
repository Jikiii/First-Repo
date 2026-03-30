<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$ad_id = $_GET['id'];

$sql = "SELECT a.*, p.PropertyNo, p.StreetName, p.District, p.City, p.PostCode, 
               p.PropertyType, p.Rooms, p.RentAmount
        FROM Advertisement a
        JOIN Property p ON a.PropertyNo = p.PropertyNo
        WHERE a.AdID = :ad_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':ad_id', $ad_id);
$stmt->execute();
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    echo "<div class='alert alert-danger'>Advertisement not found!</div>";
    include '../../includes/footer.php';
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Advertisement Details</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Property Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Property Number</th><td><?php echo $ad['PropertyNo']; ?></td></tr>
                    <tr><th>Property Type</th><td><?php echo $ad['PropertyType']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo $ad['StreetName'] . ', ' . $ad['City'] . ', ' . $ad['PostCode']; ?></td></tr>
                    <tr><th>Rooms</th><td><?php echo $ad['Rooms']; ?></td></tr>
                    <tr><th>Monthly Rent</th><td>£<?php echo number_format($ad['RentAmount'], 2); ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Advertisement Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Ad ID</th><td><?php echo $ad['AdID']; ?></td></tr>
                    <tr><th>Media Source</th><td><?php echo $ad['MediaSource']; ?></td></tr>
                    <tr><th>Publish Date</th><td><?php echo date("d M Y", strtotime($ad['PublishDate'])); ?></td></tr>
                    <tr><th>Monthly Rent</th><td>£<?php echo number_format($ad['RentAmount'], 2); ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?id=<?php echo $ad['AdID']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/view.php?property_no=<?php echo $ad['PropertyNo']; ?>" class="btn btn-info">View Property</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>