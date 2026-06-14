@extends('layouts.admin')

@section('title', 'كليات المؤهلات')
@section('page_title', 'كليات المؤهلات الدراسية')
@section('page_subtitle', 'إدارة الكليات المرتبطة بكل مؤهل دراسي')

@section('content')

<div class="mb-4">
  <button class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">➕ إضافة كلية جديدة</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="facultiesTable">
      <thead>
        <tr>
          <th>#</th>
          <th>اسم الكلية</th>
          <th>المؤهل</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($faculties as $f)
        <tr>
          <td>{{ $f->id }}</td>
          <td class="font-semibold">{{ $f->name }}</td>
          <td><span class="pill pill-blue">{{ $f->qualification->name }}</span></td>
          <td class="text-xs text-slate-500">{{ $f->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs"
                onclick="editFaculty({{ $f->id }}, {{ $f->qualification_id }}, '{{ $f->name }}')">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.qualification-faculties.destroy', $f) }}"
                data-name="{{ $f->name }}">🗑️</button>
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
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة كلية جديدة</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.qualification-faculties.store') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="label">المؤهل <span class="text-rose-500">*</span></label>
        <select class="input" name="qualification_id" required>
          <option value="">اختر المؤهل...</option>
          @foreach($qualifications as $q)
            <option value="{{ $q->id }}">{{ $q->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="label">اسم الكلية <span class="text-rose-500">*</span></label>
        <input class="input" name="name" placeholder="مثال: كلية الطب" required>
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
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل الكلية</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" method="POST" id="editForm">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="label">المؤهل <span class="text-rose-500">*</span></label>
        <select class="input" name="qualification_id" id="editQualificationId" required>
          <option value="">اختر المؤهل...</option>
          @foreach($qualifications as $q)
            <option value="{{ $q->id }}">{{ $q->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="label">اسم الكلية <span class="text-rose-500">*</span></label>
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
function editFaculty(id, qualificationId, name) {
  document.getElementById('editQualificationId').value = qualificationId;
  document.getElementById('editName').value = name;
  document.getElementById('editForm').action = '{{ url('admin/qualification-faculties') }}/' + id;
  document.getElementById('editModal').classList.add('active');
}

$(document).ready(function() {
  $('#facultiesTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [4] }]
  });
});
</script>
@endpush

@endsection
