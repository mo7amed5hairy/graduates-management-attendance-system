@extends('layouts.admin')

@section('title', 'الخريجين')
@section('page_title', 'إدارة الخريجين')
@section('page_subtitle', 'مراجعة واعتماد بيانات الخريجين')

@section('content')
<div class="card p-4 mb-4">
  <div class="grid grid-cols-12 gap-3 items-end">
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">الجنس</label>
      <select class="input text-sm" id="filterGender">
        <option value="">الكل</option>
        @foreach($genders as $g)
          <option value="{{ $g }}">{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">المحافظة</label>
      <select class="input text-sm" id="filterGovernorate">
        <option value="">الكل</option>
        @foreach($governorates as $g)
          @php $govName = is_numeric($g) && isset($governorateMap[$g]) ? $governorateMap[$g] : $g; @endphp
          <option value="{{ $g }}">{{ $govName }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">سنة الميلاد</label>
      <select class="input text-sm" id="filterBirthYear">
        <option value="">الكل</option>
        @foreach($birthYears as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">الحالة الاجتماعية</label>
      <select class="input text-sm" id="filterSocialStatus">
        <option value="">الكل</option>
        <option value="أعزب">أعزب</option>
        <option value="متزوج">متزوج</option>
        <option value="مطلق">مطلق</option>
        <option value="أرمل">أرمل</option>
      </select>
    </div>
    <div class="col-span-6 md:col-span-1">
      <label class="label text-xs mb-1">عدد الأولاد</label>
      <select class="input text-sm" id="filterChildren">
        <option value="">الكل</option>
        @for($c = 0; $c <= 10; $c++)
          <option value="{{ $c }}">{{ $c }}</option>
        @endfor
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">سنة التخرج</label>
      <select class="input text-sm" id="filterGradYear">
        <option value="">الكل</option>
        @foreach($years as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-1">
      <label class="label text-xs mb-1">المؤهل</label>
      <select class="input text-sm" id="filterQualification">
        <option value="">الكل</option>
        @foreach($qualifications as $q)
          <option value="{{ $q->id }}">{{ $q->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-12 md:col-span-1 flex gap-2">
      <button class="btn btn-primary text-sm py-2 px-3 w-full" id="resetFilters">🔄</button>
    </div>
  </div>
</div>

<div class="mb-4 flex items-center gap-3">
  <div id="bulkActions" class="flex items-center gap-2" style="display:none">
    <span class="text-sm text-slate-500" id="selectedCount">0</span>
    <span class="text-sm text-slate-400">محدد</span>
    <button class="btn btn-success text-sm" id="bulkActivateBtn" onclick="bulkActivate()">✅ تفعيل الجميع</button>
    <button class="btn btn-ghost text-sm" onclick="clearAllCheckboxes()">إلغاء التحديد</button>
  </div>
</div>

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="graduatesTable">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>رقم البطاقة الوطنية</th>
          <th>الجنس</th>
          <th>سنة الميلاد</th>
          <th>المحافظة</th>
          <th>الحالة الاجتماعية</th>
          <th>عدد الأولاد</th>
          <th>المؤهل</th>
          <th>الجامعة</th>
          <th>الكلية</th>
          <th>سنة التخرج</th>
          <th>الحالة</th>
          <th>تاريخ التسجيل</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($graduates as $i => $g)
        <tr>
          <td><input type="checkbox" class="user-checkbox" value="{{ $g->id }}" onchange="updateBulkActions()"></td>
          <td>{{ $i + 1 }}</td>
          <td class="font-bold">{{ $g->name }}</td>
          <td class="text-xs">{{ $g->email }}</td>
          <td class="text-xs">{{ $g->national_id ?? '—' }}</td>
          <td>
            @if($g->gender === 'ذكر')
              <span class="pill pill-blue">ذكر</span>
            @elseif($g->gender === 'أنثى')
              <span class="pill pill-rose">أنثى</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td>{{ $g->date_of_birth ?? '—' }}</td>
          <td>{{ $g->governorate_name }}</td>
          <td>{{ $g->social_status ?? '—' }}</td>
          <td>{{ $g->children_count ?? '—' }}</td>
          <td>{{ $g->qualification?->name ?? '—' }}</td>
          <td>{{ $g->university ?? '—' }}</td>
          <td>{{ $g->faculty ?? '—' }}</td>
          <td>{{ $g->graduation_year ?? '—' }}</td>
          <td>
            @if($g->approval_status === 'approved')
              <span class="pill pill-green">مقبول</span>
            @elseif($g->approval_status === 'rejected')
              <span class="pill pill-rose">مرفوض</span>
            @else
              <span class="pill pill-amber">قيد المراجعة</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $g->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button onclick="openUserModal({{ $g->id }})" class="btn btn-sm btn-ghost py-1 px-2" title="عرض التفاصيل">👁️</button>
              <a href="{{ route('admin.graduates.show', $g) }}" class="btn btn-sm btn-primary">🔍 عرض</a>
              <button class="btn btn-ghost py-1 px-2 text-xs toggle-status-btn" data-url="{{ route('admin.users.toggle-status', $g) }}" data-name="{{ $g->name }}" title="{{ $g->status === 'active' ? 'تعليق' : 'تفعيل' }}">
                {{ $g->status === 'active' ? '⏸️' : '▶️' }}
              </button>
              @if(!$g->isAdmin())
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.users.destroy', $g) }}"
                data-name="{{ $g->name }}">🗑️</button>
              @endif
            </div>
          </td>
        </tr>
        @empty
          <tr><td colspan="17" class="text-center text-slate-400 py-8">لا يوجد خريجين لعرضهم</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

{{-- User Details Modal --}}
<div id="userDetailsModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-start justify-center overflow-y-auto" style="padding: 1rem;">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-4 relative" style="max-height:90vh;overflow-y:auto">
    <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-4 border-b border-slate-100 rounded-t-2xl">
      <h3 class="text-lg font-extrabold text-slate-800">👤 تفاصيل المستخدم</h3>
      <button onclick="closeUserModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
    </div>
    <div class="p-5" id="userDetailsContent">
      <div class="text-center text-slate-400 py-8">جاري التحميل...</div>
    </div>
  </div>
</div>

{{-- Image Zoom Modal --}}
<div id="imageZoomModal" class="fixed inset-0 hidden bg-black/80 flex items-center justify-center p-4" style="z-index:9999" onclick="closeImageZoom()">
  <button onclick="closeImageZoom()" class="absolute top-4 left-4 text-white text-3xl hover:text-slate-300">&times;</button>
  <img id="zoomImage" class="max-w-full max-h-full object-contain rounded-lg" src="" alt="zoom">
</div>

@push('scripts')
<script>
var userModal = document.getElementById('userDetailsModal');

function openUserModal(userId) {
  userModal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  document.getElementById('userDetailsContent').innerHTML = '<div class="text-center text-slate-400 py-8">جاري التحميل...</div>';
  fetch('{{ url('/admin/user-details') }}/' + userId, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) { renderUserDetails(d.data); }
    else { document.getElementById('userDetailsContent').innerHTML = '<div class="text-center text-rose-500 py-8">حدث خطأ في تحميل البيانات</div>'; }
  })
  .catch(function() {
    document.getElementById('userDetailsContent').innerHTML = '<div class="text-center text-rose-500 py-8">حدث خطأ في الاتصال</div>';
  });
}

function closeUserModal() {
  userModal.classList.add('hidden');
  document.body.style.overflow = '';
}

userModal?.addEventListener('click', function(e) {
  if (e.target === userModal) closeUserModal();
});

function openImageZoom(src) {
  document.getElementById('zoomImage').src = src;
  document.getElementById('imageZoomModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeImageZoom() {
  document.getElementById('imageZoomModal').classList.add('hidden');
  document.body.style.overflow = '';
}

function renderUserDetails(u) {
  var html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">' +
    '<div><span class="text-xs text-slate-400 block">الإسم الرباعى مع اللقب</span><span class="font-semibold">' + esc(u.name) + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">اسم الأم الرباعى</span><span class="font-semibold">' + esc(u.mother_name || '') + ' ' + esc(u.mother_father_name || '') + ' ' + esc(u.mother_grandfather_name || '') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">رقم البطاقة الوطنية</span><span class="font-semibold">' + esc(u.national_id || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">رقم الهاتف</span><span class="font-semibold">' + esc(u.phone || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">سنة الميلاد</span><span class="font-semibold">' + esc(u.date_of_birth || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">العمر</span><span class="font-semibold">' + esc(u.age || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">الجنس</span><span class="font-semibold">' + esc(u.gender || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">الحالة الاجتماعية</span><span class="font-semibold">' + esc(u.social_status || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">الحالة الوظيفية</span><span class="font-semibold">' + esc(u.job_status || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">التحصيل الدراسى</span><span class="font-semibold">' + esc(u.qualification_name || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">الكلية / المعهد</span><span class="font-semibold">' + esc(u.faculty_name || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">سنة التخرج</span><span class="font-semibold">' + esc(u.graduation_year || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">المحافظة</span><span class="font-semibold">' + esc(u.governorate_name || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">عنوان السكن الحالى</span><span class="font-semibold">' + esc(u.address || '—') + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">البريد الإلكتروني</span><span class="font-semibold dir-ltr text-xs">' + esc(u.email || '—') + '</span></div>' +
    (u.children_count !== null && u.children_count !== undefined ? '<div><span class="text-xs text-slate-400 block">عدد الأولاد</span><span class="font-semibold">' + esc(u.children_count) + '</span></div>' : '') +
    '<div><span class="text-xs text-slate-400 block">إجمالي النقاط</span><span class="font-bold text-lg">' + esc(u.points) + '</span></div>' +
    '<div><span class="text-xs text-slate-400 block">حالة الاعتماد</span><span class="font-semibold">' + (u.approval_status === 'approved' ? '<span class="text-green-600">✅ معتمد</span>' : (u.approval_status === 'rejected' ? '<span class="text-rose-600">❌ مرفوض</span>' : '<span class="text-amber-600">⏳ قيد المراجعة</span>')) + '</span></div>' +
  '</div>';

  // ID Photos
  if (u.id_photos && u.id_photos.length > 0) {
    html += '<hr class="my-4 border-slate-100"><div><span class="text-xs text-slate-400 block mb-2">🪪 صور الهوية</span><div class="flex flex-wrap gap-3">';
    u.id_photos.forEach(function(p) {
      var src = '{{ url('/files') }}/' + p;
      html += '<a href="javascript:void(0)" onclick="openImageZoom(\'' + src + '\')"><img src="' + src + '" class="w-28 h-28 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition cursor-pointer"></a>';
    });
    html += '</div></div>';
  }

  // Graduation attachments
  if (u.graduation_attachments && u.graduation_attachments.length > 0) {
    html += '<hr class="my-4 border-slate-100"><div><span class="text-xs text-slate-400 block mb-2">📎 مرفقات التخرج (' + u.graduation_attachments.length + ')</span><div class="flex flex-wrap gap-2">';
    u.graduation_attachments.forEach(function(att) {
      var ext = (att.original_name || '').split('.').pop().toLowerCase();
      var src = '{{ url('/files') }}/' + att.file_path;
      if (['jpg','jpeg','png','gif','webp'].indexOf(ext) !== -1) {
        html += '<a href="javascript:void(0)" onclick="openImageZoom(\'' + src + '\')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg hover:bg-sky-50 hover:border-sky-200 transition text-xs text-slate-600 hover:text-sky-700" title="' + esc(att.original_name) + '">🖼️ <span class="truncate max-w-[100px]">' + esc(att.original_name) + '</span></a>';
      } else {
        html += '<a href="' + src + '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg hover:bg-sky-50 hover:border-sky-200 transition text-xs text-slate-600 hover:text-sky-700" title="' + esc(att.original_name) + '">📄 <span class="truncate max-w-[100px]">' + esc(att.original_name) + '</span></a>';
      }
    });
    html += '</div></div>';
  }

  document.getElementById('userDetailsContent').innerHTML = html;
}

function esc(str) {
  if (!str) return '';
  var div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}
</script>

<script>
@if($graduates->count() > 0)
$(function() {
  var table = $('#graduatesTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[1, 'asc']],
    columnDefs: [
      { orderable: false, targets: [0, 16] }
    ],
    pageLength: 50,
    lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, 'الكل']]
  });

  function applyFilters() {
    var gender = $('#filterGender').val();
    var gov = $('#filterGovernorate').val();
    var birth = $('#filterBirthYear').val();
    var social = $('#filterSocialStatus').val();
    var children = $('#filterChildren').val();
    var gradYear = $('#filterGradYear').val();
    var qual = $('#filterQualification').val();

    $.fn.dataTable.ext.search = [];

    if (gender) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[5].trim().localeCompare(gender, 'ar', { sensitivity: 'base' }) === 0;
      });
    }
    if (birth) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[6].trim() === birth;
      });
    }
    if (gov) {
      var govText = $('#filterGovernorate option:selected').text();
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[7].trim().localeCompare(govText, 'ar', { sensitivity: 'base' }) === 0;
      });
    }
    if (social) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[8].trim().localeCompare(social, 'ar', { sensitivity: 'base' }) === 0;
      });
    }
    if (children !== '') {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[9].trim() === children;
      });
    }
    if (qual) {
      var qualText = $('#filterQualification option:selected').text();
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[10].trim().localeCompare(qualText, 'ar', { sensitivity: 'base' }) === 0;
      });
    }
    if (gradYear) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[13].trim() === gradYear;
      });
    }

    table.draw();
  }

  $('#filterGender, #filterGovernorate, #filterBirthYear, #filterSocialStatus, #filterChildren, #filterGradYear, #filterQualification').on('change', applyFilters);

  $('#resetFilters').on('click', function() {
    $('#filterGender').val('');
    $('#filterGovernorate').val('');
    $('#filterBirthYear').val('');
    $('#filterSocialStatus').val('');
    $('#filterChildren').val('');
    $('#filterGradYear').val('');
    $('#filterQualification').val('');
    $.fn.dataTable.ext.search = [];
    table.draw();
  });
});
@endif

var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function toggleSelectAll(source) {
  document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(function(cb) {
    cb.checked = source.checked;
  });
  updateBulkActions();
}

function updateBulkActions() {
  var checked = document.querySelectorAll('.user-checkbox:checked');
  var total = document.querySelectorAll('.user-checkbox');
  var count = checked.length;
  var bar = document.getElementById('bulkActions');
  var label = document.getElementById('selectedCount');
  var btn = document.getElementById('bulkActivateBtn');
  if (count > 0) {
    bar.style.display = 'flex';
    label.textContent = count;
    if (count === total.length) {
      btn.textContent = '✅ تفعيل الجميع';
    } else if (count === 1) {
      btn.textContent = '✅ تفعيل مستخدم';
    } else {
      btn.textContent = '✅ تفعيل عدد ' + count + ' مستخدم';
    }
  } else {
    bar.style.display = 'none';
  }
}

function clearAllCheckboxes() {
  document.querySelectorAll('.user-checkbox:checked').forEach(function(cb) {
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
      '<div class="text-lg font-bold text-slate-800 mb-3" id="progressText">جاري التفعيل...</div>' +
      '<div class="w-full bg-slate-200 rounded-full h-4 mb-2 overflow-hidden">' +
      '<div class="bg-gradient-to-r from-sky-500 to-emerald-500 h-4 rounded-full transition-all duration-300" id="progressBar" style="width:0%"></div></div>' +
      '<div class="text-xs text-slate-500" id="progressPercent">0%</div></div></div>';
    document.body.appendChild(div);
  }
  var pct = total > 0 ? Math.round((current / total) * 100) : 0;
  document.getElementById('progressBar').style.width = pct + '%';
  document.getElementById('progressPercent').textContent = pct + '%';
  document.getElementById('progressText').textContent = 'جاري التفعيل... (' + current + '/' + total + ')';
}
function hideProgressOverlay() { var el = document.getElementById('bulkProgressOverlay'); if (el) el.remove(); }

function bulkActivate() {
  var checked = document.querySelectorAll('.user-checkbox:checked');
  var ids = Array.from(checked).map(function(cb) { return cb.value; });
  var total = ids.length;
  if (total === 0) return;
  if (!confirm('هل أنت متأكد من تفعيل ' + total + ' مستخدم؟')) return;

  var completed = 0;

  showProgressOverlay(0, total);

  function sendOne(id) {
    return fetch('{{ route('admin.users.bulk-activate') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ ids: [id] })
    }).then(function(r) { return r.json(); });
  }

  function processNext() {
    if (completed >= total) {
      hideProgressOverlay();
      App.toast ? App.toast('✅ تم تفعيل ' + total + ' مستخدم بنجاح', 'success') : alert('✅ تم تفعيل ' + total + ' مستخدم بنجاح');
      location.reload();
      return;
    }
    sendOne(ids[completed]).then(function(d) {
      completed++;
      showProgressOverlay(completed, total);
      processNext();
    }).catch(function() {
      hideProgressOverlay();
      alert('حدث خطأ في الاتصال بعد تفعيل ' + completed + ' من ' + total + ' مستخدم');
    });
  }

  processNext();
}

document.addEventListener('click', function(e) {
  var deleteBtn = e.target.closest('[data-delete]');
  if (deleteBtn) {
    var url = deleteBtn.dataset.delete;
    var name = deleteBtn.dataset.name;
    if (!confirm('هل أنت متأكد من حذف "' + name + '"?')) return;
    fetch(url, {
      method: 'DELETE',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      if (d.success) { App.toast ? App.toast(d.message, 'success') : alert(d.message); location.reload(); }
      else { alert(d.message || 'حدث خطأ'); }
    })
    .catch(function() { alert('حدث خطأ في الاتصال'); });
  }
});

document.addEventListener('click', function(e) {
  var btn = e.target.closest('.toggle-status-btn');
  if (!btn) return;
  var url = btn.dataset.url;
  var name = btn.dataset.name;
  if (!confirm('تأكيد تغيير حالة المستخدم "' + name + '"?')) return;
  fetch(url, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) { location.reload(); }
    else { alert(d.message || 'حدث خطأ'); }
  })
  .catch(function() { alert('حدث خطأ في الاتصال'); });
});
</script>
@endpush
