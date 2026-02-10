<?php
session_start();
require_once '../includes/db.php';

// Check Admin Access
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle Status Updates
if(isset($_POST['update_status'])) {
    $req_id = $_POST['request_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE adoption_requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $req_id]);
}

// Fetch Stats
$total_pets = $pdo->query("SELECT COUNT(*) FROM pets")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$pending_requests = $pdo->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'pending'")->fetchColumn();

// Fetch Requests
$requests = $pdo->query("
    SELECT r.*, p.name as pet_name, u.username, u.email 
    FROM adoption_requests r 
    JOIN pets p ON r.pet_id = p.id 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.request_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PetHaven</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar {
            width: 250px;
            background: var(--color-white);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
            border-right: 1px solid #eee;
        }
        .admin-content {
            margin-left: 250px;
            padding: 40px;
        }
        .stat-card {
            background: var(--color-white);
            padding: 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-card);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--color-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--color-primary);
        }
        .request-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--color-white);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .request-table th, .request-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .request-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .status-pending { background: #ffeeba; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body style="background: #f4f6f9;">

<div class="admin-sidebar">
    <a href="../index.php" class="logo mb-4" style="display: block;">
        <i class="fa-solid fa-paw"></i> PetHaven
    </a>
    <ul class="nav-links" style="flex-direction: column; gap: 10px;">
        <li><a href="dashboard.php" class="active" style="display: block; padding: 10px; border-radius: 8px; background: var(--color-primary); color: white;"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="pets_list.php" style="display: block; padding: 10px;"><i class="fa-solid fa-dog"></i> Manage Pets</a></li>
        <li><a href="add_pet.php" style="display: block; padding: 10px;"><i class="fa-solid fa-plus-circle"></i> Add New Pet</a></li>
        <li><a href="../logout.php" style="display: block; padding: 10px; color: var(--color-danger);"><i class="fa-solid fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="admin-content">
    <h2 class="mb-4">Dashboard Check</h2>
    
    <div class="hero-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 40px; gap: 20px;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-paw"></i></div>
            <div>
                <h3><?php echo $total_pets; ?></h3>
                <p>Total Pets</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <h3><?php echo $total_users; ?></h3>
                <p>Registered Users</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-file-contract"></i></div>
            <div>
                <h3><?php echo $pending_requests; ?></h3>
                <p>Pending Requests</p>
            </div>
        </div>
    </div>

    <h3 class="mb-4">Recent Adoption Requests</h3>
    <table class="request-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Pet</th>
                <th>Adopter</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($requests as $req): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($req['request_date'])); ?></td>
                <td><?php echo htmlspecialchars($req['pet_name']); ?></td>
                <td>
                    <?php echo htmlspecialchars($req['username']); ?><br>
                    <small style="color: #888;"><?php echo htmlspecialchars($req['email']); ?></small>
                </td>
                <td style="max-width: 300px;"><?php echo htmlspecialchars($req['message']); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $req['status']; ?>">
                        <?php echo ucfirst($req['status']); ?>
                    </span>
                </td>
                <td>
                    <?php if($req['status'] == 'pending'): ?>
                    <form method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                        <button type="submit" name="update_status" value="approved" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Approve</button>
                        <button type="submit" name="update_status" value="rejected" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; background: #ddd;">Reject</button>
                    </form>
                    <?php else: ?>
                        <span style="color: #aaa;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
