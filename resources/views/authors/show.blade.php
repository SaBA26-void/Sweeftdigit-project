@extends('layouts.app')

@section('title', 'Author Details - Library Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-pen"></i> Author Details</h1>
    <div>
        <a href="{{ route('authors.edit', $author) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit Author
        </a>
        <a href="{{ route('authors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Authors
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Author Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9">
                        <h4>{{ $author->name }}</h4>
                    </dd>

                    <dt class="col-sm-3">Books Written:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-primary fs-6">{{ $author->books->count() }} book{{ $author->books->count() != 1 ? 's' : '' }}</span>
                    </dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $author->created_at->format('M d, Y \a\t h:i A') }}</dd>

                    <dt class="col-sm-3">Last Updated:</dt>
                    <dd class="col-sm-9">{{ $author->updated_at->format('M d, Y \a\t h:i A') }}</dd>
                </dl>
            </div>
        </div>

        @if($author->books->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Books by {{ $author->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($author->books as $book)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $book->title }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">{{ $book->publication_year }}</small>
                                            <span class="badge {{ $book->status == 'Available' ? 'bg-success' : 'bg-danger' }} ms-2">
                                                {{ $book->status }}
                                            </span>
                                        </p>
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Book
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('authors.edit', $author) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Author
                    </a>
                    
                    @if($author->books->count() == 0)
                        <form action="{{ route('authors.destroy', $author) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this author? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Author
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-trash"></i> Cannot Delete (Has Books)
                        </button>
                    @endif

                    <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> View All Authors
                    </a>

                    <a href="{{ route('books.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Add Book by {{ $author->name }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Author Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $author->books->count() }}</h4>
                            <small class="text-muted">Total Books</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $author->books->where('status', 'Available')->count() }}</h4>
                        <small class="text-muted">Available</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h4 class="text-info">{{ $author->books->where('status', 'Borrowed')->count() }}</h4>
                    <small class="text-muted">Borrowed</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
