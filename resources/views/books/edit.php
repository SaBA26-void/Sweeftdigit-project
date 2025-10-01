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

$stmt = $pdo->prepare("SELECT author_id FROM book_author WHERE book_id = ?");
$stmt->execute([$book_id]);
$book_authors = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("SELECT * FROM authors ORDER BY name");
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Book</h1>
        <a href="/Sweeftdigit-project/?page=books" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Book Information</h5>
        </div>
        <div class="card-body">
            <form action="/Sweeftdigit-project/?page=books&action=update" method="POST">
                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?php echo htmlspecialchars($book['title']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="publication_year" class="form-label">Publication Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="publication_year" name="publication_year"
                                value="<?php echo $book['publication_year']; ?>"
                                min="1000" max="<?php echo date('Y'); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Available" <?php echo $book['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                        <option value="Borrowed" <?php echo $book['status'] == 'Borrowed' ? 'selected' : ''; ?>>Borrowed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="authors" class="form-label mb-0">Authors <span class="text-danger">*</span></label>
                        <div>
                            <a href="/Sweeftdigit-project/?page=authors&action=create&return_to=<?php echo urlencode('/Sweeftdigit-project/?page=books&action=edit&id=' . $book['id']); ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-plus"></i> Add New Author
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Refresh List
                            </button>
                        </div>
                    </div>
                    <div class="form-control" style="min-height: 150px; max-height: 200px; overflow-y: auto;">
                        <?php if (count($authors) > 0): ?>
                            <?php foreach ($authors as $author): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="authors[]" value="<?php echo $author['id']; ?>" id="author<?php echo $author['id']; ?>"
                                        <?php echo in_array($author['id'], $book_authors) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="author<?php echo $author['id']; ?>">
                                        <?php echo htmlspecialchars($author['name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <p>No authors found. <a href="/Sweeftdigit-project/?page=authors&action=create&return_to=<?php echo urlencode('/Sweeftdigit-project/?page=books&action=edit&id=' . $book['id']); ?>" target="_blank">Add the first author</a>.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <small class="form-text text-muted">
                        Select one or more authors. If you need to add a new author, click "Add New Author" above.
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Book
                    </button>
                    <a href="/Sweeftdigit-project/?page=books" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>