<?php
include_once '../../includes/header.php';
include_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Fetch all branches with their staff
$sql = "SELECT b.BranchNo, b.City, s.StaffNo, s.FName, s.LName, 
               s.JobTitle, s.Salary, s.HireDate, k.KinName as kin_name
        FROM Branch b
        LEFT JOIN Staff s ON b.BranchNo = s.BranchNo
        LEFT JOIN NextOfKin k ON s.StaffNo = k.StaffNo
        ORDER BY b.City, s.LName";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group staff by branch
$branches = [];
foreach ($results as $row) {
    $branches[$row['BranchNo']]['City'] = $row['City'];
    if ($row['StaffNo']) {
        $branches[$row['BranchNo']]['Staff'][] = $row;
    }
}
?>
<div class="card">
    <div class="card-header">
        <h2>Staff by Branch</h2>
    </div>
    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['City']; ?></h4>
            <?php if (!empty($branch['Staff'])): ?>
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
                        <?php foreach ($branch['Staff'] as $member): ?>
                            <tr>
                                <td><?php echo $member['StaffNo']; ?></td>
                                <td><?php echo $member['FName'] . ' ' . $member['LName']; ?></td>
                                <td><?php echo $member['JobTitle']; ?></td>
                                <td>£<?php echo number_format($member['Salary'], 2); ?></td>
                                <td><?php echo $member['HireDate']; ?></td>
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