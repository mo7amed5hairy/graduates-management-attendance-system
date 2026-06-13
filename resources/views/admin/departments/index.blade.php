@extends('layouts.admin')

@section('title', 'الأقسام والتخصصات')
@section('page_title', 'الأقسام والتخصصات')
@section('page_subtitle', 'إدارة الأقسام والتخصصات الدراسية')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createModal">➕ إضافة قسم/تخصص</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="table">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>المؤسسة التعليمية</th>
          <th>المحافظة</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($departments as $dept)
        <tr>
          <td>{{ $dept->id }}</td>
          <td class="font-bold">{{ $dept->name }}</td>
          <td>{{ $dept->institution->name ?? '—' }}</td>
          <td>{{ $dept->institution->governorate->name ?? '—' }}</td>
          <td class="text-xs text-slate-500">{{ $dept->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editModal{{ $dept->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs" data-delete="{{ route('admin.departments.destroy', $dept) }}" data-name="{{ $dept->name }}">🗑️</button>
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="createModal">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة قسم/تخصص</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.departments.store') }}" method="POST">
      @csrf
      <div>
        <label class="label">القسم/التخصص <span class="text-rose-500">*</span></label>
        <input class="input" name="name" required placeholder="اسم القسم أو التخصص">
      </div>
      <div class="mt-4">
        <label class="label">المؤسسة التعليمية <span class="text-rose-500">*</span></label>
        <select class="input" name="institution_id" required>
          <option value="">اختر...</option>
          @foreach($institutions as $inst)
            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->governorate->name ?? '' }})</option>
          @endforeach
        </select>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

@foreach($departments as $dept)
<div class="modal-overlay" id="editModal{{ $dept->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $dept->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.departments.update', $dept) }}" method="POST">
      @csrf
      @method('PUT')
      <div>
        <label class="label">القسم/التخصص <span class="text-rose-500">*</span></label>
        <input class="input" name="name" value="{{ $dept->name }}" required>
      </div>
      <div class="mt-4">
        <label class="label">المؤسسة التعليمية <span class="text-rose-500">*</span></label>
        <select class="input" name="institution_id" required>
          @foreach($institutions as $inst)
            <option value="{{ $inst->id }}" {{ $dept->institution_id == $inst->id ? 'selected' : '' }}>{{ $inst->name }} ({{ $inst->governorate->name ?? '' }})</option>
          @endforeach
        </select>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-primary">💾 تحديث</button>
      </div>
    </form>
  </div>
</div>
@endforeach

@push('scripts')
<script>
$(document).ready(function() {
  $('#table').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [5] }]
  });
});
</script>
@endpush
@endsection
