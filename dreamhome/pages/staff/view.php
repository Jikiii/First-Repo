<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$staff_no = $_GET['staff_no'];

$sql = "SELECT s.*, b.BranchName as branch_name, b.City as branch_city
        FROM Staff s
        LEFT JOIN Branch b ON s.BranchNo = b.BranchNo
        WHERE s.StaffNo = :staff_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':staff_no', $staff_no);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "<div class='alert alert-danger'>Staff member not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get next of kin
$kin_sql = "SELECT * FROM NextOfKin WHERE StaffNo = :staff_no";
$kin_stmt = $db->prepare($kin_sql);
$kin_stmt->bindParam(':staff_no', $staff_no);
$kin_stmt->execute();
$next_of_kin = $kin_stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Staff Details: <?php echo $staff['FName'] . ' ' . $staff['LName']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Personal Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Staff Number</th><td><?php echo $staff['StaffNo']; ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo $staff['FName'] . ' ' . $staff['LName']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($staff['Address']); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo $staff['Phone']; ?></td></tr>
                    <tr><th>Email</th><td><?php echo $staff['Email']; ?></td></tr>
                    <tr><th>Gender</th><td><?php echo $staff['Gender'] == 'M' ? 'Male' : 'Female'; ?></td></tr>
                    <tr><th>Date of Birth</th><td><?php echo formatDate($staff['BirthDate']); ?></td></tr>
                    <tr><th>National ID</th><td><?php echo $staff['NationalID']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Employment Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Job Title</th><td><?php echo $staff['JobTitle']; ?></td></tr>
                    <tr><th>Salary</th><td><?php echo formatMoney($staff['Salary']); ?></td></tr>
                    <tr><th>Hire Date</th><td><?php echo formatDate($staff['HireDate']); ?></td></tr>
                    <tr><th>Branch</th><td><?php echo $staff['branch_name'] . ' - ' . $staff['branch_city']; ?></td></tr>
                </table>
                
                <?php if ($next_of_kin): ?>
                <h4 class="mt-3">Next of Kin</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Name</th><td><?php echo $next_of_kin['KinName']; ?></td></tr>
                    <tr><th>Relationship</th><td><?php echo $next_of_kin['Relation']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($next_of_kin['Address']); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo $next_of_kin['Phone']; ?></td></tr>
                </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?staff_no=<?php echo $staff['StaffNo']; ?>" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>