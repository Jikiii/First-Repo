<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
?>
<?php
$supervisor_sql = "SELECT staff_no, first_name, last_name 
                   FROM Staff 
                   WHERE job_title = 'Supervisor'
                   ORDER BY last_name";

$supervisor_stmt = $db->prepare($supervisor_sql);
$supervisor_stmt->execute();
$supervisors = $supervisor_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$staff_list = [];

if (isset($_GET['supervisor_no']) && !empty($_GET['supervisor_no'])) {
    $supervisor_no = $_GET['supervisor_no'];

    $sql = "SELECT * FROM Staff
            WHERE supervisor_no = :supervisor_no";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':supervisor_no', $supervisor_no);
    $stmt->execute();

    $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<form method="GET">
    <label>Select Supervisor</label>
    <select name="supervisor_no" class="form-select">
        <option value="">Choose Supervisor</option>

        <?php foreach ($supervisors as $sup): ?>
            <option value="<?php echo $sup['staff_no']; ?>">
                <?php echo $sup['first_name'] . ' ' . $sup['last_name']; ?>
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
        <td><?php echo $staff['staff_no']; ?></td>
        <td><?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></td>
        <td><?php echo $staff['job_title']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
<a href="index.php" class="btn btn-secondary mt-3">Back to Staff List</a>