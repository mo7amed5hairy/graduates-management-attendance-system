@extends('layouts.admin')

@section('title', 'المؤسسات التعليمية')
@section('page_title', 'المؤسسات التعليمية')
@section('page_subtitle', 'إدارة الجامعات والمعاهد')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createModal">➕ إضافة مؤسسة</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="table">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>النوع</th>
          <th>نوع الجامعة</th>
          <th>المحافظة</th>
          <th>الأقسام والتخصصات</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($institutions as $inst)
        <tr>
          <td>{{ $inst->id }}</td>
          <td class="font-bold">{{ $inst->name }}</td>
          <td><span class="pill pill-blue">{{ $inst->institutionType->name ?? '—' }}</span></td>
          <td><span class="pill pill-violet">{{ $inst->universityType->name ?? '—' }}</span></td>
          <td>{{ $inst->governorate->name ?? '—' }}</td>
          <td>
            @if($inst->departments->count() > 0)
              <span class="pill pill-amber cursor-pointer" title="{{ $inst->departments->pluck('name')->join('، ') }}">
                {{ $inst->departments->count() }} قسم
              </span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $inst->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editModal{{ $inst->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs" data-delete="{{ route('admin.institutions.destroy', $inst) }}" data-name="{{ $inst->name }}">🗑️</button>
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
  <div class="modal-content p-6" style="max-width:600px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مؤسسة تعليمية</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.institutions.store') }}" method="POST">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="label">الاسم <span class="text-rose-500">*</span></label>
          <input class="input" name="name" required placeholder="اسم الجامعة أو المعهد">
        </div>
        <div>
          <label class="label">النوع <span class="text-rose-500">*</span></label>
          <select class="input" name="institution_type_id" required>
            <option value="">اختر...</option>
            @foreach($institutionTypes as $t)
              <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">نوع الجامعة <span class="text-rose-500">*</span></label>
          <select class="input" name="university_type_id" required>
            <option value="">اختر...</option>
            @foreach($universityTypes as $t)
              <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate_id" required>
            <option value="">اختر...</option>
            @foreach($governorates as $g)
              <option value="{{ $g->id }}">{{ $g->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">الأقسام والتخصصات</label>
          <input class="input" name="departments" placeholder="افصل بينها بفاصلة (,)" dir="rtl">
          <p class="text-xs text-slate-400 mt-1">أدخل الأقسام مفصولة بفاصلة (،)</p>
        </div>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

@foreach($institutions as $inst)
<div class="modal-overlay" id="editModal{{ $inst->id }}">
  <div class="modal-content p-6" style="max-width:600px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $inst->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.institutions.update', $inst) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="label">الاسم <span class="text-rose-500">*</span></label>
          <input class="input" name="name" value="{{ $inst->name }}" required>
        </div>
        <div>
          <label class="label">النوع <span class="text-rose-500">*</span></label>
          <select class="input" name="institution_type_id" required>
            @foreach($institutionTypes as $t)
              <option value="{{ $t->id }}" {{ $inst->institution_type_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">نوع الجامعة <span class="text-rose-500">*</span></label>
          <select class="input" name="university_type_id" required>
            @foreach($universityTypes as $t)
              <option value="{{ $t->id }}" {{ $inst->university_type_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate_id" required>
            @foreach($governorates as $g)
              <option value="{{ $g->id }}" {{ $inst->governorate_id == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">الأقسام والتخصصات</label>
          <input class="input" name="departments" value="{{ $inst->departments->pluck('name')->join('، ') }}" dir="rtl">
          <p class="text-xs text-slate-400 mt-1">أدخل الأقسام مفصولة بفاصلة (،)</p>
        </div>
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
    columnDefs: [{ orderable: false, targets: [7] }]
  });
});
</script>
@endpush
@endsection
