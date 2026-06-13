@extends('layouts.admin')

@section('title', 'الرئيسية')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'نظرة عامة على نظام الخريجين')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="stat" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
    <div class="l">إجمالي الخريجين</div>
    <div class="v">{{ $stats['total_graduates'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
    <div class="l">قيد المراجعة</div>
    <div class="v">{{ $stats['pending_graduates'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="l">الخريجين المعتمدين</div>
    <div class="v">{{ $stats['approved_graduates'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#ec4899,#be185d)">
    <div class="l">حضور الفعاليات</div>
    <div class="v">{{ $stats['total_attendance'] }}</div>
  </div>
</div>

{{-- Event Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="stat" style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
    <div class="l">إجمالي الفعاليات</div>
    <div class="v">{{ $stats['total_events'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="l">فعاليات قادمة وجارية</div>
    <div class="v">{{ $stats['upcoming_events'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
    <div class="l">إجمالي النقاط الممنوحة</div>
    <div class="v">{{ number_format($stats['total_points']) }}</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Recent Graduates --}}
  <div class="card p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-extrabold text-slate-900">🎓 أحدث الخريجين</h2>
      <a href="{{ route('admin.graduates.index') }}" class="text-xs text-sky-600 hover:underline">عرض الكل</a>
    </div>
    @forelse($recent_graduates as $g)
    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
      <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
        {{ substr($g->name, 0, 2) }}
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-sm font-semibold text-slate-900 truncate">{{ $g->name }}</div>
        <div class="text-xs text-slate-400">{{ $g->university ?? '—' }} · {{ $g->graduation_year ?? '—' }}</div>
      </div>
      @if($g->isPending())
        <span class="pill pill-amber text-xs">قيد المراجعة</span>
      @elseif($g->isApproved())
        <span class="pill pill-green text-xs">معتمد</span>
      @else
        <span class="pill pill-rose text-xs">مرفوض</span>
      @endif
      <a href="{{ route('admin.graduates.show', $g) }}" class="btn btn-sm btn-ghost">🔍</a>
    </div>
    @empty
    <p class="text-sm text-slate-400 text-center py-6">لا يوجد خريجين بعد</p>
    @endforelse
  </div>

  {{-- Top Users by Points --}}
  <div class="card p-5">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">🏆 الأكثر نقاطاً</h2>
    <table class="data w-full" id="topUsersTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الخريج</th>
          <th>النقاط</th>
        </tr>
      </thead>
      <tbody>
        @forelse($top_users as $index => $user)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td class="flex items-center gap-2">
            @if($user->image)
              <img src="{{ $user->image_url }}" class="avatar avatar-sm" style="object-fit:cover">
            @else
              <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
                {{ substr($user->name, 0, 2) }}
              </div>
            @endif
            <span class="font-semibold">{{ $user->name }}</span>
          </td>
          <td><span class="pill pill-amber">{{ number_format($user->points) }} نقطة</span></td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Upcoming Events --}}
  <div class="card p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-extrabold text-slate-900">📅 الفعاليات القادمة</h2>
      <a href="{{ route('admin.events.index') }}" class="text-xs text-sky-600 hover:underline">عرض الكل</a>
    </div>
    @forelse($upcoming_events as $e)
    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
      <div class="text-2xl">📅</div>
      <div class="flex-1 min-w-0">
        <div class="text-sm font-semibold text-slate-900 truncate">{{ $e->title }}</div>
        <div class="text-xs text-slate-400">{{ $e->event_date->format('Y/m/d h:i A') }} · {{ $e->attendances_count }} مسجل</div>
      </div>
      <a href="{{ route('admin.attendance.event', $e) }}" class="btn btn-sm btn-primary">📋 حضور</a>
    </div>
    @empty
    <p class="text-sm text-slate-400 text-center py-6">لا توجد فعاليات قادمة</p>
    @endforelse
  </div>

  {{-- Recent Transactions --}}
  <div class="card p-5">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📋 آخر المعاملات</h2>
    <div class="table-wrap">
      <table class="data" id="recentTransactionsTable">
        <thead>
          <tr>
            <th>الخريج</th>
            <th>النقاط</th>
            <th>النوع</th>
            <th>التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recent_transactions as $t)
          <tr>
            <td class="font-semibold">{{ $t->user->name }}</td>
            <td>{{ $t->points }}</td>
            <td>
              @if($t->type === 'add')
                <span class="pill pill-green">إضافة</span>
              @else
                <span class="pill pill-rose">خصم</span>
              @endif
            </td>
            <td class="text-slate-500 text-xs">{{ $t->created_at->diffForHumans() }}</td>
          </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Quick Actions --}}
<div class="mt-6">
  <h2 class="text-lg font-extrabold text-slate-900 mb-3">إجراءات سريعة</h2>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <a href="{{ route('admin.graduates.index') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-3">
        <span class="text-3xl">🎓</span>
        <span class="pill pill-amber">مراجعة</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">مراجعة الخريجين</div>
      <div class="text-sm text-slate-500 mt-1">اعتماد أو رفض طلبات التسجيل</div>
    </a>
    <a href="{{ route('admin.events.create') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-3">
        <span class="text-3xl">📅</span>
        <span class="pill pill-violet">فعالية</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">إنشاء فعالية</div>
      <div class="text-sm text-slate-500 mt-1">إضافة فعالية واختيار المشاركين</div>
    </a>
    <a href="{{ route('admin.attendance.index') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-3">
        <span class="text-3xl">📋</span>
        <span class="pill pill-green">حضور</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">تسجيل الحضور</div>
      <div class="text-sm text-slate-500 mt-1">مسح QR أو بحث يدوي لتسجيل الحضور</div>
    </a>
    <a href="{{ route('admin.import.index') }}" class="card p-5 hover:shadow-lg transition block">
      <div class="flex items-center justify-between mb-3">
        <span class="text-3xl">📥</span>
        <span class="pill pill-blue">استيراد</span>
      </div>
      <div class="text-lg font-extrabold text-slate-900">استيراد خريجين</div>
      <div class="text-sm text-slate-500 mt-1">رفع ملف Excel أو CSV للخريجين القدامى</div>
    </a>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#topUsersTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    paging: false,
    info: false,
    searching: false,
    order: [[2, 'desc']],
    columnDefs: [{ orderable: false, targets: [0] }]
  });
  $('#recentTransactionsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    paging: false,
    info: false,
    searching: false,
    order: [[3, 'desc']],
    columnDefs: [{ orderable: false, targets: [2] }]
  });
});
</script>
@endpush
