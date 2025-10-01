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

echo "<h2>Fixing Duplicate Book Issues</h2>";

echo "<h3>1. Checking for duplicate book-author relationships...</h3>";
$stmt = $pdo->prepare("
    SELECT book_id, author_id, COUNT(*) as count 
    FROM book_author 
    GROUP BY book_id, author_id 
    HAVING COUNT(*) > 1
");
$stmt->execute();
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicates) > 0) {
    echo "<p style='color: red;'>Found " . count($duplicates) . " duplicate relationships. Removing them...</p>";

    $pdo->exec("
        DELETE ba1 FROM book_author ba1
        INNER JOIN book_author ba2 
        WHERE ba1.id > ba2.id 
        AND ba1.book_id = ba2.book_id 
        AND ba1.author_id = ba2.author_id
    ");

    echo "<p style='color: green;'>Duplicates removed!</p>";
} else {
    echo "<p style='color: green;'>No duplicate relationships found.</p>";
}

echo "<h3>2. Current books and their relationships:</h3>";
$stmt = $pdo->prepare("
    SELECT b.id, b.title, GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') as authors
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

echo "<h3>3. Testing the books page query:</h3>";
$sql = "SELECT b.*, GROUP_CONCAT(a.name ORDER BY a.name SEPARATOR ', ') as author_names
        FROM books b 
        LEFT JOIN book_author ba ON b.id = ba.book_id 
        LEFT JOIN authors a ON ba.author_id = a.id 
        WHERE 1=1
        GROUP BY b.id ORDER BY b.title";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$testBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p><strong>Query returned " . count($testBooks) . " books</strong></p>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Book ID</th><th>Title</th><th>Author Names</th></tr>";
foreach ($testBooks as $book) {
    echo "<tr><td>{$book['id']}</td><td>{$book['title']}</td><td>{$book['author_names']}</td></tr>";
}
echo "</table>";

echo "<h3>4. Final check:</h3>";
$bookIds = array_column($testBooks, 'id');
$uniqueIds = array_unique($bookIds);

if (count($bookIds) === count($uniqueIds)) {
    echo "<p style='color: green;'>SUCCESS: All books are unique! No duplicates found.</p>";
} else {
    echo "<p style='color: red;'>Still found duplicates. Book IDs: " . implode(', ', $bookIds) . "</p>";
    echo "<p>Unique IDs: " . implode(', ', $uniqueIds) . "</p>";
}

echo "<h3>5. Next steps:</h3>";
echo "<p>1. Refresh your books page: <a href='/Sweeftdigit-project/?page=books' target='_blank'>http://localhost/Sweeftdigit-project/?page=books</a></p>";
echo "<p>2. Each book should now appear exactly once</p>";
echo "<p>3. If issues persist, check the database_setup.sql file for any data inconsistencies</p>";
