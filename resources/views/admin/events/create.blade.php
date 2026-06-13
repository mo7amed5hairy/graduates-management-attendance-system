@extends('layouts.admin')

@section('title', 'إنشاء فعالية')
@section('page_title', 'إنشاء فعالية جديدة')
@section('page_subtitle', 'تعبئة بيانات الفعالية واختيار المشاركين')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6">
    <form id="eventForm" data-ajax="true" action="{{ route('admin.events.store') }}" method="POST">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="label">عنوان الفعالية <span class="text-rose-500">*</span></label>
          <input class="input" name="title" placeholder="عنوان الفعالية" required>
        </div>
        <div>
          <label class="label">نوع الفعالية <span class="text-rose-500">*</span></label>
          <select class="input" name="type" required>
            <option value="">اختر...</option>
            <option value="فعالية">فعالية</option>
            <option value="مظاهرة">مظاهرة</option>
            <option value="احتفال">احتفال</option>
            <option value="ندوة">ندوة</option>
            <option value="مؤتمر">مؤتمر</option>
            <option value="ورشة عمل">ورشة عمل</option>
            <option value="أخرى">أخرى</option>
          </select>
        </div>
        <div>
          <label class="label">تاريخ الفعالية <span class="text-rose-500">*</span></label>
          <input class="input" type="datetime-local" name="event_date" required>
        </div>
        <div>
          <label class="label">المكان</label>
          <input class="input" name="location" placeholder="مكان الفعالية">
        </div>
        <div>
          <label class="label">النقاط لكل مشارك</label>
          <input class="input" type="number" name="points" value="10" min="0">
        </div>
      </div>

      <div class="mt-4">
        <label class="label">وصف الفعالية</label>
        <textarea class="input" name="description" rows="4" placeholder="وصف الفعالية..."></textarea>
      </div>

      <div class="mt-4">
        <label class="label">اختيار المشاركين</label>
        <div class="border border-slate-200 rounded-xl p-3 max-h-60 overflow-y-auto">
          @forelse($graduates as $g)
          <label class="flex items-center gap-2 py-1 hover:bg-slate-50 px-2 rounded cursor-pointer">
            <input type="checkbox" name="participants[]" value="{{ $g->id }}">
            <span class="text-sm">{{ $g->name }}</span>
            <span class="text-xs text-slate-400">({{ $g->national_id ?? $g->email }})</span>
          </label>
          @empty
          <p class="text-sm text-slate-400 text-center py-4">لا يوجد خريجين معتمدين بعد</p>
          @endforelse
        </div>
        <div class="flex gap-2 mt-2">
          <button type="button" class="text-xs text-sky-600 hover:underline" onclick="selectAll(true)">تحديد الكل</button>
          <button type="button" class="text-xs text-sky-600 hover:underline" onclick="selectAll(false)">إلغاء الكل</button>
        </div>
      </div>

      <div class="flex gap-2 mt-6 justify-end">
        <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">إلغاء</a>
        <button type="submit" class="btn btn-success">إنشاء الفعالية</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function selectAll(select) {
  document.querySelectorAll('input[name="participants[]"]').forEach(function(cb) {
    cb.checked = select;
  });
}
</script>
@endpush
