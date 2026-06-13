@extends('layouts.admin')

@section('title', 'الفعاليات')
@section('page_title', 'إدارة الفعاليات')
@section('page_subtitle', 'إنشاء وإدارة الفعاليات والحضور')

@section('content')
<div class="mb-4">
  <a href="{{ route('admin.events.create') }}" class="btn btn-primary">➕ إنشاء فعالية جديدة</a>
</div>

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="eventsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>العنوان</th>
          <th>النوع</th>
          <th>المكان</th>
          <th>التاريخ</th>
          <th>النقاط</th>
          <th>المسجلون</th>
          <th>الحاضرون</th>
          <th>الحالة</th>
          <th>تاريخ الإنشاء</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($events as $i => $e)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td class="font-bold">{{ $e->title }}</td>
          <td><span class="pill pill-violet">{{ $e->type }}</span></td>
          <td>{{ $e->location ?? '—' }}</td>
          <td class="text-xs">{{ $e->event_date->format('Y/m/d h:i A') }}</td>
          <td class="font-bold text-amber-600">{{ number_format($e->points) }}</td>
          <td>{{ $e->attendances_count }}</td>
          <td>{{ $e->attendees_count }}</td>
          <td>
            @if($e->status === 'upcoming')
              <span class="pill pill-blue">قادم</span>
            @elseif($e->status === 'ongoing')
              <span class="pill pill-green">جاري</span>
            @elseif($e->status === 'completed')
              <span class="pill pill-slate">منتهي</span>
            @else
              <span class="pill pill-rose">ملغي</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $e->created_at->format('Y/m/d') }}</td>
          <td class="flex gap-1">
            <a href="{{ route('admin.events.show', $e) }}" class="btn btn-sm btn-primary">🔍</a>
            <a href="{{ route('admin.events.edit', $e) }}" class="btn btn-sm btn-warning">✏️</a>
            <a href="{{ route('admin.attendance.event', $e) }}" class="btn btn-sm btn-success">📋 حضور</a>
            <button class="btn btn-sm btn-danger" onclick="deleteEvent(this)" data-url="{{ route('admin.events.destroy', $e) }}">🗑</button>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
$(function() {
  $('#eventsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [{ orderable: false, targets: [10] }]
  });
});

function deleteEvent(btn) {
  if (!confirm('هل أنت متأكد من حذف هذه الفعالية؟')) return;
  var form = document.getElementById('deleteForm');
  form.action = btn.dataset.url;
  fetch(form.action, {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: new FormData(form)
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) { App.toast(d.message); setTimeout(function() { window.location.reload(); }, 1000); }
  }).catch(function(e) { App.toast('حدث خطأ', 'error'); });
}
</script>
@endpush
