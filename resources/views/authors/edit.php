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
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Author</h1>
        <a href="/Sweeftdigit-project/?page=authors" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Authors
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Author Information</h5>
        </div>
        <div class="card-body">
            <form action="/Sweeftdigit-project/?page=authors&action=update" method="POST">
                <input type="hidden" name="id" value="<?php echo $author['id']; ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">Author Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($author['name']); ?>"
                        placeholder="Enter author's full name" required>
                    <small class="form-text text-muted">
                        Enter the full name of the author (e.g., "John Doe", "Jane Smith").
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Author
                    </button>
                    <a href="/Sweeftdigit-project/?page=authors" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>