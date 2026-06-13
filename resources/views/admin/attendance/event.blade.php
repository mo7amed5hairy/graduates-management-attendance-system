@extends('layouts.admin')

@section('title', 'حضور ' . $event->title)
@section('page_title', 'تسجيل الحضور')
@section('page_subtitle', $event->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <div class="card p-4 text-center">
    <div class="text-xs text-slate-400">إجمالي المسجلين</div>
    <div class="text-3xl font-extrabold text-slate-900">{{ $event->attendances->count() }}</div>
  </div>
  <div class="card p-4 text-center">
    <div class="text-xs text-slate-400">الحاضرون</div>
    <div class="text-3xl font-extrabold text-green-600">{{ $event->attendees->count() }}</div>
  </div>
  <div class="card p-4 text-center">
    <div class="text-xs text-slate-400">نسبة الحضور</div>
    <div class="text-3xl font-extrabold text-amber-600">
      {{ $event->attendances->count() > 0 ? round(($event->attendees->count() / $event->attendances->count()) * 100) : 0 }}%
    </div>
  </div>
</div>

<div class="card p-5 mb-6">
  <div class="flex gap-2 items-center mb-4">
    <h3 class="font-bold text-slate-900">بحث عن خريج</h3>
    <a href="{{ route('admin.attendance.scan', $event) }}" class="btn btn-sm btn-primary">📱 مسح QR</a>
  </div>
  <div class="flex gap-2">
    <input class="input flex-1" id="graduateSearch" placeholder="ابحث بالاسم أو البريد أو الرقم القومي...">
    <button class="btn btn-primary" onclick="searchGraduate()">🔍 بحث</button>
  </div>
  <div id="searchResults" class="mt-3 hidden"></div>
</div>

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="attendanceTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>الرقم القومي</th>
          <th>الحالة</th>
          <th>وقت الحضور</th>
          <th>الإجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($event->attendances as $i => $a)
        <tr id="attendance-row-{{ $a->id }}">
          <td>{{ $i + 1 }}</td>
          <td class="font-bold">{{ $a->user->name }}</td>
          <td class="text-xs">{{ $a->user->email }}</td>
          <td class="text-xs">{{ $a->user->national_id ?? '—' }}</td>
          <td>
            @if($a->isAttended())
              <span class="pill pill-green">حاضر</span>
            @else
              <span class="pill pill-amber" id="status-{{ $a->id }}">لم يحضر</span>
            @endif
          </td>
          <td class="text-xs" id="time-{{ $a->id }}">{{ $a->attended_at?->format('Y/m/d h:i A') ?? '—' }}</td>
          <td>
            @if(!$a->isAttended())
              <button class="btn btn-sm btn-success" onclick="markAttendance({{ $event->id }}, {{ $a->user_id }}, {{ $a->id }})">✓ تسجيل حضور</button>
            @else
              <span class="text-xs text-green-600">✓ تم</span>
            @endif
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
  $('#attendanceTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [{ orderable: false, targets: [6] }]
  });
});

function markAttendance(eventId, userId, attendanceId) {
  fetch('{{ route('admin.attendance.mark') }}', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ event_id: eventId, user_id: userId })
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) {
      App.toast(d.message);
      document.getElementById('status-' + attendanceId).textContent = 'حاضر';
      document.getElementById('status-' + attendanceId).className = 'pill pill-green';
      document.getElementById('time-' + attendanceId).textContent = new Date().toLocaleString('ar-EG');
      var btn = document.querySelector('#attendance-row-' + attendanceId + ' td:last-child');
      if (btn) btn.innerHTML = '<span class="text-xs text-green-600">✓ تم</span>';
    } else {
      App.toast(d.message, 'error');
    }
  }).catch(function(e) { App.toast('حدث خطأ', 'error'); });
}

function searchGraduate() {
  var q = document.getElementById('graduateSearch').value;
  if (!q) return;
  var resultsDiv = document.getElementById('searchResults');
  resultsDiv.classList.remove('hidden');

  fetch('{{ route('admin.attendance.search') }}?q=' + encodeURIComponent(q), {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.data.length === 0) {
      resultsDiv.innerHTML = '<div class="p-3 text-sm text-slate-400 text-center">لا توجد نتائج</div>';
      return;
    }
    resultsDiv.innerHTML = '<div class="border border-slate-200 rounded-xl overflow-hidden">' +
      '<table class="data w-full"><thead><tr><th>الاسم</th><th>البريد</th><th>الرقم القومي</th><th></th></tr></thead><tbody>' +
      d.data.map(function(u) {
        return '<tr><td class="font-bold">' + u.name + '</td><td class="text-xs">' + u.email + '</td><td class="text-xs">' + (u.national_id || '—') + '</td>' +
          '<td><button class="btn btn-sm btn-success" onclick="markAttendance(' + {{ $event->id }} + ', ' + u.id + ', 0)">✓ تسجيل حضور</button></td></tr>';
      }).join('') +
      '</tbody></table></div>';
  }).catch(function(e) { resultsDiv.innerHTML = '<div class="p-3 text-sm text-red-500">حدث خطأ</div>'; });
}
</script>
@endpush
