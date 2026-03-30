<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$branch_no = $_GET['branch_no'];

$sql = "SELECT * FROM Branch WHERE BranchNo = :branch_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':branch_no', $branch_no);
$stmt->execute();
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$branch) {
    echo "<div class='alert alert-danger'>Branch not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$staff_sql = "SELECT StaffNo, FName, LName, JobTitle, Salary FROM Staff WHERE BranchNo = :branch_no";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->bindParam(':branch_no', $branch_no);
$staff_stmt->execute();
$staff_members = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType, RentAmount, Status FROM Property WHERE BranchNo = :branch_no";
$property_stmt = $db->prepare($property_sql);
$property_stmt->bindParam(':branch_no', $branch_no);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Branch Details: <?php echo $branch['BranchNo']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Branch Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Branch Number</th><td><?php echo $branch['BranchNo']; ?></td></tr>
                    <tr><th>Branch Name</th><td><?php echo $branch['BranchName']; ?></td></tr>
                    <tr><th>Street</th><td><?php echo $branch['Street']; ?></td></tr>
                    <tr><th>Area</th><td><?php echo $branch['Area']; ?></td></tr>
                    <tr><th>City</th><td><?php echo $branch['City']; ?></td></tr>
                    <tr><th>Postcode</th><td><?php echo $branch['PostCode']; ?></td></tr>
                    <tr><th>Contact No</th><td><?php echo $branch['ContactNo']; ?></td></tr>
                    <tr><th>Email</th><td><?php echo $branch['Email']; ?></td></tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Staff at this Branch</h4>
                <?php if (count($staff_members) > 0): ?>
                <table class="table table-bordered">
                    <thead><tr><th>Staff No</th><th>Name</th><th>Job Title</th><th>Salary</th></tr></thead>
                    <tbody>
                        <?php foreach ($staff_members as $staff): ?>
                        <tr>
                            <td><?php echo $staff['StaffNo']; ?></td>
                            <td><?php echo $staff['FName'] . ' ' . $staff['LName']; ?></td>
                            <td><?php echo $staff['JobTitle']; ?></td>
                            <td><?php echo formatMoney($staff['Salary']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No staff members at this branch.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h4>Properties at this Branch</h4>
                <?php if (count($properties) > 0): ?>
                <table class="table table-bordered">
                    <thead><tr><th>Property No</th><th>Address</th><th>Type</th><th>Rent</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?php echo $property['PropertyNo']; ?></td>
                            <td><?php echo $property['StreetName'] . ', ' . $property['City']; ?></td>
                            <td><?php echo $property['PropertyType']; ?></td>
                            <td><?php echo formatMoney($property['RentAmount']); ?></td>
                            <td><?php echo $property['Status']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No properties at this branch.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?branch_no=<?php echo $branch['BranchNo']; ?>" class="btn btn-warning">Edit Branch</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>