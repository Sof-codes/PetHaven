<?php
session_start();
require_once '../includes/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Delete Logic
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM pets WHERE id = ?")->execute([$id]);
    header("Location: pets_list.php?msg=deleted");
}

$pets = $pdo->query("SELECT p.*, c.name as category_name FROM pets p LEFT JOIN categories c ON p.category_id = c.id ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pets - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar { width: 250px; background: var(--color-white); height: 100vh; position: fixed; left: 0; top: 0; padding: 20px; border-right: 1px solid #eee; }
        .admin-content { margin-left: 250px; padding: 40px; }
        .pet-table { width: 100%; border-collapse: collapse; background: white; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-card); }
        .pet-table th, .pet-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        .pet-table th { background: #f8f9fa; }
        .action-btn { padding: 5px 10px; border-radius: 5px; font-size: 0.8rem; margin-right: 5px; }
    </style>
</head>
<body style="background: #f4f6f9;">

<div class="admin-sidebar">
    <a href="../index.php" class="logo mb-4" style="display: block;">
        <i class="fa-solid fa-paw"></i> PetHaven
    </a>
    <ul class="nav-links" style="flex-direction: column; gap: 10px;">
        <li><a href="dashboard.php" style="display: block; padding: 10px;"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="pets_list.php" class="active" style="display: block; padding: 10px; border-radius: 8px; background: var(--color-primary); color: white;"><i class="fa-solid fa-dog"></i> Manage Pets</a></li>
        <li><a href="add_pet.php" style="display: block; padding: 10px;"><i class="fa-solid fa-plus-circle"></i> Add New Pet</a></li>
        <li><a href="../logout.php" style="display: block; padding: 10px; color: var(--color-danger);"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="admin-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2>Manage Pets</h2>
        <a href="add_pet.php" class="btn btn-primary">Add New Pet</a>
    </div>

    <table class="pet-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Breed</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pets as $pet): ?>
            <tr>
                <td><div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; overflow: hidden;"><img src="<?php echo $pet['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;"></div></td>
                <td><?php echo htmlspecialchars($pet['name']); ?></td>
                <td><?php echo htmlspecialchars($pet['category_name']); ?></td>
                <td><?php echo htmlspecialchars($pet['breed']); ?></td>
                <td>
                    <span class="tag" style="background: <?php echo $pet['status'] == 'available' ? '#d4edda' : '#f8d7da'; ?>;">
                        <?php echo ucfirst($pet['status']); ?>
                    </span>
                </td>
                <td>
                    <a href="pet_details.php?id=<?php echo $pet['id']; ?>" class="action-btn" style="background: var(--color-secondary);">View</a>
                    <a href="?delete=<?php echo $pet['id']; ?>" class="action-btn" style="background: var(--color-danger); color: white;" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
