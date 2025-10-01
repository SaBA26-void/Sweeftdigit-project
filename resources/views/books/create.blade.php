@extends('layouts.app')

@section('title', 'Add New Book - Library Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-plus"></i> Add New Book</h1>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Books
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Book Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('books.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="publication_year" class="form-label">Publication Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('publication_year') is-invalid @enderror" 
                               id="publication_year" name="publication_year" 
                               value="{{ old('publication_year') }}" 
                               min="1000" max="{{ date('Y') }}" required>
                        @error('publication_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Borrowed" {{ old('status') == 'Borrowed' ? 'selected' : '' }}>Borrowed</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="authors" class="form-label">Authors <span class="text-danger">*</span></label>
                <div class="form-control @error('authors') is-invalid @enderror" style="min-height: 150px; max-height: 200px; overflow-y: auto;">
                    @foreach($authors as $author)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="authors[]" value="{{ $author->id }}" id="author{{ $author->id }}"
                                   {{ in_array($author->id, old('authors', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="author{{ $author->id }}">
                                {{ $author->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('authors')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Select one or more authors. If you need to add a new author, 
                    <a href="{{ route('authors.create') }}" target="_blank">click here</a>.
                </small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Book
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
