<?php
include_once __DIR__ . '/../layouts/header.php';

$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$sql = "SELECT a.*, COUNT(ba.book_id) as books_count 
        FROM authors a 
        LEFT JOIN book_author ba ON a.id = ba.author_id 
        GROUP BY a.id 
        ORDER BY a.name";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-pen"></i> Authors</h1>
        <a href="/Sweeftdigit-project/?page=authors&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Author
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Author List (<?php echo count($authors); ?> authors found)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Books Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($authors as $author): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($author['name']); ?></strong></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $author['books_count']; ?> book<?php echo $author['books_count'] != 1 ? 's' : ''; ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($author['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/Sweeftdigit-project/?page=authors&action=show&id=<?php echo $author['id']; ?>" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/Sweeftdigit-project/?page=authors&action=edit&id=<?php echo $author['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($author['books_count'] == 0): ?>
                                            <a href="/Sweeftdigit-project/?page=authors&action=delete&id=<?php echo $author['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this author?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete - author has books">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>