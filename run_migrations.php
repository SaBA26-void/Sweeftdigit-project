<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS library_management");
$pdo->exec("USE library_management");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$migrationFiles = glob('database/migrations/*.php');
sort($migrationFiles);

echo "<h2>Library Management System - Migration Runner</h2>";
echo "<p>Running migrations...</p>";

$batch = 1;
$runCount = 0;

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
    $stmt->execute([$migrationName]);

    if ($stmt->fetchColumn() > 0) {
        echo "<p style='color: orange;'>Migration '{$migrationName}' already run - skipping</p>";
        continue;
    }

    echo "<p style='color: blue;'>Running migration: {$migrationName}</p>";

    $content = file_get_contents($file);

    if (preg_match('/public function up\(\):\s*void\s*\{\s*Schema::create\([^,]+,\s*function\s*\([^)]+\)\s*\{([^}]+)\}\);/s', $content, $matches)) {
        $tableDefinition = $matches[1];

        if (strpos($file, 'authors') !== false) {
            $sql = "
                CREATE TABLE IF NOT EXISTS authors (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                )
            ";
        } elseif (strpos($file, 'books') !== false) {
            $sql = "
                CREATE TABLE IF NOT EXISTS books (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    publication_year YEAR NOT NULL,
                    status ENUM('Available', 'Borrowed') DEFAULT 'Available',
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                )
            ";
        } elseif (strpos($file, 'book_author') !== false) {
            $sql = "
                CREATE TABLE IF NOT EXISTS book_author (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    book_id BIGINT UNSIGNED NOT NULL,
                    author_id BIGINT UNSIGNED NOT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
                    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_book_author (book_id, author_id)
                )
            ";
        }

        if (isset($sql)) {
            try {
                $pdo->exec($sql);
                echo "<p style='color: green;'>Migration '{$migrationName}' completed successfully</p>";

                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migrationName, $batch]);
                $runCount++;
            } catch (Exception $e) {
                echo "<p style='color: red;'>Migration '{$migrationName}' failed: " . $e->getMessage() . "</p>";
            }
        }
    }
}

if ($runCount > 0) {
    echo "<h3>Migrations completed successfully!</h3>";
    echo "<p>Ran {$runCount} new migration(s).</p>";
} else {
    echo "<h3>All migrations are up to date.</h3>";
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM authors");
$stmt->execute();
$authorCount = $stmt->fetchColumn();

if ($authorCount == 0) {
    echo "<h3>Seeding sample data...</h3>";

    $authors = [
        ['J.K. Rowling'],
        ['George R.R. Martin'],
        ['Harper Lee'],
        ['Nodar Dumbadze'],
        ['გივი სიხარულიძე'],
        ['Ilia Chavchavadze']
    ];

    foreach ($authors as $author) {
        $stmt = $pdo->prepare("INSERT INTO authors (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute($author);
    }

    $books = [
        ['Harry Potter and the Philosopher\'s Stone', 1997, 'Available'],
        ['A Game of Thrones', 1996, 'Available'],
        ['To Kill a Mockingbird', 1960, 'Borrowed'],
        ['Kukaracha', 1970, 'Available'],
        ['აწყვეტილი სიცოცხლე(რჩეული)', 2002, 'Borrowed'],
        ['Mgzavris Werilebi', 1912, 'Available']
    ];

    foreach ($books as $book) {
        $stmt = $pdo->prepare("INSERT INTO books (title, publication_year, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute($book);
    }

    $relationships = [
        [1, 1],
        [2, 2],
        [3, 3],
        [4, 4],
        [5, 5],
        [6, 6]
    ];

    foreach ($relationships as $rel) {
        $stmt = $pdo->prepare("INSERT INTO book_author (book_id, author_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute($rel);
    }

    echo "<p style='color: green;'>Sample data seeded successfully!</p>";
}

echo "<h3>Setup Complete!!!!</h3>";
echo "<p>You can now access your Library Management System:</p>";
echo "<p><a href='/Sweeftdigit-project/?page=books' target='_blank'>View Books</a></p>";
echo "<p><a href='/Sweeftdigit-project/?page=authors' target='_blank'>View Authors</a></p>";

echo "<h3>Database Status:</h3>";
$tables = ['authors', 'books', 'book_author'];
foreach ($tables as $table) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table}");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "<p>{$table}: {$count} records</p>";
}
