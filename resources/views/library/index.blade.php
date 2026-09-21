@extends('layouts.app')

@section('title', 'Library - Smart Campus Portal')

@section('content')
@php use Carbon\Carbon; @endphp

<div class="container-fluid pt-2 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-book text-secondary me-2"></i>Library</h2>
            <p class="text-muted mb-0">Browse the catalog and manage your loans</p>
        </div>
    </div>

    {{-- Active Loans --}}
    <div class="card shadow-sm border-0 rounded-3 mb-5">
        <div class="card-header border-0 bg-white pt-3 pb-0 px-4">
            <h5 class="fw-bold mb-0 pb-2 border-bottom"><i class="bi bi-bookmark-check-fill text-warning me-2"></i>My Active Loans</h5>
        </div>
        <div class="card-body p-0">
            @if($loans->count() > 0)
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Book Title</th>
                            <th>Author</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        @php
                            $isOverdue = Carbon::parse($loan->due_date)->isPast();
                            $isSoon = !$isOverdue && Carbon::parse($loan->due_date)->diffInDays(now()) <= 3;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $loan->book->title }}</td>
                            <td class="text-muted small">{{ $loan->book->author }}</td>
                            <td class="small {{ $isOverdue ? 'text-danger fw-bold' : ($isSoon ? 'text-warning fw-bold' : '') }}">
                                {{ Carbon::parse($loan->due_date)->format('M j, Y') }}
                            </td>
                            <td>
                                @if($isOverdue)
                                    <span class="badge bg-danger">Overdue</span>
                                @elseif($isSoon)
                                    <span class="badge bg-warning text-dark">Due Soon</span>
                                @else
                                    <span class="badge bg-success">On Loan</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <form method="POST" action="{{ route('library.returnBook') }}">
                                    @csrf
                                    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-arrow-return-left me-1"></i>Return
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted p-4 mb-0">You have no active loans.</p>
            @endif
        </div>
    </div>

    {{-- Book Catalog --}}
    <div class="mb-3">
        <h4 class="fw-bold"><i class="bi bi-journals me-2 text-primary"></i>Book Catalog</h4>
        <p class="text-muted small">{{ $books->count() }} books in the library. Click "Borrow" to check out a book (14-day loan).</p>
    </div>

    <div class="row g-4">
        @foreach($books as $book)
        @php
            $alreadyBorrowed = $loans->where('library_book_id', $book->id)->count() > 0;
        @endphp
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:60px;font-size:1.4rem">📕</div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="fw-bold mb-1 lh-sm">{{ $book->title }}</h6>
                            <p class="text-muted small mb-1"><i class="bi bi-person me-1"></i>{{ $book->author }}</p>
                            @if($book->isbn)
                                <p class="text-muted" style="font-size:.75rem"><i class="bi bi-upc me-1"></i>{{ $book->isbn }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center px-3 pb-3">
                    <span class="{{ $book->available_copies > 0 ? 'text-success' : 'text-danger' }} small fw-semibold">
                        <i class="bi bi-stack me-1"></i>{{ $book->available_copies }} available
                    </span>
                    @if($alreadyBorrowed)
                        <span class="btn btn-sm btn-outline-secondary disabled">On Loan</span>
                    @elseif($book->available_copies > 0)
                        <form method="POST" action="{{ route('library.borrow') }}" class="m-0">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-box-arrow-in-down me-1"></i>Borrow
                            </button>
                        </form>
                    @else
                        <span class="btn btn-sm btn-outline-danger disabled">Unavailable</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
