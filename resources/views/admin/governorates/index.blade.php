@extends('layouts.admin')

@section('title', 'المحافظات')
@section('page_title', 'المحافظات')
@section('page_subtitle', 'إدارة المحافظات')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createModal">➕ إضافة محافظة</button>
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
        @forelse($governorates as $gov)
        <tr>
          <td>{{ $gov->id }}</td>
          <td class="font-bold">{{ $gov->name }}</td>
          <td><span class="pill pill-amber">{{ $gov->institutions_count }}</span></td>
          <td class="text-xs text-slate-500">{{ $gov->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editModal{{ $gov->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs" data-delete="{{ route('admin.governorates.destroy', $gov) }}" data-name="{{ $gov->name }}">🗑️</button>
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
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة محافظة</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.governorates.store') }}" method="POST">
      @csrf
      <div>
        <label class="label">اسم المحافظة <span class="text-rose-500">*</span></label>
        <input class="input" name="name" required placeholder="أدخل اسم المحافظة">
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

@foreach($governorates as $gov)
<div class="modal-overlay" id="editModal{{ $gov->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $gov->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.governorates.update', $gov) }}" method="POST">
      @csrf
      @method('PUT')
      <div>
        <label class="label">اسم المحافظة <span class="text-rose-500">*</span></label>
        <input class="input" name="name" value="{{ $gov->name }}" required>
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
