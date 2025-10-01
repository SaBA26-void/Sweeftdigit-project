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

$author_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM authors WHERE id = ?");
$stmt->execute([$author_id]);
$author = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$author) {
    die("Author not found");
}

$stmt = $pdo->prepare("SELECT b.* FROM books b 
                       JOIN book_author ba ON b.id = ba.book_id 
                       WHERE ba.author_id = ?");
$stmt->execute([$author_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-pen"></i> Author Details</h1>
        <div>
            <a href="/Sweeftdigit-project/?page=authors&action=edit&id=<?php echo $author['id']; ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Edit Author
            </a>
            <a href="/Sweeftdigit-project/?page=authors" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Authors
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Author Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Name:</dt>
                        <dd class="col-sm-9">
                            <h4><?php echo htmlspecialchars($author['name']); ?></h4>
                        </dd>

                        <dt class="col-sm-3">Books Written:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-primary fs-6"><?php echo count($books); ?> book<?php echo count($books) != 1 ? 's' : ''; ?></span>
                        </dd>

                        <dt class="col-sm-3">Created:</dt>
                        <dd class="col-sm-9"><?php echo date('M d, Y \a\t h:i A', strtotime($author['created_at'])); ?></dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9"><?php echo date('M d, Y \a\t h:i A', strtotime($author['updated_at'])); ?></dd>
                    </dl>
                </div>
            </div>

            <?php if (count($books) > 0): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Books by <?php echo htmlspecialchars($author['name']); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($books as $book): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted"><?php echo $book['publication_year']; ?></small>
                                                <span class="badge <?php echo $book['status'] == 'Available' ? 'bg-success' : 'bg-danger'; ?> ms-2">
                                                    <?php echo $book['status']; ?>
                                                </span>
                                            </p>
                                            <a href="/Sweeftdigit-project/?page=books&action=show&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Book
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/Sweeftdigit-project/?page=authors&action=edit&id=<?php echo $author['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Author
                        </a>

                        <?php if (count($books) == 0): ?>
                            <a href="/Sweeftdigit-project/?page=authors&action=delete&id=<?php echo $author['id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this author? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete Author
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-trash"></i> Cannot Delete (Has Books)
                            </button>
                        <?php endif; ?>

                        <a href="/Sweeftdigit-project/?page=authors" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Authors
                        </a>

                        <a href="/Sweeftdigit-project/?page=books&action=create" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add Book by <?php echo htmlspecialchars($author['name']); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Author Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary"><?php echo count($books); ?></h4>
                                <small class="text-muted">Total Books</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success"><?php echo count(array_filter($books, function ($book) {
                                                            return $book['status'] == 'Available';
                                                        })); ?></h4>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="text-info"><?php echo count(array_filter($books, function ($book) {
                                                    return $book['status'] == 'Borrowed';
                                                })); ?></h4>
                        <small class="text-muted">Borrowed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>