@extends('admin.layout')
@section('admin_title', 'Library Books')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Manage Library Books</h5>
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Book</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light"><tr>
                <th class="ps-4">Title</th><th>Author</th><th>ISBN</th><th>Available</th><th>On loan</th><th class="pe-4">Actions</th>
            </tr></thead>
            <tbody>
            @forelse($books as $b)
            <tr>
                <td class="ps-4 fw-semibold text-break">{{ $b->title }}</td>
                <td class="text-muted small">{{ $b->author }}</td>
                <td class="text-muted small">{{ $b->isbn ?: '—' }}</td>
                <td><span class="badge {{ $b->available_copies > 0 ? 'bg-success' : 'bg-danger' }}">{{ $b->available_copies }}</span></td>
                <td><span class="badge bg-light text-dark border">{{ $b->active_loans_count ?? 0 }}</span></td>
                <td class="pe-4 text-nowrap">
                    <a href="{{ route('admin.books.edit', $b->id) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.books.destroy', $b->id) }}" class="d-inline" onsubmit="return confirm('Delete this book and its loan history?')">
                        @csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No books in the catalog.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
