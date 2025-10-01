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

$book_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die("Book not found");
}

$stmt = $pdo->prepare("SELECT a.name FROM authors a 
                       JOIN book_author ba ON a.id = ba.author_id 
                       WHERE ba.book_id = ?");
$stmt->execute([$book_id]);
$authors = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-book"></i> Book Details</h1>
        <div>
            <a href="/Sweeftdigit-project/?page=books&action=edit&id=<?php echo $book['id']; ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Edit Book
            </a>
            <a href="/Sweeftdigit-project/?page=books" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Books
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Book Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Title:</dt>
                        <dd class="col-sm-9">
                            <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        </dd>

                        <dt class="col-sm-3">Authors:</dt>
                        <dd class="col-sm-9">
                            <?php foreach ($authors as $author): ?>
                                <span class="badge bg-primary me-1 fs-6"><?php echo htmlspecialchars($author); ?></span>
                            <?php endforeach; ?>
                        </dd>

                        <dt class="col-sm-3">Publication Year:</dt>
                        <dd class="col-sm-9"><?php echo $book['publication_year']; ?></dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            <span class="badge <?php echo $book['status'] == 'Available' ? 'bg-success' : 'bg-danger'; ?> fs-6">
                                <i class="fas fa-<?php echo $book['status'] == 'Available' ? 'check' : 'times'; ?>"></i>
                                <?php echo $book['status']; ?>
                            </span>
                        </dd>

                        <dt class="col-sm-3">Created:</dt>
                        <dd class="col-sm-9"><?php echo date('M d, Y \a\t h:i A', strtotime($book['created_at'])); ?></dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9"><?php echo date('M d, Y \a\t h:i A', strtotime($book['updated_at'])); ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/Sweeftdigit-project/?page=books&action=edit&id=<?php echo $book['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Book
                        </a>

                        <a href="/Sweeftdigit-project/?page=books&action=delete&id=<?php echo $book['id']; ?>" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Book
                        </a>

                        <a href="/Sweeftdigit-project/?page=books" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Books
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Book Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?php echo count($authors); ?></h4>
                                <small class="text-muted">Authors</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-<?php echo $book['status'] == 'Available' ? 'success' : 'danger'; ?>">
                                <?php echo $book['status'] == 'Available' ? '✓' : '✗'; ?>
                            </h4>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>