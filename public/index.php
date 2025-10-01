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
    die("Connection failed: " . $e->getMessage());
}

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

$path = str_replace('/library-management', '', $path);
$path = str_replace('/public', '', $path);

switch ($path) {
    case '/':
    case '':
        header('Location: /library-management/public/books');
        exit;
    case '/books':
        include '../resources/views/books/index.php';
        break;
    case '/books/create':
        include '../resources/views/books/create.php';
        break;
    case '/authors':
        include '../resources/views/authors/index.php';
        break;
    case '/authors/create':
        include '../resources/views/authors/create.php';
        break;
    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
