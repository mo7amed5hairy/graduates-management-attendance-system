@extends('layouts.admin')

@section('title', 'فعالياتي')
@section('page_title', 'الفعاليات المسجل فيها')
@section('page_subtitle', 'عرض الفعاليات التي تمت دعوتك إليها')

@section('content')
@if(session('success'))
  <div class="card p-4 mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">{{ session('success') }}</div>
@endif

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="eventsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الفعالية</th>
          <th>النوع</th>
          <th>المكان</th>
          <th>التاريخ</th>
          <th>النقاط</th>
          <th>حالة الحضور</th>
          <th>QR</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attendances as $i => $a)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td class="font-bold">{{ $a->event->title }}</td>
          <td><span class="pill pill-violet">{{ $a->event->type }}</span></td>
          <td>{{ $a->event->location ?? '—' }}</td>
          <td class="text-xs">{{ $a->event->event_date->format('Y/m/d h:i A') }}</td>
          <td class="font-bold text-amber-600">{{ number_format($a->points_awarded) }}</td>
          <td>
            @if($a->isAttended())
              <span class="pill pill-green">تم الحضور</span>
            @else
              <span class="pill pill-amber">لم يحضر بعد</span>
            @endif
          </td>
          <td>
            <a href="{{ route('events.qr', $a) }}" target="_blank" class="btn btn-sm btn-primary">📱 عرض QR</a>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
  $('#eventsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [{ orderable: false, targets: [7] }]
  });
});
</script>
@endpush
