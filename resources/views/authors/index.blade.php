@extends('layouts.app')

@section('title', 'Authors - Library Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-pen"></i> Authors</h1>
    <a href="{{ route('authors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Author
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Author List ({{ $authors->total() }} authors found)</h5>
    </div>
    <div class="card-body">
        @if($authors->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($authors as $author)
                    <tr>
                        <td>
                            <strong>{{ $author->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $author->books_count }} book{{ $author->books_count != 1 ? 's' : '' }}</span>
                        </td>
                        <td>{{ $author->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('authors.show', $author) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($author->books_count == 0)
                                <form action="{{ route('authors.destroy', $author) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this author?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete - author has books">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $authors->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-user-pen fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No authors found</h4>
            <p class="text-muted">Start by adding your first author.</p>
            <a href="{{ route('authors.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Author
            </a>
        </div>
        @endif
    </div>
</div>
@endsection