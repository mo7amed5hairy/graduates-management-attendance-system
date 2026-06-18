@extends('layouts.admin')

@section('title', 'مراجعة خريج')
@section('page_title', 'مراجعة بيانات الخريج')
@section('page_subtitle', $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="card p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
      <div class="flex items-center gap-4 flex-wrap min-w-0">
        @if($user->image)
          <img src="{{ $user->image_url }}" class="avatar avatar-lg shrink-0" style="object-fit:cover">
        @else
          <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white shrink-0">
            {{ substr($user->name, 0, 2) }}
          </div>
        @endif
        <div class="min-w-0">
          <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 break-words">{{ $user->name }}</h2>
          <p class="text-slate-500 text-sm break-words">{{ $user->email }}</p>
        </div>
      </div>
      <div class="flex gap-2 flex-wrap shrink-0">
        @if($user->isPending())
          <button class="btn btn-success text-sm" onclick="approveUser()">✓ اعتماد</button>
          <button class="btn btn-danger text-sm" onclick="showRejectModal()">✕ رفض</button>
        @endif
        <a href="{{ route('admin.graduates.index') }}" class="btn btn-ghost text-sm">⬅ عودة</a>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <span class="text-xs text-slate-400 block">الإسم الرباعى مع اللقب</span>
        <span class="font-semibold">{{ $user->name }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">اسم الأم الرباعى</span>
        <span class="font-semibold">{{ trim($user->mother_name . ' ' . $user->mother_father_name . ' ' . $user->mother_grandfather_name) ?: '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم البطاقة الوطنية</span>
        <span class="font-semibold">{{ $user->national_id ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم الهاتف</span>
        <span class="font-semibold">{{ $user->phone ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ الميلاد</span>
        <span class="font-semibold">{{ $user->date_of_birth ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">العمر</span>
        <span class="font-semibold">{{ $user->age ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الجنس</span>
        <span class="font-semibold">{{ $user->gender ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الاجتماعية</span>
        <span class="font-semibold">{{ $user->social_status ?? '—' }}</span>
        @if(str_contains($user->social_status ?? '', 'لديه أطفال') && $user->children_count !== null)
          <span class="text-xs text-slate-500">عدد الأولاد: {{ $user->children_count }}</span>
        @endif
      </div>
      <div>
        <span class="text-xs text-slate-400 block">التحصيل الدراسى</span>
        <span class="font-semibold">{{ $user->qualification?->name ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الكلية / المعهد</span>
        <span class="font-semibold">{{ $user->qualificationFaculty?->name ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">سنة التخرج</span>
        <span class="font-semibold">{{ $user->graduation_year ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الوظيفية</span>
        <span class="font-semibold">{{ $user->job_status ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">المحافظة</span>
        <span class="font-semibold">{{ $user->governorate_name }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">عنوان السكن الحالى</span>
        <span class="font-semibold">{{ $user->address ?? '—' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">حالة الاعتماد</span>
        @if($user->approval_status === 'approved')
          <span class="pill pill-green">مقبول</span>
          <span class="text-xs text-slate-400 block mt-1">
            بواسطة: {{ $user->approvedBy?->name ?? '—' }}
            في: {{ $user->approved_at?->format('Y/m/d h:i A') ?? '—' }}
          </span>
        @elseif($user->approval_status === 'rejected')
          <span class="pill pill-rose">مرفوض</span>
          @if($user->rejection_reason)
            <div class="text-xs text-rose-600 mt-1">السبب: {{ $user->rejection_reason }}</div>
          @endif
        @else
          <span class="pill pill-amber">قيد المراجعة</span>
        @endif
      </div>
      <div>
        <span class="text-xs text-slate-400 block">إجمالي النقاط</span>
        <span class="font-bold text-lg">{{ number_format($user->points) }}</span>
      </div>
    </div>

    @if($user->id_photos && count($user->id_photos) > 0)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">صور الهوية</span>
      <div class="flex flex-wrap gap-3">
        @foreach($user->id_photos as $photo)
          <a href="{{ asset('storage/' . $photo) }}" target="_blank">
            <img src="{{ asset('storage/' . $photo) }}" class="w-32 h-32 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition">
          </a>
        @endforeach
      </div>
    </div>
    @endif

    @if($user->residence_proof && count($user->residence_proof) > 0)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">إثباتات السكن</span>
      <div class="flex flex-wrap gap-3">
        @foreach($user->residence_proof as $proof)
          <a href="{{ asset('storage/' . $proof) }}" target="_blank">
            <img src="{{ asset('storage/' . $proof) }}" class="w-32 h-32 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition">
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

{{-- Reject Modal --}}
<div class="modal-overlay" id="rejectModal">
  <div class="modal-content">
    <h3 class="text-lg font-extrabold text-slate-900 mb-4">رفض الحساب</h3>
    <form id="rejectForm" data-ajax="true" method="POST">
      @csrf
      <div class="mb-4">
        <label class="label">سبب الرفض</label>
        <textarea class="input" name="reason" rows="3" placeholder="اذكر سبب الرفض..." required></textarea>
      </div>
      <div class="flex gap-2 justify-end">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('rejectModal').classList.remove('active')">إلغاء</button>
        <button type="submit" class="btn btn-danger">✕ تأكيد الرفض</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function approveUser() {
  if (!confirm('هل أنت متأكد من اعتماد هذا الحساب؟')) return;
  fetch('{{ route('admin.graduates.approve', $user) }}', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) { App.toast(d.message); setTimeout(function() { window.location.reload(); }, 1000); }
    else { App.toast(d.message, 'error'); }
  }).catch(function(e) { App.toast('حدث خطأ', 'error'); });
}

function showRejectModal() {
  var form = document.getElementById('rejectForm');
  form.action = '{{ route('admin.graduates.reject', $user) }}';
  document.getElementById('rejectModal').classList.add('active');
}
</script>
@endpush
