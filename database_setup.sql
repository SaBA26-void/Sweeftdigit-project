
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

CREATE TABLE IF NOT EXISTS authors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    publication_year YEAR NOT NULL,
    status ENUM('Available', 'Borrowed') DEFAULT 'Available',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS book_author (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    book_id BIGINT UNSIGNED NOT NULL,
    author_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_book_author (book_id, author_id)
);

INSERT INTO authors (name, created_at, updated_at) VALUES
('J.K. Rowling', NOW(), NOW()),
('George R.R. Martin', NOW(), NOW()),
('Harper Lee', NOW(), NOW()),
('Nodar Dumbadze', NOW(), NOW()),
('გივი სიხარულიძე', NOW(), NOW()),
('Ilia Chavchavadze', NOW(), NOW());

INSERT INTO books (title, publication_year, status, created_at, updated_at) VALUES
('Harry Potter and the Philosopher''s Stone', 1997, 'Available', NOW(), NOW()),
('A Game of Thrones', 1996, 'Available', NOW(), NOW()),
('To Kill a Mockingbird', 1960, 'Borrowed', NOW(), NOW()),
('Kukaracha', 1970, 'Available', NOW(), NOW()),
('აწყვეტილი სიცოცხლე(რჩეული)', 2002, 'Borrowed', NOW(), NOW()),
('Mgzavris Werilebi', 1912, 'Available', NOW(), NOW());

INSERT INTO book_author (book_id, author_id, created_at, updated_at) VALUES
(1, 1, NOW(), NOW()), 
(2, 2, NOW(), NOW()), 
(3, 3, NOW(), NOW()), 
(4, 4, NOW(), NOW()), 
(5, 5, NOW(), NOW()); 
