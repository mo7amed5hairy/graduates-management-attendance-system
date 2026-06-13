@extends('layouts.admin')

@section('title', 'الإشعارات')
@section('page_title', '🔔 الإشعارات')
@section('page_subtitle', 'جميع الإشعارات')

@section('content')

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="notificationsTable">
      <thead>
        <tr>
          <th>النوع</th>
          <th>المحتوى</th>
          <th>التاريخ</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($notifications as $notification)
        <tr class="{{ $notification->read_at ? 'opacity-60' : '' }}">
          <td>
            @if($notification->type === 'task_assigned') 📋
            @elseif($notification->type === 'approval') ✅
            @elseif($notification->type === 'rejection') ❌
            @elseif($notification->type === 'event_invitation') 📅
            @else 🔔
            @endif
          </td>
          <td>
            <div class="font-semibold">
              @if($notification->type === 'task_assigned')
                تم تكليفك بمهمة: <a href="{{ route('tasks.show', $notification->data['task_id'] ?? 0) }}" class="text-sky-600 hover:underline" onclick="markRead({{ $notification->id }})">{{ $notification->data['title'] ?? '' }}</a>
              @elseif($notification->type === 'event_invitation')
                دعوة لفعالية: <a href="{{ route('events.index') }}" class="text-sky-600 hover:underline" onclick="markRead({{ $notification->id }})">{{ $notification->data['title'] ?? '' }}</a>
                <div class="text-xs text-slate-400">{{ $notification->data['message'] ?? '' }}</div>
              @else
                <div class="text-sm">{{ $notification->data['message'] ?? 'إشعار' }}</div>
                @if($notification->data['reason'] ?? false)
                  <div class="text-xs text-rose-500 mt-1">{{ $notification->data['reason'] }}</div>
                @endif
              @endif
            </div>
          </td>
          <td class="text-xs">{{ $notification->created_at->diffForHumans() }}</td>
          <td>
            @if(!$notification->read_at)
            <button class="mark-read btn btn-ghost py-1 px-2 text-xs" data-id="{{ $notification->id }}">✓</button>
            @endif
          </td>
        </tr>
        @empty
        <tr><td></td><td></td><td></td><td></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4 flex justify-end">
  @if($notifications->whereNull('read_at')->count() > 0)
  <button class="btn btn-ghost text-sm" id="markAllRead">✅ تحديد الكل كمقروء</button>
  @endif
</div>

@endsection

@push('scripts')
<script>
var notifBase = '{{ route('notifications.index') }}'.replace(/\/+$/, '');
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function markRead(id) {
  fetch(notifBase + '/' + id + '/read', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } }).catch(function(){});
}

$(document).ready(function() {
  $('#notificationsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[2, 'desc']],
    columnDefs: [{ orderable: false, targets: [3] }]
  });
});

document.querySelectorAll('.mark-read').forEach(btn => {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    try {
      const res = await fetch(notifBase + '/' + id + '/read', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } });
      if (res.ok) {
        this.closest('tr').classList.add('opacity-60');
        this.remove();
      }
    } catch(e) {}
  });
});

document.getElementById('markAllRead')?.addEventListener('click', async function() {
  try {
    const res = await fetch(notifBase + '/read-all', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } });
    if (res.ok) {
      document.querySelectorAll('.mark-read').forEach(b => b.click());
      this.remove();
    }
  } catch(e) {}
});
</script>
@endpush
