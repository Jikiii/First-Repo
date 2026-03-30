<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$staff_no = $_GET['staff_no'];
$sql = "SELECT s.*, b.city as branch_city, b.street as branch_street,
        CONCAT(super.first_name, ' ', super.last_name) as supervisor_name,
        n.full_name as kin_name, n.relationship as kin_relationship,
        n.address as kin_address, n.telephone as kin_telephone
        FROM Staff s 
        LEFT JOIN Branch b ON s.branch_no = b.branch_no 
        LEFT JOIN Staff super ON s.supervisor_no = super.staff_no
        LEFT JOIN NextOfKin n ON s.staff_no = n.staff_no
        WHERE s.staff_no = :staff_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':staff_no', $staff_no);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "<div class='alert alert-danger'>Staff member not found!</div>";
    include '../../includes/footer.php';
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Staff Details: <?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Personal Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Staff Number</th><td><?php echo $staff['staff_no']; ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($staff['address']); ?></td></tr>
                    <tr><th>Telephone</th><td><?php echo $staff['telephone']; ?></td></tr>
                    <tr><th>Sex</th><td><?php echo $staff['sex'] == 'M' ? 'Male' : 'Female'; ?></td></tr>
                    <tr><th>Date of Birth</th><td><?php echo $staff['date_of_birth']; ?></td></tr>
                    <tr><th>National Insurance No</th><td><?php echo $staff['national_insurance_no']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Employment Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Job Title</th><td><?php echo $staff['job_title']; ?></td></tr>
                    <tr><th>Salary</th><td>£<?php echo number_format($staff['salary'], 2); ?></td></tr>
                    <tr><th>Date Joined</th><td><?php echo $staff['date_joined']; ?></td></tr>
                    <tr><th>Branch</th><td><?php echo $staff['branch_city']; ?><br><?php echo $staff['branch_street']; ?></td></tr>
                    <tr><th>Supervisor</th><td><?php echo $staff['supervisor_name'] ?: 'None'; ?></td></tr>
                    <?php if ($staff['job_title'] == 'Manager'): ?>
                    <tr><th>Car Allowance</th><td>£<?php echo number_format($staff['car_allowance'], 2); ?></td></tr>
                    <tr><th>Monthly Bonus</th><td>£<?php echo number_format($staff['monthly_bonus'], 2); ?></td></tr>
                    <tr><th>Manager Since</th><td><?php echo $staff['manager_start_date']; ?></td></tr>
                    <?php endif; ?>
                    <?php if ($staff['job_title'] == 'Secretary'): ?>
                    <tr><th>Typing Speed</th><td><?php echo $staff['typing_speed']; ?> wpm</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <?php if ($staff['kin_name']): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Next of Kin Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Full Name</th><td><?php echo $staff['kin_name']; ?></td></tr>
                    <tr><th>Relationship</th><td><?php echo $staff['kin_relationship']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($staff['kin_address']); ?></td></tr>
                    <tr><th>Telephone</th><td><?php echo $staff['kin_telephone']; ?></td></tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?staff_no=<?php echo $staff['staff_no']; ?>" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>