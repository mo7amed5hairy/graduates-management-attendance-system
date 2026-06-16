@extends('layouts.admin')

@section('title', 'طلبات تعديل البيانات')
@section('page_title', 'طلبات تعديل البيانات')
@section('page_subtitle', 'مراجعة طلبات تعديل البيانات ورفع المرفقات')

@section('content')

<div class="mb-4 flex items-center gap-3">
  <div id="bulkActions" class="flex items-center gap-2" style="display:none">
    <span class="text-sm text-slate-500" id="selectedCount">0</span>
    <span class="text-sm text-slate-400">محدد</span>
    <button class="btn btn-success text-sm" onclick="bulkApprove()">✅ قبول الجميع</button>
    <button class="btn btn-ghost text-sm" onclick="clearAllCheckboxes()">إلغاء التحديد</button>
  </div>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="changeRequestsTable">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
          <th>#</th>
          <th>المستخدم</th>
          <th>نوع الطلب</th>
          <th>المرفقات</th>
          <th>الحالة</th>
          <th>تاريخ التقديم</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $r)
        <tr>
          <td><input type="checkbox" class="request-checkbox" value="{{ $r->id }}" onchange="updateBulkActions()" {{ $r->status !== 'pending' ? 'disabled' : '' }}></td>
          <td>{{ $r->id }}</td>
          <td class="font-semibold">{{ $r->user->name }}</td>
          <td>
            @if($r->requested_data && $r->attachments->count() > 0)
              <span class="pill pill-amber">تعديل + مرفقات</span>
            @elseif($r->requested_data)
              <span class="pill pill-blue">تعديل بيانات</span>
            @else
              <span class="pill pill-violet">رفع مرفقات</span>
            @endif
          </td>
          <td>{{ $r->attachments->count() ? $r->attachments->count() . ' ملف' : '—' }}</td>
          <td>
            @if($r->status === 'pending')
              <span class="pill pill-amber">قيد المراجعة</span>
            @elseif($r->status === 'approved')
              <span class="pill pill-green">مقبول</span>
            @else
              <span class="pill pill-rose">مرفوض</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $r->created_at->format('Y/m/d H:i') }}</td>
          <td>
            <a href="{{ route('admin.profile-change-requests.show', $r) }}" class="btn btn-sm btn-primary">🔍 عرض</a>
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
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

$(function() {
  $('#changeRequestsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[1, 'desc']],
    columnDefs: [{ orderable: false, targets: [0, 7] }]
  });
});

function toggleSelectAll(source) {
  document.querySelectorAll('.request-checkbox:not(:disabled)').forEach(function(cb) {
    cb.checked = source.checked;
  });
  updateBulkActions();
}

function updateBulkActions() {
  var checked = document.querySelectorAll('.request-checkbox:checked');
  var count = checked.length;
  var bar = document.getElementById('bulkActions');
  var label = document.getElementById('selectedCount');
  if (count > 0) {
    bar.style.display = 'flex';
    label.textContent = count;
  } else {
    bar.style.display = 'none';
  }
}

function clearAllCheckboxes() {
  document.querySelectorAll('.request-checkbox:checked').forEach(function(cb) {
    cb.checked = false;
  });
  document.getElementById('selectAll').checked = false;
  updateBulkActions();
}

function bulkApprove() {
  var checked = document.querySelectorAll('.request-checkbox:checked');
  var ids = Array.from(checked).map(function(cb) { return cb.value; });
  var count = ids.length;
  if (count === 0) return;
  if (!confirm('هل أنت متأكد من قبول ' + count + ' طلب؟')) return;

  fetch('{{ route('admin.profile-change-requests.bulk-approve') }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ ids: ids })
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      App.toast ? App.toast(d.message) : alert(d.message);
      location.reload();
    } else {
      alert(d.message || 'حدث خطأ');
    }
  })
  .catch(function() { alert('حدث خطأ في الاتصال'); });
}
</script>
@endpush
