<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$results = [];
$search_performed = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_performed = true;
    $property_type = $_POST['property_type'];
    $max_rent = $_POST['max_rent'];
    $city = $_POST['city'];
    
    $sql = "SELECT p.*, CONCAT(s.first_name, ' ', s.last_name) as manager 
            FROM Property p 
            LEFT JOIN Staff s ON p.staff_no = s.staff_no 
            WHERE p.status = 'available'";
    
    $params = [];
    
    if (!empty($property_type)) {
        $sql .= " AND p.type = :type";
        $params[':type'] = $property_type;
    }
    
    if (!empty($max_rent)) {
        $sql .= " AND p.monthly_rent <= :max_rent";
        $params[':max_rent'] = $max_rent;
    }
    
    if (!empty($city)) {
        $sql .= " AND p.city LIKE :city";
        $params[':city'] = "%$city%";
    }
    
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Search Properties</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Property Type</label>
                    <select class="form-select" name="property_type">
                        <option value="">Any</option>
                        <option value="Flat">Flat</option>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Maximum Monthly Rent (£)</label>
                    <input type="number" class="form-control" name="max_rent" step="50">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        
        <?php if ($search_performed): ?>
        <div class="mt-4">
            <h4>Search Results (<?php echo count($results); ?> properties found)</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Property No</th>
                            <th>Address</th>
                            <th>Type</th>
                            <th>Rooms</th>
                            <th>Monthly Rent</th>
                            <th>Manager</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $property): ?>
                        <tr>
                            <td><?php echo $property['property_no']; ?></td>
                            <td><?php echo $property['street'] . ', ' . $property['city']; ?></td>
                            <td><?php echo $property['type']; ?></td>
                            <td><?php echo $property['rooms']; ?></td>
                            <td>£<?php echo number_format($property['monthly_rent'], 2); ?></td>
                            <td><?php echo $property['manager']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewProperty('<?php echo $property['property_no']; ?>')">View Details</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function viewProperty(property_no) {
    window.location.href = `view.php?property_no=${property_no}`;
}
</script>

<?php include '../../includes/footer.php'; ?>