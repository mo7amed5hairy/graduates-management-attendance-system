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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
          <input class="input" id="participantSearch" placeholder="🔍 بحث باسم المستخدم..." oninput="filterParticipants()">
          <select class="input" id="qualFilter" onchange="filterParticipants()">
            <option value="">كل المؤهلات</option>
            @foreach($qualifications as $q)
              <option value="{{ $q->name }}">{{ $q->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <span class="text-xs text-slate-500" id="participantCount">{{ count($graduates) }} مشارك</span>
          <button type="button" class="text-xs text-sky-600 hover:underline" onclick="selectAll(true)">تحديد الكل</button>
          <button type="button" class="text-xs text-sky-600 hover:underline" onclick="selectAll(false)">إلغاء الكل</button>
        </div>
        <div class="border border-slate-200 rounded-xl p-3 max-h-60 overflow-y-auto" id="participantList">
          @forelse($graduates as $g)
          <label class="flex items-center gap-2 py-1 hover:bg-slate-50 px-2 rounded cursor-pointer participant-item" data-name="{{ $g->name }}" data-qual="{{ $g->qualification?->name ?? '' }}">
            <input type="checkbox" name="participants[]" value="{{ $g->id }}">
            <span class="text-sm participant-name">{{ $g->name }}</span>
            <span class="text-xs text-slate-400">{{ $g->national_id ? '(' . $g->national_id . ')' : '' }}</span>
            @if($g->qualification)
              <span class="text-xs text-sky-500 mr-auto">{{ $g->qualification->name }}</span>
            @endif
          </label>
          @empty
          <p class="text-sm text-slate-400 text-center py-4">لا يوجد خريجين معتمدين بعد</p>
          @endforelse
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
function filterParticipants() {
  var search = document.getElementById('participantSearch').value.trim().toLowerCase();
  var qual = document.getElementById('qualFilter').value;
  var items = document.querySelectorAll('.participant-item');
  var visible = 0;
  items.forEach(function(item) {
    var name = item.getAttribute('data-name').toLowerCase();
    var itemQual = item.getAttribute('data-qual');
    var match = true;
    if (search && name.indexOf(search) === -1) match = false;
    if (qual && itemQual !== qual) match = false;
    item.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('participantCount').textContent = visible + ' مشارك';
}

function selectAll(select) {
  document.querySelectorAll('.participant-item:not([style*="display: none"]) input[type="checkbox"]').forEach(function(cb) {
    cb.checked = select;
  });
}
</script>
@endpush
