<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['delete'])) {
    $staff_no = $_GET['delete'];
    $sql = "DELETE FROM Staff WHERE StaffNo = :staff_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $staff_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Staff member deleted successfully!</div>";
    }
}

$sql = "SELECT s.*, b.BranchName as branch_name
        FROM Staff s
        LEFT JOIN Branch b ON s.BranchNo = b.BranchNo
        ORDER BY s.StaffNo";
$stmt = $db->prepare($sql);
$stmt->execute();
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Staff Management</h2>
    <div>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Staff</button>
        <button class="btn btn-light me-2" onclick="location.href='by_branch.php'">By Branch</button>
        <button class="btn btn-light" onclick="location.href='by_supervisor.php'">By Supervisor</button>
        <button class="btn btn-light" onclick="location.href='supervisors_by_branch.php'">Supervisors by Branch</button>
    </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Staff No</th>
                        <th>Name</th>
                        <th>Job Title</th>
                        <th>Branch</th>
                        <th>Salary</th>
                        <th>Hire Date</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><?php echo $member['StaffNo']; ?></td>
                        <td><?php echo $member['FName'] . ' ' . $member['LName']; ?></td>
                        <td><?php echo $member['JobTitle']; ?></td>
                        <td><?php echo $member['branch_name']; ?></td>
                        <td><?php echo formatMoney($member['Salary']); ?></td>
                        <td><?php echo formatDate($member['HireDate']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?staff_no=<?php echo $member['StaffNo']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?staff_no=<?php echo $member['StaffNo']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this staff member?')) location.href='index.php?delete=<?php echo $member['StaffNo']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>