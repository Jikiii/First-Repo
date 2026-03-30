<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* Get all supervisors */
$supervisor_sql = "SELECT StaffNo, FName, LName, BranchNo
                   FROM Staff
                   WHERE JobTitle = 'Supervisor'
                   ORDER BY LName";

$supervisor_stmt = $db->prepare($supervisor_sql);
$supervisor_stmt->execute();
$supervisors = $supervisor_stmt->fetchAll(PDO::FETCH_ASSOC);

$staff_list = [];

if (isset($_GET['supervisor_no']) && !empty($_GET['supervisor_no'])) {
    $supervisor_no = $_GET['supervisor_no'];

    /* Get selected supervisor branch */
    $branch_sql = "SELECT BranchNo
                   FROM Staff
                   WHERE StaffNo = :supervisor_no";

    $branch_stmt = $db->prepare($branch_sql);
    $branch_stmt->bindParam(':supervisor_no', $supervisor_no);
    $branch_stmt->execute();

    $branch = $branch_stmt->fetch(PDO::FETCH_ASSOC);

    if ($branch) {
        $sql = "SELECT StaffNo, FName, LName, JobTitle
                FROM Staff
                WHERE BranchNo = :branch_no
                ORDER BY LName";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':branch_no', $branch['BranchNo']);
        $stmt->execute();

        $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<form method="GET">
    <label>Select Supervisor</label>
    <select name="supervisor_no" class="form-select">
        <option value="">Choose Supervisor</option>

        <?php foreach ($supervisors as $sup): ?>
            <option value="<?php echo $sup['StaffNo']; ?>">
                <?php echo $sup['FName'] . ' ' . $sup['LName']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary mt-2">
        Show Staff
    </button>
</form>

<?php if (!empty($staff_list)): ?>
<table class="table mt-3">
    <tr>
        <th>Staff No</th>
        <th>Name</th>
        <th>Job Title</th>
    </tr>

    <?php foreach ($staff_list as $staff): ?>
    <tr>
        <td><?php echo $staff['StaffNo']; ?></td>
        <td>
            <?php echo $staff['FName'] . ' ' . $staff['LName']; ?>
        </td>
        <td><?php echo $staff['JobTitle']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">
    Back to Staff List
</a>