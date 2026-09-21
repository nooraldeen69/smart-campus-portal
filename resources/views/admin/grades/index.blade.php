@extends('admin.layout')
@section('admin_title', 'Grades')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Manage Grades</h5>
    <a href="{{ route('admin.grades.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Post Grade</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light"><tr>
                <th class="ps-4">Student</th><th>University ID</th><th>Course</th><th>Score</th><th>Semester</th><th class="pe-4">Action</th>
            </tr></thead>
            <tbody>
            @forelse($grades as $g)
            @php $badge = in_array($g->score,['A','A-'])?'success':(in_array($g->score,['B+','B','B-'])?'info':(in_array($g->score,['C+','C'])?'warning':'danger')); @endphp
            <tr>
                <td class="ps-4 fw-semibold">{{ $g->user->name ?? 'N/A' }}</td>
                <td class="text-muted small">{{ $g->user->university_id ?? '' }}</td>
                <td>{{ $g->course_name }}</td>
                <td><span class="badge bg-{{ $badge }}">{{ $g->score }}</span></td>
                <td class="text-muted small">{{ $g->semester }}</td>
                <td class="pe-4">
                    <form method="POST" action="{{ route('admin.grades.destroy', $g->id) }}" onsubmit="return confirm('Delete this grade?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No grades posted yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($grades->hasPages())
    <div class="card-footer bg-white border-0 py-3">{{ $grades->links() }}</div>
    @endif
</div>
@endsection
