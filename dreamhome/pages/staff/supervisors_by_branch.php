<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "SELECT b.BranchNo, b.City, 
               s.StaffNo, s.FName, s.LName, s.JobTitle
        FROM Branch b
        LEFT JOIN Staff s ON b.BranchNo = s.BranchNo
        WHERE s.JobTitle = 'Supervisor'
        ORDER BY b.City, s.LName";

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$branches = [];

foreach ($results as $row) {
    $branchNo = $row['BranchNo'];

    $branches[$branchNo]['City'] = $row['City'];
    $branches[$branchNo]['Supervisors'][] = $row;
}
?>

<div class="card">
    <div class="card-header">
        <h2>Supervisors by Branch</h2>
    </div>

    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['City']; ?></h4>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Staff No</th>
                        <th>Name</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branch['Supervisors'] as $sup): ?>
                    <tr>
                        <td><?php echo $sup['StaffNo']; ?></td>
                        <td>
                            <?php echo $sup['FName'] . ' ' . $sup['LName']; ?>
                        </td>
                        <td><?php echo $sup['JobTitle']; ?></td>
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