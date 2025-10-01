@extends('layouts.app')

@section('title', 'Book Details - Library Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-book"></i> Book Details</h1>
    <div>
        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit Book
        </a>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Books
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Book Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Title:</dt>
                    <dd class="col-sm-9">
                        <h4>{{ $book->title }}</h4>
                    </dd>

                    <dt class="col-sm-3">Authors:</dt>
                    <dd class="col-sm-9">
                        @foreach($book->authors as $author)
                            <span class="badge bg-primary me-1 fs-6">{{ $author->name }}</span>
                        @endforeach
                    </dd>

                    <dt class="col-sm-3">Publication Year:</dt>
                    <dd class="col-sm-9">{{ $book->publication_year }}</dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }} fs-6">
                            <i class="fas fa-{{ $book->status == 'Available' ? 'check' : 'times' }}"></i>
                            {{ $book->status }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $book->created_at->format('M d, Y \a\t h:i A') }}</dd>

                    <dt class="col-sm-3">Last Updated:</dt>
                    <dd class="col-sm-9">{{ $book->updated_at->format('M d, Y \a\t h:i A') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Book
                    </a>
                    
                    <form action="{{ route('books.destroy', $book) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Book
                        </button>
                    </form>

                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> View All Books
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Book Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $book->authors->count() }}</h4>
                            <small class="text-muted">Authors</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-{{ $book->status == 'Available' ? 'success' : 'danger' }}">
                            {{ $book->status == 'Available' ? '✓' : '✗' }}
                        </h4>
                        <small class="text-muted">Status</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
