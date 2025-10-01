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

echo "<h2>Library Management System - Debug Report</h2>";

echo "<h3>1. Checking for duplicate book-author relationships:</h3>";
$stmt = $pdo->prepare("
    SELECT book_id, author_id, COUNT(*) as count 
    FROM book_author 
    GROUP BY book_id, author_id 
    HAVING COUNT(*) > 1
");
$stmt->execute();
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicates) > 0) {
    echo "<p style='color: red;'> Found " . count($duplicates) . " duplicate book-author relationships:</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Book ID</th><th>Author ID</th><th>Count</th></tr>";
    foreach ($duplicates as $dup) {
        echo "<tr><td>{$dup['book_id']}</td><td>{$dup['author_id']}</td><td>{$dup['count']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'> No duplicate book-author relationships found.</p>";
}

echo "<h3>2. All books and their authors:</h3>";
$stmt = $pdo->prepare("
    SELECT b.id, b.title, GROUP_CONCAT(a.name ORDER BY a.name) as authors
    FROM books b
    LEFT JOIN book_author ba ON b.id = ba.book_id
    LEFT JOIN authors a ON ba.author_id = a.id
    GROUP BY b.id, b.title
    ORDER BY b.title
");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Book ID</th><th>Title</th><th>Authors</th></tr>";
foreach ($books as $book) {
    echo "<tr><td>{$book['id']}</td><td>{$book['title']}</td><td>{$book['authors']}</td></tr>";
}
echo "</table>";

echo "<h3>3. Book-Author table content:</h3>";
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

echo "<h3>4. Summary:</h3>";
$stmt = $pdo->prepare("SELECT COUNT(*) as book_count FROM books");
$stmt->execute();
$bookCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as author_count FROM authors");
$stmt->execute();
$authorCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as relationship_count FROM book_author");
$stmt->execute();
$relationshipCount = $stmt->fetchColumn();

echo "<p>Total Books: {$bookCount}</p>";
echo "<p>Total Authors: {$authorCount}</p>";
echo "<p>Total Book-Author Relationships: {$relationshipCount}</p>";

if ($relationshipCount > ($bookCount + $authorCount)) {
    echo "<p style='color: orange;'>High number of relationships detected. This might indicate duplicates.</p>";
}
