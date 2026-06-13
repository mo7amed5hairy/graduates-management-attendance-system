@extends('layouts.admin')

@section('title', 'تعديل فعالية')
@section('page_title', 'تعديل الفعالية')
@section('page_subtitle', $event->title)

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6">
    <form id="eventForm" data-ajax="true" action="{{ route('admin.events.update', $event) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="label">عنوان الفعالية <span class="text-rose-500">*</span></label>
          <input class="input" name="title" value="{{ $event->title }}" required>
        </div>
        <div>
          <label class="label">نوع الفعالية <span class="text-rose-500">*</span></label>
          <select class="input" name="type" required>
            <option value="فعالية" {{ $event->type === 'فعالية' ? 'selected' : '' }}>فعالية</option>
            <option value="مظاهرة" {{ $event->type === 'مظاهرة' ? 'selected' : '' }}>مظاهرة</option>
            <option value="احتفال" {{ $event->type === 'احتفال' ? 'selected' : '' }}>احتفال</option>
            <option value="ندوة" {{ $event->type === 'ندوة' ? 'selected' : '' }}>ندوة</option>
            <option value="مؤتمر" {{ $event->type === 'مؤتمر' ? 'selected' : '' }}>مؤتمر</option>
            <option value="ورشة عمل" {{ $event->type === 'ورشة عمل' ? 'selected' : '' }}>ورشة عمل</option>
            <option value="أخرى" {{ $event->type === 'أخرى' ? 'selected' : '' }}>أخرى</option>
          </select>
        </div>
        <div>
          <label class="label">تاريخ الفعالية <span class="text-rose-500">*</span></label>
          <input class="input" type="datetime-local" name="event_date" value="{{ $event->event_date->format('Y-m-d\TH:i') }}" required>
        </div>
        <div>
          <label class="label">المكان</label>
          <input class="input" name="location" value="{{ $event->location }}">
        </div>
        <div>
          <label class="label">النقاط لكل مشارك</label>
          <input class="input" type="number" name="points" value="{{ $event->points }}" min="0">
        </div>
        <div>
          <label class="label">الحالة</label>
          <select class="input" name="status">
            <option value="upcoming" {{ $event->status === 'upcoming' ? 'selected' : '' }}>قادم</option>
            <option value="ongoing" {{ $event->status === 'ongoing' ? 'selected' : '' }}>جاري</option>
            <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>منتهي</option>
            <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
          </select>
        </div>
      </div>

      <div class="mt-4">
        <label class="label">وصف الفعالية</label>
        <textarea class="input" name="description" rows="4">{{ $event->description }}</textarea>
      </div>

      <div class="mt-4">
        <label class="label">اختيار المشاركين</label>
        <div class="border border-slate-200 rounded-xl p-3 max-h-60 overflow-y-auto">
          @forelse($graduates as $g)
          <label class="flex items-center gap-2 py-1 hover:bg-slate-50 px-2 rounded cursor-pointer">
            <input type="checkbox" name="participants[]" value="{{ $g->id }}" {{ in_array($g->id, $participants) ? 'checked' : '' }}>
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
        <button type="submit" class="btn btn-success">حفظ التغييرات</button>
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
