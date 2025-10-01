@extends('layouts.app')

@section('title', 'Books - Library Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-books"></i> Books</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Book
    </a>
</div>

<div class="search-form">
    <form method="GET" action="{{ route('books.index') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="title" class="form-label">Search by Title</label>
                <input type="text" class="form-control" id="title" name="title"
                    value="{{ request('title') }}" placeholder="Enter book title...">
            </div>
            <div class="col-md-4">
                <label for="author" class="form-label">Search by Author</label>
                <input type="text" class="form-control" id="author" name="author"
                    value="{{ request('author') }}" placeholder="Enter author name...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Filter by Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Borrowed" {{ request('status') == 'Borrowed' ? 'selected' : '' }}>Borrowed</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        @if(request()->hasAny(['title', 'author', 'status']))
        <div class="mt-2">
            <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times"></i> Clear Filters
            </a>
        </div>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Book List ({{ $books->total() }} books found)</h5>
    </div>
    <div class="card-body">
        @if($books->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Authors</th>
                        <th>Publication Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td>
                            <strong>{{ $book->title }}</strong>
                        </td>
                        <td>
                            @foreach($book->authors as $author)
                            <span class="badge bg-secondary me-1">{{ $author->name }}</span>
                            @endforeach
                        </td>
                        <td>{{ $book->publication_year }}</td>
                        <td>
                            <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas fa-{{ $book->status == 'Available' ? 'check' : 'times' }}"></i>
                                {{ $book->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this book?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $books->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-book fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No books found</h4>
            <p class="text-muted">
                @if(request()->hasAny(['title', 'author', 'status']))
                Try adjusting your search criteria.
                @else
                Start by adding your first book.
                @endif
            </p>
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Book
            </a>
        </div>
        @endif
    </div>
</div>
@endsection