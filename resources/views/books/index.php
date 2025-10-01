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

$sql = "SELECT b.*, GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR ', ') as author_names
        FROM books b 
        LEFT JOIN book_author ba ON b.id = ba.book_id 
        LEFT JOIN authors a ON ba.author_id = a.id 
        WHERE 1=1";

$params = [];

if (!empty($_GET['title'])) {
    $sql .= " AND b.title LIKE ?";
    $params[] = "%{$_GET['title']}%";
}

if (!empty($_GET['author'])) {
    $sql .= " AND a.name LIKE ?";
    $params[] = "%{$_GET['author']}%";
}

if (!empty($_GET['status'])) {
    $sql .= " AND b.status = ?";
    $params[] = $_GET['status'];
}

$sql .= " GROUP BY b.id ORDER BY b.title";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$uniqueBooks = [];
$seenIds = [];

foreach ($books as $book) {
    if (!in_array($book['id'], $seenIds)) {
        $book['authors'] = $book['author_names'] ? explode(', ', $book['author_names']) : [];
        unset($book['author_names']);
        $uniqueBooks[] = $book;
        $seenIds[] = $book['id'];
    }
}

$books = $uniqueBooks;
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
        <h1><i class="fas fa-books"></i> Books</h1>
        <a href="/Sweeftdigit-project/?page=books&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="title" class="form-label">Search by Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo htmlspecialchars($_GET['title'] ?? ''); ?>" placeholder="Enter book title...">
                    </div>
                    <div class="col-md-4">
                        <label for="author" class="form-label">Search by Author</label>
                        <input type="text" class="form-control" id="author" name="author"
                            value="<?php echo htmlspecialchars($_GET['author'] ?? ''); ?>" placeholder="Enter author name...">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="Available" <?php echo (($_GET['status'] ?? '') == 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Borrowed" <?php echo (($_GET['status'] ?? '') == 'Borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Book List (<?php echo count($books); ?> books found)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Authors</th>
                            <th>Publication Year</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                                <td>
                                    <?php foreach ($book['authors'] as $author): ?>
                                        <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($author); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo $book['publication_year']; ?></td>
                                <td>
                                    <span class="badge <?php echo $book['status'] == 'Available' ? 'bg-success' : 'bg-danger'; ?>">
                                        <i class="fas fa-<?php echo $book['status'] == 'Available' ? 'check' : 'times'; ?>"></i>
                                        <?php echo $book['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/Sweeftdigit-project/?page=books&action=show&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/Sweeftdigit-project/?page=books&action=edit&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/Sweeftdigit-project/?page=books&action=delete&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this book?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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