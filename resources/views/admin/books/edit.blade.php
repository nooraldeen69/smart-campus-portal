@extends('admin.layout')
@section('admin_title', 'Edit Book')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Edit Book</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:640px">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger py-2 small"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.books.update', $book->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold" for="title">Title</label>
                <input id="title" name="title" class="form-control" value="{{ old('title', $book->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold" for="author">Author</label>
                <input id="author" name="author" class="form-control" value="{{ old('author', $book->author) }}" required>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-8">
                    <label class="form-label fw-semibold" for="isbn">ISBN <span class="text-muted small">(optional)</span></label>
                    <input id="isbn" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}">
                </div>
                <div class="col-4">
                    <label class="form-label fw-semibold" for="available_copies">Copies</label>
                    <input type="number" id="available_copies" name="available_copies" min="0" class="form-control" value="{{ old('available_copies', $book->available_copies) }}" required>
                </div>
            </div>
            <button class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save changes</button>
            <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
