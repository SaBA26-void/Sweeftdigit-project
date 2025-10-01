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

$stmt = $pdo->prepare("SHOW DATABASES LIKE 'library_management'");
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "<h2>Database 'library_management' does not exist.</h2>";
    echo "<p>Nothing to rollback.</p>";
    exit;
}

$pdo->exec("USE library_management");

echo "<h2>Rolling Back Migrations</h2>";

$stmt = $pdo->prepare("SELECT MAX(batch) as max_batch FROM migrations");
$stmt->execute();
$maxBatch = $stmt->fetchColumn();

if (!$maxBatch) {
    echo "<p>No migrations found to rollback.</p>";
    exit;
}

echo "<p>Rolling back batch {$maxBatch}...</p>";

$tables = ['book_author', 'books', 'authors'];

foreach ($tables as $table) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS {$table}");
        echo "<p style='color: green;'>Dropped table: {$table}</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Failed to drop table {$table}: " . $e->getMessage() . "</p>";
    }
}

try {
    $pdo->exec("DROP TABLE IF EXISTS migrations");
    echo "<p style='color: green;'>Cleared migrations table</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Failed to clear migrations table: " . $e->getMessage() . "</p>";
}

echo "<h3>Rollback Complete!!!!!</h3>";
echo "<p>All tables have been dropped. Run <a href='run_migrations.php'>run_migrations.php</a> to set up the database again.</p>";
