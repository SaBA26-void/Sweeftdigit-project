<?php
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus"></i> Add New Author</h1>
        <a href="<?php echo isset($_GET['return_to']) ? urldecode($_GET['return_to']) : '/Sweeftdigit-project/?page=authors'; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Authors
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Author Information</h5>
        </div>
        <div class="card-body">
            <form action="/Sweeftdigit-project/?page=authors&action=store<?php echo isset($_GET['return_to']) ? '&return_to=' . urlencode($_GET['return_to']) : ''; ?>" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Author Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter author's full name" required>
                    <small class="form-text text-muted">
                        Enter the full name of the author (e.g., "John Doe", "Jane Smith").
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Author
                    </button>
                    <a href="<?php echo isset($_GET['return_to']) ? urldecode($_GET['return_to']) : '/Sweeftdigit-project/?page=authors'; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>