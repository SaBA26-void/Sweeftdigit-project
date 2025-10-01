<?php
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

echo "<h2>Book Rendering Debug Report</h2>";

echo "<h3>1. Main Books Query (same as index page):</h3>";
$sql = "SELECT b.*, GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') as author_names
        FROM books b 
        LEFT JOIN book_author ba ON b.id = ba.book_id 
        LEFT JOIN authors a ON ba.author_id = a.id 
        WHERE 1=1
        GROUP BY b.id ORDER BY b.title";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p><strong>Books found:</strong> " . count($books) . "</p>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Book ID</th><th>Title</th><th>Year</th><th>Status</th></tr>";
foreach ($books as $book) {
    echo "<tr><td>{$book['id']}</td><td>{$book['title']}</td><td>{$book['publication_year']}</td><td>{$book['status']}</td></tr>";
}
echo "</table>";

echo "<h3>2. Duplicate Check:</h3>";
$bookIds = array_column($books, 'id');
$duplicateIds = array_diff_assoc($bookIds, array_unique($bookIds));
if (!empty($duplicateIds)) {
    echo "<p style='color: red;'> Duplicate book IDs found: " . implode(', ', $duplicateIds) . "</p>";
} else {
    echo "<p style='color: green;'>No duplicate book IDs in main query.</p>";
}

echo "<h3>3. Authors for each book:</h3>";
foreach ($books as &$book) {
    $book['authors'] = $book['author_names'] ? explode(', ', $book['author_names']) : [];
    unset($book['author_names']);
}

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Book ID</th><th>Title</th><th>Authors</th><th>Author Count</th></tr>";
foreach ($books as $book) {
    $authors = implode(', ', $book['authors']);
    echo "<tr><td>{$book['id']}</td><td>{$book['title']}</td><td>{$authors}</td><td>" . count($book['authors']) . "</td></tr>";
}
echo "</table>";

echo "<h3>4. Book-Author relationships:</h3>";
$stmt = $pdo->prepare("
    SELECT ba.*, b.title, a.name as author_name
    FROM book_author ba
    JOIN books b ON ba.book_id = b.id
    JOIN authors a ON ba.author_id = a.id
    ORDER BY b.title, a.name
");
$stmt->execute();
$relationships = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Book ID</th><th>Author ID</th><th>Book Title</th><th>Author Name</th></tr>";
foreach ($relationships as $rel) {
    echo "<tr><td>{$rel['id']}</td><td>{$rel['book_id']}</td><td>{$rel['author_id']}</td><td>{$rel['title']}</td><td>{$rel['author_name']}</td></tr>";
}
echo "</table>";

echo "<h3>5. Duplicate relationship check:</h3>";
$stmt = $pdo->prepare("
    SELECT book_id, author_id, COUNT(*) as count 
    FROM book_author 
    GROUP BY book_id, author_id 
    HAVING COUNT(*) > 1
");
$stmt->execute();
$duplicateRels = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicateRels) > 0) {
    echo "<p style='color: red;'> Found " . count($duplicateRels) . " duplicate book-author relationships:</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Book ID</th><th>Author ID</th><th>Count</th></tr>";
    foreach ($duplicateRels as $dup) {
        echo "<tr><td>{$dup['book_id']}</td><td>{$dup['author_id']}</td><td>{$dup['count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'> No duplicate book-author relationships found.</p>";
}

echo "<h3>6. Simulated Book List Rendering:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
echo "<p><strong>Book List (" . count($books) . " books found)</strong></p>";
foreach ($books as $book) {
    echo "<div style='border-bottom: 1px solid #eee; padding: 5px;'>";
    echo "<strong>ID: {$book['id']} - {$book['title']}</strong><br>";
    echo "Authors: ";
    foreach ($book['authors'] as $author) {
        echo "<span style='background: #ccc; padding: 2px 5px; margin: 2px;'>" . htmlspecialchars($author) . "</span>";
    }
    echo "<br>Year: {$book['publication_year']}, Status: {$book['status']}";
    echo "</div>";
}
echo "</div>";

echo "<h3>7. Summary:</h3>";
echo "<p>Total Books: " . count($books) . "</p>";
echo "<p>Total Relationships: " . count($relationships) . "</p>";
echo "<p>Duplicate Relationships: " . count($duplicateRels) . "</p>";
