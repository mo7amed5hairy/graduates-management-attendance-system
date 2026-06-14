@extends('layouts.admin')

@section('title', 'البروفايل')
@section('page_title', 'البروفايل الشخصي')
@section('page_subtitle', 'معلومات حسابك')

@section('content')
@if(session('error'))
  <div class="max-w-3xl mx-auto mb-4">
    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">⚠️ {{ session('error') }}</div>
  </div>
@endif
@if(session('success'))
  <div class="max-w-3xl mx-auto mb-4">
    <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
  </div>
@endif

<div class="max-w-3xl mx-auto">
  <div class="card p-6 mb-6">
    <div class="flex items-center gap-5">
      @if($user->image)
        <img src="{{ $user->image_url }}" class="avatar avatar-lg" style="object-fit:cover">
      @else
        <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white">
          {{ substr($user->name, 0, 2) }}
        </div>
      @endif
      <div class="flex-1">
        <h2 class="text-2xl font-extrabold text-slate-900">{{ $user->name }}</h2>
        <div class="flex gap-2 mt-2">
          @if($user->isAdmin())
            <span class="pill pill-violet">مدير</span>
          @else
            <span class="pill pill-blue">خريج</span>
          @endif
          <span class="pill pill-amber">{{ number_format($user->points) }} نقطة</span>
          @if($user->isApproved())
            <span class="pill pill-green">معتمد</span>
          @elseif($user->isRejected())
            <span class="pill pill-rose">مرفوض</span>
          @else
            <span class="pill pill-amber">قيد المراجعة</span>
          @endif
        </div>
      </div>
      @if(!$user->isApproved() && !$user->isAdmin())
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">✏️ تعديل</a>
      @elseif($user->isApproved())
        <span class="text-xs text-slate-400">🔒 البيانات معتمدة ومقفلة</span>
      @endif
    </div>

    <hr class="my-5 border-slate-100">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <span class="text-xs text-slate-400 block">الإسم الرباعى مع اللقب</span>
        <span class="font-semibold">{{ $user->name }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">اسم الأم الرباعى</span>
        <span class="font-semibold">{{ $user->mother_name ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم البطاقة الوطنية</span>
        <span class="font-semibold">{{ $user->national_id ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم الهاتف</span>
        <span class="font-semibold">{{ $user->phone ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ الميلاد</span>
        <span class="font-semibold">{{ $user->date_of_birth ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">العمر</span>
        <span class="font-semibold">{{ $user->age ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الجنس</span>
        <span class="font-semibold">{{ $user->gender ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الاجتماعية</span>
        <span class="font-semibold">{{ $user->social_status ?? 'غير محدد' }}</span>
        @if(str_contains($user->social_status ?? '', 'أطفال') && $user->children_count !== null)
          <span class="text-xs text-slate-500">عدد الأولاد: {{ $user->children_count }}</span>
        @endif
      </div>
      <div>
        <span class="text-xs text-slate-400 block">التحصيل الدراسى</span>
        <span class="font-semibold">{{ $user->qualification?->name ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الكلية / المعهد</span>
        <span class="font-semibold">{{ $user->qualificationFaculty?->name ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">سنة التخرج</span>
        <span class="font-semibold">{{ $user->graduation_year ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الوظيفية</span>
        <span class="font-semibold">{{ $user->job_status ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">المحافظة</span>
        <span class="font-semibold">{{ $user->governorate ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">عنوان السكن الحالى</span>
        <span class="font-semibold">{{ $user->address ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ التسجيل</span>
        <span class="font-semibold">{{ $user->created_at->format('Y/m/d') }}</span>
      </div>
    </div>

    @if($user->rejection_reason)
    <hr class="my-5 border-slate-100">
    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
      <span class="text-xs text-slate-400 block">سبب الرفض</span>
      <span class="font-semibold text-rose-700">{{ $user->rejection_reason }}</span>
    </div>
    @endif

    @if(!$user->isAdmin())
    <hr class="my-5 border-slate-100">
    <div class="bg-sky-50 border border-sky-100 rounded-xl p-4">
      <div class="flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-400 block">رابط بياناتي الشخصية</span>
          <span class="text-sm font-bold text-slate-800" dir="ltr" id="detailsLink">{{ url('/checkmydetails/' . $user->access_token) }}</span>
        </div>
        <button class="btn btn-primary text-sm" onclick="copyLink()">📋 نسخ الرابط</button>
      </div>
    </div>
    @endif

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

    @if($user->social_links)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">روابط التواصل</span>
      <div class="flex flex-wrap gap-2">
        @foreach($user->social_links as $platform => $link)
          @if($link)
          <a href="{{ $link }}" target="_blank" rel="noopener" class="btn btn-ghost text-sm">
            {{ $platform == 'facebook' ? 'فيسبوك' : ($platform == 'twitter' ? 'تويتر' : ($platform == 'instagram' ? 'انستغرام' : ($platform == 'linkedin' ? 'لينكد إن' : $platform))) }}
          </a>
          @endif
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <div class="card p-5">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📋 سجل النقاط</h2>
    <div class="table-wrap">
      <table class="data" id="profileTransactionsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>النقاط</th>
            <th>النوع</th>
            <th>السبب</th>
            <th>المهمة/الفعالية</th>
            <th>بواسطة</th>
            <th>التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $index => $t)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td class="font-bold">{{ $t->points }}</td>
            <td>
              @if($t->type === 'add')
                <span class="pill pill-green">إضافة</span>
              @else
                <span class="pill pill-rose">خصم</span>
              @endif
            </td>
            <td>{{ $t->reason ?? '—' }}</td>
            <td>
              @if($t->task)
                <span class="pill pill-amber text-xs">{{ $t->task->title }}</span>
              @elseif($t->event)
                <span class="pill pill-violet text-xs">{{ $t->event->title }}</span>
              @else
                <span class="text-slate-400">—</span>
              @endif
            </td>
            <td>{{ $t->creator->name }}</td>
            <td class="text-xs text-slate-500">{{ $t->created_at->format('Y/m/d h:i A') }}</td>
          </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function copyLink() {
  var link = document.getElementById('detailsLink');
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(link.textContent).then(function() {
      App.toast('تم نسخ الرابط', 'success');
    });
  } else {
    var range = document.createRange();
    range.selectNode(link);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    App.toast('تم نسخ الرابط', 'success');
  }
}

$(function() {
  $('#profileTransactionsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    columnDefs: [{ targets: [3, 4, 5], orderable: false }],
    order: [[6, 'desc']]
  });
});
</script>
@endpush
