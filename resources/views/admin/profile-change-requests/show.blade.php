@extends('layouts.admin')

@section('title', 'تفاصيل طلب تعديل')
@section('page_title', 'تفاصيل طلب تعديل البيانات')
@section('page_subtitle', 'مراجعة بيانات التعديل والمرفقات')

@section('content')

{{-- Status message at the top --}}
@if($changeRequest->status !== 'pending')
<div class="card p-5 mb-6">
  <div class="flex items-center gap-3">
    @if($changeRequest->status === 'approved')
      <span class="text-green-600 text-lg">✅</span>
      <h3 class="font-extrabold text-green-700">تم البت في هذا الطلب — تمت الموافقة</h3>
    @else
      <span class="text-rose-600 text-lg">❌</span>
      <h3 class="font-extrabold text-rose-700">تم البت في هذا الطلب — تم الرفض</h3>
    @endif
  </div>
  @if($changeRequest->admin_notes)
    <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-600 mt-3">
      <span class="text-xs text-slate-400 block mb-1">💬 ملاحظات المشرف</span>
      {{ $changeRequest->admin_notes }}
    </div>
  @endif
</div>
@endif

{{-- Success/error flash messages --}}
@if(session('success'))
  <div class="card p-4 mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg" id="flashSuccess">
    <div class="flex items-center gap-2">
      <span>✅</span>
      <span class="font-semibold">{{ session('success') }}</span>
      <button onclick="this.parentElement.parentElement.remove()" class="mr-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
  </div>
@endif
@if(session('error'))
  <div class="card p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg" id="flashError">
    <div class="flex items-center gap-2">
      <span>❌</span>
      <span class="font-semibold">{{ session('error') }}</span>
      <button onclick="this.parentElement.parentElement.remove()" class="mr-auto text-rose-500 hover:text-rose-700">&times;</button>
    </div>
  </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- User info --}}
  <div class="card p-5">
    <h3 class="font-extrabold text-slate-800 mb-4">👤 المستخدم</h3>
    <div class="space-y-2 text-sm">
      <p><span class="text-slate-500">الاسم:</span> <span class="font-semibold">{{ $changeRequest->user->name }}</span></p>
      <p><span class="text-slate-500">البريد:</span> {{ $changeRequest->user->email }}</p>
      <p><span class="text-slate-500">الهاتف:</span> {{ $changeRequest->user->phone ?? '—' }}</p>
      <p><span class="text-slate-500">الرقم القومي:</span> {{ $changeRequest->user->national_id ?? '—' }}</p>
      <p><span class="text-slate-500">تاريخ الطلب:</span> {{ $changeRequest->created_at->format('Y/m/d H:i') }}</p>
      <p>
        <span class="text-slate-500">الحالة:</span>
        @if($changeRequest->status === 'pending')
          <span class="pill pill-amber">قيد المراجعة</span>
        @elseif($changeRequest->status === 'approved')
          <span class="pill pill-green">مقبول</span>
        @else
          <span class="pill pill-rose">مرفوض</span>
        @endif
      </p>
      @if($changeRequest->reviewer)
        <p><span class="text-slate-500">تمت المراجعة بواسطة:</span> {{ $changeRequest->reviewer->name }}</p>
      @endif
      @if($changeRequest->reviewed_at)
        <p><span class="text-slate-500">تاريخ المراجعة:</span> {{ $changeRequest->reviewed_at->format('Y/m/d H:i') }}</p>
      @endif
    </div>
  </div>

  {{-- Data diff --}}
  <div class="card p-5 lg:col-span-2">
    <h3 class="font-extrabold text-slate-800 mb-4">📋 التعديلات المطلوبة</h3>
    @if($changeRequest->requested_data)
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200">
              <th class="text-right py-2 px-3 text-slate-500">الحقل</th>
              <th class="text-right py-2 px-3 text-slate-500">القيمة الحالية</th>
              <th class="text-right py-2 px-3 text-slate-500">القيمة الجديدة</th>
            </tr>
          </thead>
          <tbody>
            @php $fieldLabels = [
              'first_name' => 'الإسم', 'father_name' => 'اسم الأب', 'grandfather_name' => 'اسم الجد',
              'family_name' => 'اللقب', 'mother_name' => 'اسم الأم', 'mother_father_name' => 'أب الأم',
              'mother_grandfather_name' => 'جد الأم', 'email' => 'البريد', 'phone' => 'الهاتف',
              'national_id' => 'الرقم القومي', 'date_of_birth' => 'سنة الميلاد', 'gender' => 'الجنس',
              'job_status' => 'الحالة الوظيفية', 'social_status' => 'الحالة الاجتماعية',
              'children_count' => 'عدد الأولاد', 'governorate' => 'المحافظة', 'address' => 'العنوان',
              'graduation_year' => 'سنة التخرج', 'qualification_id' => 'المؤهل',
              'qualification_faculty_id' => 'الكلية',
            ]; @endphp
            @foreach($changeRequest->requested_data as $field => $newValue)
              @if(in_array($field, ['_new_image', '_new_id_photos'])) @continue @endif
              <tr class="border-b border-slate-100">
                <td class="py-2 px-3 font-semibold">{{ $fieldLabels[$field] ?? $field }}</td>
                <td class="py-2 px-3 text-slate-400">
                  @if(is_array($changeRequest->user->$field ?? null))
                    {{ json_encode($changeRequest->user->$field, JSON_UNESCAPED_UNICODE) }}
                  @else
                    {{ $changeRequest->user->$field ?? '—' }}
                  @endif
                </td>
                <td class="py-2 px-3 text-emerald-600 font-semibold">
                  @if(is_array($newValue))
                    @if($field === 'social_links')
                      <div class="text-xs space-y-1">
                        @foreach($newValue as $platform => $link)
                          @if($link)
                            <div>{{ $platform }}: <a href="{{ $link }}" target="_blank" class="text-sky-600 hover:underline">{{ $link }}</a></div>
                          @endif
                        @endforeach
                      </div>
                    @else
                      {{ json_encode($newValue, JSON_UNESCAPED_UNICODE) }}
                    @endif
                  @else
                    {{ $newValue ?? '—' }}
                  @endif
                </td>
              </tr>
            @endforeach
            @if(isset($changeRequest->requested_data['_new_image']))
              <tr class="border-b border-slate-100">
                <td class="py-2 px-3 font-semibold">الصورة الشخصية</td>
                <td class="py-2 px-3 text-slate-400">
                  @if($changeRequest->user->image)
                    <img src="{{ $changeRequest->user->image_url }}" class="w-16 h-16 rounded-full object-cover">
                  @else
                    —
                  @endif
                </td>
                <td class="py-2 px-3">
                  <img src="{{ route('file.serve', $changeRequest->requested_data['_new_image']) }}" class="w-16 h-16 rounded-full object-cover">
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    @else
      <p class="text-slate-400">لا توجد تعديلات في البيانات (مرفقات فقط)</p>
    @endif
  </div>

  {{-- National ID Photos --}}
  @if(isset($changeRequest->requested_data['_new_id_photos']))
  <div class="card p-5 lg:col-span-3">
    <h3 class="font-extrabold text-slate-800 mb-4">🪪 صور البطاقة الوطنية</h3>
    <div class="flex flex-wrap gap-4">
      @foreach($changeRequest->requested_data['_new_id_photos'] as $side => $path)
        <div class="border border-slate-200 rounded-lg p-3 text-center">
          <div class="text-xs text-slate-500 mb-2 font-semibold">{{ $side === 'front' ? 'الوجه الأمامي' : 'الوجه الخلفي' }}</div>
          <img src="{{ route('file.serve', $path) }}" class="w-48 h-32 object-contain rounded mb-2 cursor-pointer hover:opacity-80 transition" onclick="openZoom('{{ route('file.serve', $path) }}')">
          <a href="{{ route('file.serve', $path) }}" target="_blank" class="text-xs text-sky-600">عرض في نافذة جديدة</a>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Attachments (old-style from attachments table) --}}
  @if($changeRequest->attachments->count() > 0)
  <div class="card p-5 lg:col-span-3">
    <h3 class="font-extrabold text-slate-800 mb-4">📎 المرفقات ({{ $changeRequest->attachments->count() }})</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      @foreach($changeRequest->attachments as $att)
        <div class="border border-slate-200 rounded-lg p-3 text-center">
          @if(str_starts_with($att->mime_type, 'image/'))
            <img src="{{ route('file.serve', $att->file_path) }}" class="w-full h-24 object-cover rounded mb-2">
          @else
            <div class="w-full h-24 flex items-center justify-center bg-slate-50 rounded mb-2 text-3xl">📄</div>
          @endif
          <div class="text-xs text-slate-500 truncate" title="{{ $att->original_name }}">{{ $att->original_name }}</div>
          <a href="{{ route('file.serve', $att->file_path) }}" target="_blank" class="text-xs text-sky-600">عرض</a>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- New-style graduation attachments (stored in requested_data) --}}
  @if(isset($changeRequest->requested_data['_new_graduation_attachments']) && count($changeRequest->requested_data['_new_graduation_attachments']) > 0)
  <div class="card p-5 lg:col-span-3">
    <h3 class="font-extrabold text-slate-800 mb-4">📎 مرفق التخرج الجديد (يحل محل القديم)</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      @foreach($changeRequest->requested_data['_new_graduation_attachments'] as $att)
        <div class="border border-slate-200 rounded-lg p-3 text-center">
          @if(str_starts_with($att['mime_type'] ?? '', 'image/'))
            <img src="{{ route('file.serve', $att['file_path']) }}" class="w-full h-24 object-cover rounded mb-2">
          @else
            <div class="w-full h-24 flex items-center justify-center bg-slate-50 rounded mb-2 text-3xl">📄</div>
          @endif
          <div class="text-xs text-slate-500 truncate" title="{{ $att['original_name'] }}">{{ $att['original_name'] ?? 'ملف' }}</div>
          <a href="{{ route('file.serve', $att['file_path']) }}" target="_blank" class="text-xs text-sky-600">عرض</a>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Admin actions --}}
  @if($changeRequest->status === 'pending')
  <div class="card p-5 lg:col-span-3">
    <h3 class="font-extrabold text-slate-800 mb-4">⚙️ إجراءات المراجعة</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Approve form --}}
      <form id="approveForm" class="pcr-form">
        @csrf
        <div class="mb-3">
          <label class="label">ملاحظات <span class="text-rose-500">*</span></label>
          <textarea class="input" name="admin_notes" rows="2" placeholder="ملاحظات على الموافقة" required></textarea>
        </div>
        <button type="submit" class="btn btn-success w-full" id="approveBtn">✅ قبول الطلب</button>
      </form>

      {{-- Reject form --}}
      <form id="rejectForm">
        @csrf
        <div class="mb-3">
          <label class="label">سبب الرفض <span class="text-rose-500">*</span></label>
          <textarea class="input" name="admin_notes" rows="2" placeholder="اذكر سبب الرفض" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger w-full" id="rejectBtn">❌ رفض الطلب</button>
      </form>
    </div>
  </div>
  @endif
</div>

<div class="mt-4">
  <a href="{{ route('admin.profile-change-requests.index') }}" class="btn btn-ghost">← العودة للقائمة</a>
</div>

{{-- Zoom Modal --}}
<div id="zoomModal" class="fixed inset-0 z-[9999] bg-black/80 hidden items-center justify-center p-4" onclick="closeZoom(event)">
  <button class="absolute top-4 left-4 text-white text-3xl hover:text-slate-300 z-10" onclick="closeZoom()">&times;</button>
  <img id="zoomImg" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" onclick="event.stopPropagation()">
</div>

@push('scripts')
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Show toast on success flash
(function() {
  var flash = document.getElementById('flashSuccess') || document.getElementById('flashError');
  if (flash) {
    var msg = flash.querySelector('span.font-semibold')?.textContent || '';
    if (App.toast) App.toast(msg, flash.id === 'flashSuccess' ? 'success' : 'error');
    setTimeout(function() { flash.style.display = 'none'; }, 5000);
  }
})();

function openZoom(src) {
  document.getElementById('zoomImg').src = src;
  document.getElementById('zoomModal').classList.remove('hidden');
  document.getElementById('zoomModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeZoom(e) {
  if (e && e.target !== document.getElementById('zoomImg')) {
    document.getElementById('zoomModal').classList.add('hidden');
    document.getElementById('zoomModal').style.display = 'none';
    document.body.style.overflow = '';
  } else if (!e) {
    document.getElementById('zoomModal').classList.add('hidden');
    document.getElementById('zoomModal').style.display = 'none';
    document.body.style.overflow = '';
  }
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeZoom();
});

// AJAX Approve
document.getElementById('approveForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('approveBtn');
  var notes = this.querySelector('[name="admin_notes"]').value.trim();
  if (!notes) return;
  btn.disabled = true;
  btn.textContent = '⏳ جاري القبول...';
  fetch('{{ route("admin.profile-change-requests.approve", $changeRequest) }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ admin_notes: notes })
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      App.toast ? App.toast('✅ ' + d.message) : alert(d.message);
      window.location.href = '{{ route("admin.profile-change-requests.index") }}';
    } else {
      alert(d.message || 'حدث خطأ');
      btn.disabled = false;
      btn.textContent = '✅ قبول الطلب';
    }
  })
  .catch(function() {
    alert('حدث خطأ في الاتصال');
    btn.disabled = false;
    btn.textContent = '✅ قبول الطلب';
  });
});

// AJAX Reject
document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('rejectBtn');
  var notes = this.querySelector('[name="admin_notes"]').value.trim();
  if (!notes) return;
  btn.disabled = true;
  btn.textContent = '⏳ جاري الرفض...';
  fetch('{{ route("admin.profile-change-requests.reject", $changeRequest) }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ admin_notes: notes })
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      App.toast ? App.toast(d.message) : alert(d.message);
      window.location.href = '{{ route("admin.profile-change-requests.index") }}';
    } else {
      alert(d.message || 'حدث خطأ');
      btn.disabled = false;
      btn.textContent = '❌ رفض الطلب';
    }
  })
  .catch(function() {
    alert('حدث خطأ في الاتصال');
    btn.disabled = false;
    btn.textContent = '❌ رفض الطلب';
  });
});
</script>
@endpush
@endsection
