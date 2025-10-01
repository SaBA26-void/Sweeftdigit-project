<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Database Setup Required</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">The Library Management System database has not been set up yet.</p>
                        
                        <h5>Follow these steps to get started:</h5>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Step 1: Database Setup</h6>
                            <ol>
                                <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                                <li>Click on "Import" tab</li>
                                <li>Choose the <code>database_setup.sql</code> file from this project</li>
                                <li>Click "Go" to import the database</li>
                            </ol>
                        </div>

                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Step 2: Refresh This Page</h6>
                            <p>After importing the database, refresh this page to access the Library Management System.</p>
                        </div>

                        <div class="mt-4">
                            <h6>What will be created:</h6>
                            <ul>
                                <li>Database: <code>library_management</code></li>
                                <li>Tables: <code>authors</code>, <code>books</code>, <code>book_author</code></li>
                                <li>Sample data with 5 authors and 5 books</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Refresh Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
