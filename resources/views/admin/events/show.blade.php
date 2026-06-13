@extends('layouts.admin')

@section('title', $event->title)
@section('page_title', $event->title)
@section('page_subtitle', 'تفاصيل الفعالية والحضور')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">
    <div class="card p-5 mb-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <span class="text-xs text-slate-400 block">النوع</span>
          <span class="pill pill-violet">{{ $event->type }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">المكان</span>
          <span class="font-semibold">{{ $event->location ?? '—' }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">التاريخ</span>
          <span class="font-semibold">{{ $event->event_date->format('Y/m/d h:i A') }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">النقاط</span>
          <span class="font-bold text-amber-600">{{ number_format($event->points) }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">الحالة</span>
          @if($event->status === 'upcoming')
            <span class="pill pill-blue">قادم</span>
          @elseif($event->status === 'ongoing')
            <span class="pill pill-green">جاري</span>
          @elseif($event->status === 'completed')
            <span class="pill pill-slate">منتهي</span>
          @else
            <span class="pill pill-rose">ملغي</span>
          @endif
        </div>
        <div>
          <span class="text-xs text-slate-400 block">المسجلون</span>
          <span class="font-bold text-lg">{{ $event->attendances_count }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">الحاضرون</span>
          <span class="font-bold text-lg text-green-600">{{ $event->attendees_count }}</span>
        </div>
        <div>
          <span class="text-xs text-slate-400 block">نسبة الحضور</span>
          <span class="font-bold text-lg">
            {{ $event->attendances_count > 0 ? round(($event->attendees_count / $event->attendances_count) * 100) : 0 }}%
          </span>
        </div>
      </div>

      @if($event->description)
      <hr class="my-4 border-slate-100">
      <div>
        <span class="text-xs text-slate-400 block mb-1">الوصف</span>
        <p class="text-sm text-slate-700">{{ $event->description }}</p>
      </div>
      @endif

      <hr class="my-4 border-slate-100">
      <div class="flex gap-2">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">✏️ تعديل</a>
        <a href="{{ route('admin.attendance.event', $event) }}" class="btn btn-success">📋 إدارة الحضور</a>
        <a href="{{ route('admin.attendance.scan', $event) }}" class="btn btn-primary">📱 مسح QR</a>
      </div>
    </div>

    <div class="card p-5">
      <h3 class="font-bold text-slate-900 mb-4">قائمة المسجلين</h3>
      <div class="table-wrap">
        <table class="data" id="attendancesTable">
          <thead>
            <tr>
              <th>#</th>
              <th>الاسم</th>
              <th>البريد</th>
              <th>الرقم القومي</th>
              <th>الحالة</th>
              <th>وقت الحضور</th>
              <th>QR</th>
            </tr>
          </thead>
          <tbody>
            @forelse($event->attendances as $i => $a)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td class="font-bold">{{ $a->user->name }}</td>
              <td class="text-xs">{{ $a->user->email }}</td>
              <td class="text-xs">{{ $a->user->national_id ?? '—' }}</td>
              <td>
                @if($a->isAttended())
                  <span class="pill pill-green">حاضر</span>
                @else
                  <span class="pill pill-amber">لم يحضر</span>
                @endif
              </td>
              <td class="text-xs">{{ $a->attended_at?->format('Y/m/d h:i A') ?? '—' }}</td>
              <td>
                <a href="{{ route('events.qr', $a) }}" target="_blank" class="btn btn-sm btn-primary">📱 QR</a>
              </td>
            </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
  $('#attendancesTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [{ orderable: false, targets: [6] }]
  });
});
</script>
@endpush
