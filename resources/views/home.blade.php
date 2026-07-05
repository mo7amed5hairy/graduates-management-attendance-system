@extends('layouts.admin')

@section('title', 'الرئيسية')
@section('page_title', 'الصفحة الرئيسية')
@section('page_subtitle')
  مرحباً بعودتك، {{ $user->name }}
@stop

@section('content')

{{-- Approval Status --}}
@if($user->isPending())
  <div class="card p-4 mb-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
    ⏳ حسابك قيد المراجعة. سيتم إشعارك عند الاعتماد.
  </div>
@elseif($user->isRejected())
  <div class="card p-4 mb-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
    ❌ لم يتم اعتماد حسابك. @if($user->rejection_reason) السبب: {{ $user->rejection_reason }} @endif
    <a href="{{ route('profile.edit') }}" class="text-rose-800 font-bold hover:underline">تعديل البيانات</a>
  </div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="stat" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
    <div class="l">نقاطي</div>
    <div class="v">{{ number_format($user->points) }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="l">حالة الحساب</div>
    <div class="v">{{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
    <div class="l">حالة الاعتماد</div>
    <div class="v">{{ $user->isApproved() ? 'معتمد' : ($user->isRejected() ? 'مرفوض' : 'قيد المراجعة') }}</div>
  </div>
</div>

{{-- Task Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="stat" style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
    <div class="l">إجمالي المهام</div>
    <div class="v">{{ $taskStats['total'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="l">المهام المنجزة</div>
    <div class="v">{{ $taskStats['completed'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
    <div class="l">المهام المتبقية</div>
    <div class="v">{{ $taskStats['pending'] }}</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Profile Info --}}
  <div class="card p-5">
    <div class="flex items-center gap-4 mb-4">
      @if($user->image)
        <img src="{{ $user->image_url }}" class="avatar avatar-lg" style="object-fit:cover">
      @else
        <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white">
          {{ substr($user->name, 0, 2) }}
        </div>
      @endif
      <div>
        <h2 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
        <p class="text-sm text-slate-500">{{ $user->email }}</p>
      </div>
    </div>
    <div class="space-y-2 text-sm">
      <div><span class="text-slate-400">الهاتف:</span> <span class="font-semibold">{{ $user->phone ?? 'غير محدد' }}</span></div>
      @if($user->university)
      <div><span class="text-slate-400">الجامعة:</span> <span class="font-semibold">{{ $user->university }}</span></div>
      @endif
      <div><span class="text-slate-400">سنة التخرج:</span> <span class="font-semibold">{{ $user->graduation_year ?? 'غير محدد' }}</span></div>
      @if($user->address)
      <div><span class="text-slate-400">العنوان:</span> <span class="font-semibold">{{ $user->address }}</span></div>
      @endif
      <div><span class="text-slate-400">تاريخ التسجيل:</span> <span class="font-semibold">{{ $user->created_at->format('Y/m/d') }}</span></div>
    </div>
  </div>

  {{-- Recent Transactions --}}
  <div class="card p-5">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📋 آخر معاملات النقاط</h2>
    <div class="table-wrap">
      <table class="data" id="homeTransactionsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>النقاط</th>
            <th>النوع</th>
            <th>السبب</th>
            <th>المهمة/الفعالية</th>
            <th>التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $i => $t)
          <tr>
            <td>{{ $i + 1 }}</td>
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
            <td class="text-xs text-slate-500">{{ $t->created_at->format('Y/m/d') }}</td>
          </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Upcoming Events --}}
  @if($upcomingEvents->count() > 0)
  <div class="card p-5">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📅 الفعاليات القادمة</h2>
    @foreach($upcomingEvents as $a)
    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
      <div class="text-2xl">📅</div>
      <div class="flex-1">
        <div class="text-sm font-semibold text-slate-900">{{ $a->event->title }}</div>
        <div class="text-xs text-slate-400">{{ $a->event->event_date->format('Y/m/d h:i A') }}</div>
      </div>
      <a href="{{ route('events.qr', $a) }}" target="_blank" class="btn btn-sm btn-primary">📱 QR</a>
    </div>
    @endforeach
  </div>
  @endif
</div>

{{-- Quick Actions --}}
<div class="mt-6">
  <h2 class="text-lg font-extrabold text-slate-900 mb-3">إجراءات سريعة</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('tasks.index') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-2">
        <span class="text-3xl">📋</span>
        <span class="pill pill-violet">عرض</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">مهامي</div>
      <div class="text-sm text-slate-500 mt-1">عرض المهام المسندة إليك</div>
    </a>
    <a href="{{ route('events.index') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-2">
        <span class="text-3xl">📅</span>
        <span class="pill pill-amber">فعاليات</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">فعالياتي</div>
      <div class="text-sm text-slate-500 mt-1">عرض الفعاليات المسجل فيها</div>
    </a>
    <a href="{{ route('profile.edit') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-2">
        <span class="text-3xl">✏️</span>
        <span class="pill pill-blue">تعديل</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">تعديل البيانات</div>
      <div class="text-sm text-slate-500 mt-1">تحديث بيانات الخريج والصور</div>
    </a>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#homeTransactionsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[5, 'desc']],
    columnDefs: [{ orderable: false, targets: [3, 4] }]
  });
});
</script>
@endpush
