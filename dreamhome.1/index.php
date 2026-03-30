<?php
include 'includes/header.php';
include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get counts for dashboard
$counts = [];

$queries = [
    'branches' => "SELECT COUNT(*) as count FROM Branch",
    'staff' => "SELECT COUNT(*) as count FROM Staff",
    'properties' => "SELECT COUNT(*) as count FROM Property WHERE status = 'available'",
    'renters' => "SELECT COUNT(*) as count FROM Renter",
    'leases' => "SELECT COUNT(*) as count FROM LeaseAgreement WHERE end_date >= CURDATE()",
    'inspections' => "SELECT COUNT(*) as count FROM PropertyInspection"
];

foreach ($queries as $key => $sql) {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $counts[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Dashboard</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/branches/index.php'">
                            <i class="bi bi-building"></i>
                            <h3><?php echo $counts['branches']; ?></h3>
                            <p>Branch Offices</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/staff/index.php'">
                            <i class="bi bi-people"></i>
                            <h3><?php echo $counts['staff']; ?></h3>
                            <p>Staff Members</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/properties/index.php'">
                            <i class="bi bi-house"></i>
                            <h3><?php echo $counts['properties']; ?></h3>
                            <p>Available Properties</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/renters/index.php'">
                            <i class="bi bi-person"></i>
                            <h3><?php echo $counts['renters']; ?></h3>
                            <p>Active Renters</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/leases/index.php'">
                            <i class="bi bi-file-text"></i>
                            <h3><?php echo $counts['leases']; ?></h3>
                            <p>Active Leases</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/inspections/index.php'">
                            <i class="bi bi-clipboard-check"></i>
                            <h3><?php echo $counts['inspections']; ?></h3>
                            <p>Total Inspections</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Properties -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Recent Properties</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Property No</th><th>Address</th><th>Type</th><th>Rent</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT property_no, street, city, type, monthly_rent FROM Property WHERE status = 'available' LIMIT 5";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>{$row['property_no']}</td>";
                                echo "<td>{$row['street']}, {$row['city']}</td>";
                                echo "<td>{$row['type']}</td>";
                                echo "<td>£{$row['monthly_rent']}</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Recent Inspections</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Property</th><th>Date</th><th>Inspector</th><th>Comments</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.property_no, pi.inspection_date, CONCAT(s.first_name, ' ', s.last_name) as inspector, pi.comments 
                                   FROM PropertyInspection pi 
                                   JOIN Property p ON pi.property_no = p.property_no 
                                   JOIN Staff s ON pi.staff_no = s.staff_no 
                                   ORDER BY pi.inspection_date DESC LIMIT 5";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>{$row['property_no']}</td>";
                                echo "<td>{$row['inspection_date']}</td>";
                                echo "<td>{$row['inspector']}</td>";
                                echo "<td>" . substr($row['comments'], 0, 30) . "...</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>