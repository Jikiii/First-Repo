<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$viewing_id = $_GET['id'];

$sql = "SELECT v.ViewingID, v.PropertyNo, v.RenterNo, v.ViewDate, v.Remarks,
        p.StreetName as property_street, p.District as property_district, p.City as property_city, 
        p.PostCode as property_postcode, p.PropertyType as property_type, p.Rooms, p.RentAmount,
        CONCAT(r.FName, ' ', r.LName) as renter_name,
        r.Address as renter_address, r.Phone as renter_phone,
        r.PreferredType, r.MaxBudget
        FROM Viewing v
        JOIN Property p ON v.PropertyNo = p.PropertyNo
        JOIN Renter r ON v.RenterNo = r.RenterNo
        WHERE v.ViewingID = :viewing_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':viewing_id', $viewing_id);
$stmt->execute();
$viewing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viewing) {
    echo "<div class='alert alert-danger'>Viewing record not found!</div>";
    include '../../includes/footer.php';
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Viewing Details</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Property Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Property Number</th><td><?php echo $viewing['PropertyNo']; ?></td></tr>
                    <tr><th>Property Type</th><td><?php echo $viewing['property_type']; ?></td></tr>
                    <tr><th>Address</th><td>
                        <?php echo $viewing['property_street']; ?><br>
                        <?php if ($viewing['property_district']): echo $viewing['property_district'] . '<br>'; endif; ?>
                        <?php echo $viewing['property_city']; ?><br>
                        <?php echo $viewing['property_postcode']; ?>
                    </td></tr>
                    <tr><th>Number of Rooms</th><td><?php echo $viewing['Rooms']; ?></td></tr>
                    <tr><th>Monthly Rent</th><td><?php echo formatMoney($viewing['RentAmount']); ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Renter Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Name</th><td><?php echo $viewing['renter_name']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($viewing['renter_address']); ?></td></tr>
                    <tr><th>Telephone</th><td><?php echo $viewing['renter_phone']; ?></td></tr>
                    <tr><th>Preferred Type</th><td><?php echo $viewing['PreferredType'] ?: 'Any'; ?></td></tr>
                    <tr><th>Max Budget</th><td><?php echo formatMoney($viewing['MaxBudget']); ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h4>Viewing Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Viewing Date</th><td><?php echo formatDate($viewing['ViewDate']); ?></td></tr>
                    <tr><th>Remarks / Feedback</th><td><?php echo nl2br($viewing['Remarks']) ?: 'No remarks recorded.'; ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?id=<?php echo $viewing['ViewingID']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/view.php?property_no=<?php echo $viewing['PropertyNo']; ?>" class="btn btn-info">View Property</a>
            <a href="../renters/view.php?renter_no=<?php echo $viewing['RenterNo']; ?>" class="btn btn-info">View Renter</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>