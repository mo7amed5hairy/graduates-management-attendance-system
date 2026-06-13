@extends('layouts.admin')

@section('title', 'أنواع الجامعات')
@section('page_title', 'أنواع الجامعات')
@section('page_subtitle', 'إدارة أنواع الجامعات (حكومية / أهلية)')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createModal">➕ إضافة نوع</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="table">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>عدد المؤسسات</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($types as $type)
        <tr>
          <td>{{ $type->id }}</td>
          <td class="font-bold">{{ $type->name }}</td>
          <td><span class="pill pill-amber">{{ $type->institutions_count }}</span></td>
          <td class="text-xs text-slate-500">{{ $type->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editModal{{ $type->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs" data-delete="{{ route('admin.university-types.destroy', $type) }}" data-name="{{ $type->name }}">🗑️</button>
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
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة نوع</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.university-types.store') }}" method="POST">
      @csrf
      <div>
        <label class="label">الاسم <span class="text-rose-500">*</span></label>
        <input class="input" name="name" required placeholder="مثال: حكومية، أهلية">
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

@foreach($types as $type)
<div class="modal-overlay" id="editModal{{ $type->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $type->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.university-types.update', $type) }}" method="POST">
      @csrf
      @method('PUT')
      <div>
        <label class="label">الاسم <span class="text-rose-500">*</span></label>
        <input class="input" name="name" value="{{ $type->name }}" required>
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
    columnDefs: [{ orderable: false, targets: [4] }]
  });
});
</script>
@endpush
@endsection
