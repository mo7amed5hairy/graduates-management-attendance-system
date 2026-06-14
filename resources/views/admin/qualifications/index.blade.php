@extends('layouts.admin')

@section('title', 'المؤهلات الدراسية')
@section('page_title', 'المؤهلات الدراسية')
@section('page_subtitle', 'إدارة المؤهلات الدراسية التي تظهر في شاشة التسجيل')

@section('content')

<div class="mb-4">
  <button class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">➕ إضافة مؤهل جديد</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="qualificationsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>اسم المؤهل</th>
          <th>عدد الكليات</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($qualifications as $q)
        <tr>
          <td>{{ $q->id }}</td>
          <td class="font-semibold">{{ $q->name }}</td>
          <td><span class="pill pill-amber">{{ $q->faculties_count }}</span></td>
          <td class="text-xs text-slate-500">{{ $q->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" onclick="editQualification({{ $q->id }}, '{{ $q->name }}')">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.qualifications.destroy', $q) }}"
                data-name="{{ $q->name }}">🗑️</button>
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Create Modal --}}
<div class="modal-overlay" id="createModal">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مؤهل جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.qualifications.store') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="label">اسم المؤهل <span class="text-rose-500">*</span></label>
        <input class="input" name="name" placeholder="مثال: بكالوريوس" required>
      </div>
      <div class="flex gap-2 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 إضافة</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل المؤهل</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" method="POST" id="editForm">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="label">اسم المؤهل <span class="text-rose-500">*</span></label>
        <input class="input" name="name" id="editName" required>
      </div>
      <div class="flex gap-2 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-primary">💾 تحديث</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function editQualification(id, name) {
  document.getElementById('editName').value = name;
  document.getElementById('editForm').action = '{{ url('admin/qualifications') }}/' + id;
  document.getElementById('editModal').classList.add('active');
}

$(document).ready(function() {
  $('#qualificationsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [4] }]
  });
});
</script>
@endpush

@endsection
