<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(18, 13, 13, 0.13);
        }

        .card-header {
            background-color: rgb(255, 255, 255);
            border-bottom: 1px solid rgba(20, 17, 17, 0.13);
        }

        .status-available {
            color: #198754;
        }

        .status-borrowed {
            color: rgb(238, 49, 68);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/Sweeftdigit-project/">
                <i class="fas fa-book"></i> Library Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/Sweeftdigit-project/?page=books">
                            <i class="fas fa-books"></i> Books
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Sweeftdigit-project/?page=authors">
                            <i class="fas fa-user-pen"></i> Authors
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>