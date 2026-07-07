@extends('layouts.admin')

@section('title', 'طلبات تعديل البيانات')
@section('page_title', 'طلبات تعديل البيانات')
@section('page_subtitle', 'مراجعة طلبات تعديل البيانات ورفع المرفقات')

@section('content')

<div class="mb-4 flex items-center gap-3">
  <div id="bulkActions" class="flex items-center gap-2" style="display:none">
    <span class="text-sm text-slate-500" id="selectedCount">0</span>
    <span class="text-sm text-slate-400">محدد</span>
    <button class="btn btn-success text-sm" onclick="bulkApprove()">✅ قبول المحدد</button>
    <button class="btn btn-ghost text-sm" onclick="clearAllCheckboxes()">إلغاء التحديد</button>
  </div>
</div>

@if($requests->count() === 0)
<div class="card p-8 text-center">
  <div class="text-4xl mb-3">✅</div>
  <div class="text-lg font-bold text-slate-700">لا توجد طلبات تعديل معلقة</div>
  <div class="text-sm text-slate-400 mt-1">جميع الطلبات تمت مراجعتها</div>
</div>
@else

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
          <th>تاريخ التقديم</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($requests as $r)
        <tr id="row-{{ $r->id }}">
          <td><input type="checkbox" class="request-checkbox" value="{{ $r->id }}" onchange="updateBulkActions()"></td>
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
          <td class="text-xs text-slate-500">{{ $r->created_at->format('Y/m/d H:i') }}</td>
          <td>
            <a href="{{ route('admin.profile-change-requests.show', $r) }}" class="btn btn-sm btn-primary">🔍 عرض</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endif
@endsection

@push('scripts')
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

$(function() {
  $('#changeRequestsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[1, 'desc']],
    columnDefs: [{ orderable: false, targets: [0, 6] }]
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

function showProgressOverlay(current, total) {
  var existing = document.getElementById('bulkProgressOverlay');
  if (!existing) {
    var div = document.createElement('div');
    div.id = 'bulkProgressOverlay';
    div.innerHTML = '<div class="fixed inset-0 bg-white/80 flex items-center justify-center" style="z-index:99999">' +
      '<div class="text-center bg-white rounded-2xl shadow-2xl p-8 w-96">' +
      '<div class="text-lg font-bold text-slate-800 mb-3" id="progressText">جاري القبول...</div>' +
      '<div class="w-full bg-slate-200 rounded-full h-4 mb-2 overflow-hidden">' +
      '<div class="bg-gradient-to-r from-sky-500 to-emerald-500 h-4 rounded-full transition-all duration-300" id="progressBar" style="width:0%"></div></div>' +
      '<div class="text-xs text-slate-500" id="progressPercent">0%</div></div></div>';
    document.body.appendChild(div);
  }
  var pct = total > 0 ? Math.round((current / total) * 100) : 0;
  document.getElementById('progressBar').style.width = pct + '%';
  document.getElementById('progressPercent').textContent = pct + '%';
  document.getElementById('progressText').textContent = 'جاري القبول... (' + current + '/' + total + ')';
}

function hideProgressOverlay() {
  var el = document.getElementById('bulkProgressOverlay');
  if (el) el.remove();
}

function bulkApprove() {
  var checked = document.querySelectorAll('.request-checkbox:checked');
  var ids = Array.from(checked).map(function(cb) { return cb.value; });
  var total = ids.length;
  if (total === 0) return;
  if (!confirm('هل أنت متأكد من قبول ' + total + ' طلب؟')) return;

  var completed = 0;

  showProgressOverlay(0, total);

  function sendOne(id) {
    return fetch('{{ route("admin.profile-change-requests.bulk-approve") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ ids: [id] })
    }).then(function(r) { return r.json(); });
  }

  function processNext() {
    if (completed >= total) {
      hideProgressOverlay();
      App.toast ? App.toast('✅ تم قبول ' + total + ' طلب بنجاح') : alert('✅ تم القبول بنجاح');
      location.reload();
      return;
    }
    sendOne(ids[completed]).then(function(d) {
      completed++;
      showProgressOverlay(completed, total);
      // Remove row from table
      var row = document.getElementById('row-' + ids[completed - 1]);
      if (row) row.remove();
      processNext();
    }).catch(function() {
      hideProgressOverlay();
      alert('حدث خطأ في الاتصال بعد قبول ' + completed + ' من ' + total + ' طلب');
    });
  }

  processNext();
}
</script>
@endpush
