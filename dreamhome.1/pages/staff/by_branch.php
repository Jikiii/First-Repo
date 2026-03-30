<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch all branches with their staff
$sql = "SELECT b.branch_no, b.city, s.staff_no, s.first_name, s.last_name, 
               s.job_title, s.salary, s.date_joined, k.full_name as kin_name
        FROM Branch b
        LEFT JOIN Staff s ON b.branch_no = s.branch_no
        LEFT JOIN NextOfKin k ON s.staff_no = k.staff_no
        ORDER BY b.city, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group staff by branch
$branches = [];
foreach ($results as $row) {
    $branches[$row['branch_no']]['city'] = $row['city'];
    if ($row['staff_no']) {
        $branches[$row['branch_no']]['staff'][] = $row;
    }
}
?>
<div class="card">
    <div class="card-header">
        <h2>Staff by Branch</h2>
    </div>
    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['city']; ?></h4>
            <?php if (!empty($branch['staff'])): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Staff No</th>
                            <th>Name</th>
                            <th>Job Title</th>
                            <th>Salary</th>
                            <th>Date Joined</th>
                            <th>Next of Kin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branch['staff'] as $member): ?>
                            <tr>
                                <td><?php echo $member['staff_no']; ?></td>
                                <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                                <td><?php echo $member['job_title']; ?></td>
                                <td>£<?php echo number_format($member['salary'], 2); ?></td>
                                <td><?php echo $member['date_joined']; ?></td>
                                <td><?php echo $member['kin_name'] ?: '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No staff in this branch.</p>
            <?php endif; ?>
        <?php endforeach; ?>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Staff List</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>