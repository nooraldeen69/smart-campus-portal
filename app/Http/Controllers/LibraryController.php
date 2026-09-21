<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    private const MAX_LOANS = 3;
    private const LOAN_DAYS = 14;

    public function index()
    {
        $books = LibraryBook::orderBy('title')->get();
        $loans = LibraryLoan::with('book')->where('user_id', Auth::id())->whereNull('returned_at')->orderBy('due_date')->get();

        return view('library.index', compact('books', 'loans'));
    }

    public function borrow(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:library_books,id']);
        $userId = Auth::id();

        $result = DB::transaction(function () use ($request, $userId) {
            $active = LibraryLoan::where('user_id', $userId)->whereNull('returned_at')->get();

            if ($active->contains(fn ($l) => Carbon::parse($l->due_date)->endOfDay()->isPast())) {
                return ['error', 'Please return your overdue book(s) before borrowing another.'];
            }
            if ($active->count() >= self::MAX_LOANS) {
                return ['error', 'Loan limit reached ('.self::MAX_LOANS.' books at a time).'];
            }
            if ($active->contains('library_book_id', (int) $request->book_id)) {
                return ['error', 'You already have this book on loan.'];
            }

            // Atomic: only succeeds while a copy is really available (no two students can take the last copy)
            $taken = LibraryBook::whereKey($request->book_id)->where('available_copies', '>', 0)->decrement('available_copies');
            if (! $taken) {
                return ['error', 'No copies available right now.'];
            }

            LibraryLoan::create([
                'user_id'         => $userId,
                'library_book_id' => (int) $request->book_id,
                'due_date'        => Carbon::now()->addDays(self::LOAN_DAYS)->toDateString(),
            ]);

            return ['status', 'Book borrowed successfully! Due in '.self::LOAN_DAYS.' days.'];
        });

        return back()->with($result[0], $result[1]);
    }

    public function returnBook(Request $request)
    {
        $request->validate(['loan_id' => 'required|exists:library_loans,id']);

        $result = DB::transaction(function () use ($request) {
            // Only your own loans, and only while still open (atomic, so a double-click cannot add two copies)
            $closed = LibraryLoan::where('id', $request->loan_id)->where('user_id', Auth::id())->whereNull('returned_at')
                ->update(['returned_at' => Carbon::now()->toDateString()]);

            if (! $closed) {
                return LibraryLoan::where('id', $request->loan_id)->where('user_id', Auth::id())->exists()
                    ? ['error', 'Book is already returned.']
                    : null;
            }

            LibraryBook::whereKey(LibraryLoan::find($request->loan_id)->library_book_id)->increment('available_copies');

            return ['status', 'Book returned successfully!'];
        });

        abort_if($result === null, 404);

        return back()->with($result[0], $result[1]);
    }
}
