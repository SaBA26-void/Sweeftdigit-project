<?php
session_start();

$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    if ($e->getCode() == 1049) {
        header("Location: /Sweeftdigit-project/run_migrations.php");
        exit;
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

$page = $_GET['page'] ?? 'books';
$action = $_GET['action'] ?? 'index';

if (!isset($_SESSION['duplicates_cleaned'])) {
    try {
        $pdo->exec("
            DELETE ba1 FROM book_author ba1
            INNER JOIN book_author ba2 
            WHERE ba1.id > ba2.id 
            AND ba1.book_id = ba2.book_id 
            AND ba1.author_id = ba2.author_id
        ");
        $_SESSION['duplicates_cleaned'] = true;
    } catch (Exception $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'books' && $action === 'store') {
        $title = $_POST['title'];
        $publication_year = $_POST['publication_year'];
        $status = $_POST['status'];
        $authors = $_POST['authors'] ?? [];

        $stmt = $pdo->prepare("INSERT INTO books (title, publication_year, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$title, $publication_year, $status]);
        $book_id = $pdo->lastInsertId();

        foreach ($authors as $author_id) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO book_author (book_id, author_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$book_id, $author_id]);
        }

        header("Location: /Sweeftdigit-project/?page=books&success=Book created successfully!");
        exit;
    }

    if ($page === 'authors' && $action === 'store') {
        $name = $_POST['name'];

        $stmt = $pdo->prepare("INSERT INTO authors (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$name]);

        $return_to = $_GET['return_to'] ?? null;
        if ($return_to) {
            header("Location: {$return_to}&success=Author created successfully!");
        } else {
            header("Location: /Sweeftdigit-project/?page=authors&success=Author created successfully!");
        }
        exit;
    }

    if ($page === 'books' && $action === 'update') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $publication_year = $_POST['publication_year'];
        $status = $_POST['status'];
        $authors = $_POST['authors'] ?? [];

        $stmt = $pdo->prepare("UPDATE books SET title = ?, publication_year = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $publication_year, $status, $id]);

        $stmt = $pdo->prepare("DELETE FROM book_author WHERE book_id = ?");
        $stmt->execute([$id]);

        foreach ($authors as $author_id) {
            $stmt = $pdo->prepare("INSERT INTO book_author (book_id, author_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$id, $author_id]);
        }

        header("Location: /Sweeftdigit-project/?page=books&success=Book updated successfully!");
        exit;
    }

    if ($page === 'authors' && $action === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];

        $stmt = $pdo->prepare("UPDATE authors SET name = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $id]);

        header("Location: /Sweeftdigit-project/?page=authors&success=Author updated successfully!");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
    $id = $_GET['id'];

    if ($page === 'books') {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: /Sweeftdigit-project/?page=books&success=Book deleted successfully!");
        exit;
    }

    if ($page === 'authors') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM book_author WHERE author_id = ?");
        $stmt->execute([$id]);
        $bookCount = $stmt->fetchColumn();

        if ($bookCount > 0) {
            header("Location: /Sweeftdigit-project/?page=authors&error=Cannot delete author with existing books!");
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM authors WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: /Sweeftdigit-project/?page=authors&success=Author deleted successfully!");
        exit;
    }
}

if (isset($showSetup)) {
    include 'setup.php';
} elseif ($page === 'books') {
    if ($action === 'create') {
        include 'resources/views/books/create.php';
    } elseif ($action === 'edit') {
        include 'resources/views/books/edit.php';
    } elseif ($action === 'show') {
        include 'resources/views/books/show.php';
    } else {
        include 'resources/views/books/index.php';
    }
} elseif ($page === 'authors') {
    if ($action === 'create') {
        include 'resources/views/authors/create.php';
    } elseif ($action === 'edit') {
        include 'resources/views/authors/edit.php';
    } elseif ($action === 'show') {
        include 'resources/views/authors/show.php';
    } else {
        include 'resources/views/authors/index.php';
    }
} else {
    include 'resources/views/books/index.php';
}
