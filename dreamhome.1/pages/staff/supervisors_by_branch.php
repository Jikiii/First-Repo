<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "SELECT b.branch_no, b.city, 
               s.staff_no, s.first_name, s.last_name, s.job_title
        FROM Branch b
        LEFT JOIN Staff s ON b.branch_no = s.branch_no
        WHERE s.job_title = 'Supervisor'
        ORDER BY b.city, s.last_name";

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$branches = [];

foreach ($results as $row) {
    $branches[$row['branch_no']]['city'] = $row['city'];
    $branches[$row['branch_no']]['supervisors'][] = $row;
}
?>
<div class="card">
    <div class="card-header">
        <h2>Supervisors by Branch</h2>
    </div>

    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['city']; ?></h4>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Staff No</th>
                        <th>Name</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branch['supervisors'] as $sup): ?>
                    <tr>
                        <td><?php echo $sup['staff_no']; ?></td>
                        <td><?php echo $sup['first_name'] . ' ' . $sup['last_name']; ?></td>
                        <td><?php echo $sup['job_title']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <a href="index.php" class="btn btn-secondary mt-3">
            Back to Staff List
        </a>
    </div>
</div>