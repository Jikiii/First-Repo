<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$staff_no = $_GET['staff_no'];

$sql = "SELECT * FROM Staff WHERE StaffNo = :staff_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':staff_no', $staff_no);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "<div class='alert alert-danger'>Staff member not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$branch_sql = "SELECT BranchNo, BranchName FROM Branch ORDER BY BranchName";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $national_id = $_POST['national_id'];
    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $hire_date = $_POST['hire_date'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "UPDATE Staff SET FName=:fname, LName=:lname, Address=:address, Phone=:phone, 
            Email=:email, Gender=:gender, BirthDate=:birthdate, NationalID=:national_id, 
            JobTitle=:job_title, Salary=:salary, HireDate=:hire_date, BranchNo=:branch_no 
            WHERE StaffNo=:staff_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':birthdate', $birthdate);
    $stmt->bindParam(':national_id', $national_id);
    $stmt->bindParam(':job_title', $job_title);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':hire_date', $hire_date);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':staff_no', $staff_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Staff member updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?staff_no=$staff_no'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating staff member!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Staff Member: <?php echo $staff['FName'] . ' ' . $staff['LName']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Staff Number</label>
                    <input type="text" class="form-control" value="<?php echo $staff['StaffNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="fname" value="<?php echo $staff['FName']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="lname" value="<?php echo $staff['LName']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required><?php echo $staff['Address']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo $staff['Phone']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $staff['Email']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender *</label>
                    <select class="form-select" name="gender" required>
                        <option value="M" <?php echo $staff['Gender'] == 'M' ? 'selected' : ''; ?>>Male</option>
                        <option value="F" <?php echo $staff['Gender'] == 'F' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" name="birthdate" value="<?php echo $staff['BirthDate']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">National ID *</label>
                    <input type="text" class="form-control" name="national_id" value="<?php echo $staff['NationalID']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Job Title *</label>
                    <select class="form-select" name="job_title" required>
                        <option value="Manager" <?php echo $staff['JobTitle'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="Supervisor" <?php echo $staff['JobTitle'] == 'Supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                        <option value="Administrator" <?php echo $staff['JobTitle'] == 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                        <option value="Secretary" <?php echo $staff['JobTitle'] == 'Secretary' ? 'selected' : ''; ?>>Secretary</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (£) *</label>
                    <input type="number" class="form-control" name="salary" value="<?php echo $staff['Salary']; ?>" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hire Date *</label>
                    <input type="date" class="form-control" name="hire_date" value="<?php echo $staff['HireDate']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['BranchNo']; ?>" <?php echo $staff['BranchNo'] == $branch['BranchNo'] ? 'selected' : ''; ?>>
                            <?php echo $branch['BranchName']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Staff Member</button>
            <a href="view.php?staff_no=<?php echo $staff['StaffNo']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>