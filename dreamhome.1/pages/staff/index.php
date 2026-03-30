<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle Delete
if (isset($_GET['delete'])) {
    $staff_no = $_GET['delete'];
    $sql = "DELETE FROM Staff WHERE staff_no = :staff_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $staff_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Staff member deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting staff member!</div>";
    }
}

$sql = "SELECT s.*, b.city as branch_city, 
        CONCAT(super.first_name, ' ', super.last_name) as supervisor_name
        FROM Staff s 
        LEFT JOIN Branch b ON s.branch_no = b.branch_no 
        LEFT JOIN Staff super ON s.supervisor_no = super.staff_no
        ORDER BY s.staff_no";
$stmt = $db->prepare($sql);
$stmt->execute();
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card-header d-flex justify-content-between align-items-center">
    <h2 class="mb-0">Staff Management</h2>
    <div>
        <button class="btn btn-info me-2" onclick="location.href='by_branch.php'">By Branch</button>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Staff</button>
        <button class="btn btn-info" onclick="location.href='by_supervisor.php'">By Supervisor</button>
        <button class="btn btn-info" onclick="location.href='supervisors_by_branch.php'">Supervisors by Branch</button>
    </div>
</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="staffTable">
                <thead>
                    <tr>
                        <th>Staff No</th>
                        <th>Name</th>
                        <th>Job Title</th>
                        <th>Branch</th>
                        <th>Supervisor</th>
                        <th>Salary</th>
                        <th>Date Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><?php echo $member['staff_no']; ?></td>
                        <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                        <td><?php echo $member['job_title']; ?></td>
                        <td><?php echo $member['branch_city']; ?></td>
                        <td><?php echo $member['supervisor_name'] ?: '-'; ?></td>
                        <td>£<?php echo number_format($member['salary'], 2); ?></td>
                        <td><?php echo $member['date_joined']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewStaff('<?php echo $member['staff_no']; ?>')">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editStaff('<?php echo $member['staff_no']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteStaff('<?php echo $member['staff_no']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewStaff(staff_no) { window.location.href = `view.php?staff_no=${staff_no}`; }
function editStaff(staff_no) { window.location.href = `edit.php?staff_no=${staff_no}`; }
function deleteStaff(staff_no) {
    if (confirm('Are you sure you want to delete this staff member?')) {
        window.location.href = `index.php?delete=${staff_no}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>