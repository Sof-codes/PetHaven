<?php
session_start();
require_once '../includes/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $color = $_POST['color'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $behavior = $_POST['behavior'];
    $care_pattern = $_POST['care_pattern'];
    $is_rescued = isset($_POST['is_rescued']) ? 1 : 0;
    
    // Simple Image handling - in real app, use move_uploaded_file
    // For now we will use a placeholder or specific logic if provided, to keep it simple and working without file permissions issues on some setups
    $image = 'assets/images/placeholder.png'; 
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        // Just mocking the upload for safety in generated code, but let's try to actually move it if we were real.
        // I will use just the string path for now in DB and rely on user to put file there or just use placeholder logic
        $image = 'assets/images/' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    $sql = "INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$name, $category_id, $breed, $age, $gender, $color, $price, $description, $behavior, $care_pattern, $is_rescued, $image])) {
        $msg = "Pet added successfully!";
    } else {
        $msg = "Error adding pet.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { width: 250px; background: var(--color-white); height: 100vh; position: fixed; left: 0; top: 0; padding: 20px; border-right: 1px solid #eee; }
        .admin-content { margin-left: 250px; padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
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
    <h2 class="mb-4">Add New Pet</h2>
    
    <?php if($msg): ?>
        <div style="background: var(--color-success); padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-card);">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Pet Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php
                        $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                        foreach($cats as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Breed</label>
                    <input type="text" name="breed" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" name="age" class="form-control" placeholder="e.g. 2 years" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Adoption Fee (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="0.00">
                </div>
                <div class="form-group">
                    <label>Pet Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            
             <div class="form-grid">
                <div class="form-group">
                    <label>Behavior</label>
                    <input type="text" name="behavior" class="form-control" placeholder="e.g. Friendly, Calm">
                </div>
                <div class="form-group">
                    <label>Care Pattern</label>
                    <input type="text" name="care_pattern" class="form-control" placeholder="e.g. Needs daily walks">
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_rescued" value="1" style="width: 20px; height: 20px;">
                    Is this a rescued pet?
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Add Pet</button>
        </form>
    </div>
</div>

</body>
</html>
