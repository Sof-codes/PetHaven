<?php
/**
 * ADD PET PAGE (ADMIN)
 * Allows administrators to add new pets to the database.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

// Check Admin Access
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = '';
$err = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pet'])) {
    $name = trim($_POST['name']);
    $category_name = $_POST['category_name'];
    $breed = trim($_POST['breed']);
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $color = trim($_POST['color']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $behavior = trim($_POST['behavior']);
    $care_pattern = trim($_POST['care_pattern']);
    $is_rescued = isset($_POST['is_rescued']) ? 1 : 0;
    
    // Simple image processing
    $image = 'assets/images/default.jpg'; 
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $name) . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = 'assets/images/' . $file_name;
        }
    }
    
    $sql = "INSERT INTO pets (name, category_name, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$name, $category_name, $breed, $age, $gender, $color, $price, $description, $behavior, $care_pattern, $is_rescued, $image])) {
        header("Location: pets_list.php?msg=added");
        exit;
    } else {
        $err = "Error adding pet record.";
    }
}

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { width: 250px; background: var(--color-white); height: 100vh; position: fixed; left: 0; top: 0; padding: 20px; border-right: 1px solid #eee; }
        .admin-content { margin-left: 250px; padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .add-form-container { background: white; padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-card); max-width: 900px; }
        .form-section-title { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--color-warm-bg); color: var(--color-primary); }
    </style>
</head>
<body style="background: #f4f6f9;">

<div class="admin-sidebar">
    <a href="../index.php" class="logo mb-4" style="display: block;">
        <i class="fa-solid fa-paw"></i> PetHaven
    </a>
    <ul class="nav-links" style="flex-direction: column; gap: 10px;">
        <li><a href="dashboard.php" style="display: block; padding: 10px;"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="pets_list.php" style="display: block; padding: 10px;"><i class="fa-solid fa-dog"></i> Manage Pets</a></li>
        <li><a href="add_pet.php" class="active" style="display: block; padding: 10px; border-radius: 8px; background: var(--color-primary); color: white;"><i class="fa-solid fa-plus-circle"></i> Add New Pet</a></li>
        <li><a href="../logout.php" style="display: block; padding: 10px; color: var(--color-danger);"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="admin-content">
    <h2 class="mb-4">Register New Pet Profile</h2>
    
    <?php if($err): ?>
        <div style="background: var(--color-danger); color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;"><?php echo $err; ?></div>
    <?php endif; ?>

    <div class="add-form-container">
        <form method="POST" enctype="multipart/form-data">
            <h4 class="form-section-title">Basic Information</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label>Pet Name <span style="color:red;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Snowy" required>
                </div>
                <div class="form-group">
                    <label>Category <span style="color:red;">*</span></label>
                    <select name="category_name" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $c) echo "<option value='{$c['name']}'>{$c['name']}</option>"; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Breed</label>
                    <input type="text" name="breed" class="form-control" placeholder="e.g. Persian Cat">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" name="age" class="form-control" placeholder="e.g. 2 years">
                </div>
            </div>

            <h4 class="form-section-title mt-4">Physical Attributes & Adoption</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" class="form-control" placeholder="e.g. Pure White">
                </div>
                <div class="form-group">
                    <label>Adoption Fee (₹) <span style="color:red;">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" value="0.00" required>
                </div>
                <div class="form-group">
                    <label>Pet Photo</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>

            <h4 class="form-section-title mt-4">Behavior & Care</h4>
            <div class="form-group">
                <label>Tell their story (Description)</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe the pet's journey, health status, and personality..."></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Behavior / Personality</label>
                    <input type="text" name="behavior" class="form-control" placeholder="e.g. Playful, shy, good with kids">
                </div>
                <div class="form-group">
                    <label>Care Requirements</label>
                    <input type="text" name="care_pattern" class="form-control" placeholder="e.g. Needs daily walks, twice feeding">
                </div>
            </div>
            
            <div class="form-group mt-3">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; background: var(--color-warm-bg); padding: 15px; border-radius: 8px;">
                    <input type="checkbox" name="is_rescued" value="1" style="width: 20px; height: 20px;">
                    <strong>Mark as Rescued Pet</strong> (Displays Rescued badge on the card)
                </label>
            </div>

            <button type="submit" name="add_pet" class="btn btn-primary" style="width: 100%; height: 50px; font-size: 1.1rem; margin-top: 30px;">
                <i class="fa-solid fa-floppy-disk"></i> Save Pet Profile
            </button>
        </form>
    </div>
</div>

</body>
</html>
