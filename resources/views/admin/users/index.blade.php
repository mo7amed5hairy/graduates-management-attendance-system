@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page_title', 'إدارة المستخدمين')
@section('page_subtitle', 'مراجعة وإدارة حسابات المستخدمين')

@section('content')

<div class="mb-4 flex items-center gap-3">
  <button class="btn btn-primary" onclick="document.getElementById('createUserModal').classList.add('active')">➕ إضافة مستخدم جديد</button>
  <div id="bulkActions" class="flex items-center gap-2" style="display:none">
    <span class="text-sm text-slate-500" id="selectedCount">0</span>
    <span class="text-sm text-slate-400">محدد</span>
    <button class="btn btn-success text-sm" onclick="bulkActivate()">✅ تفعيل الجميع</button>
    <button class="btn btn-ghost text-sm" onclick="clearAllCheckboxes()">إلغاء التحديد</button>
  </div>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="usersTable">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
          <th>#</th>
          <th>المستخدم</th>
          <th>البريد الإلكتروني</th>
          <th>الهاتف</th>
          <th>النقاط</th>
          <th>حالة الاعتماد</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td><input type="checkbox" class="user-checkbox" value="{{ $user->id }}" onchange="updateBulkActions()" {{ $user->isAdmin() ? 'disabled' : '' }}></td>
          <td>{{ $user->id }}</td>
          <td>
            <div class="flex items-center gap-2">
              @if($user->image)
                <img src="{{ $user->image_url }}" class="avatar avatar-sm" style="object-fit:cover">
              @else
                <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
                  {{ substr($user->name, 0, 2) }}
                </div>
              @endif
              <span class="font-semibold">{{ $user->name }}</span>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->phone ?? '—' }}</td>
          <td><span class="pill pill-amber">{{ number_format($user->points) }}</span></td>
          <td>
            @if($user->role === 'admin')
              <span class="pill pill-violet">مدير</span>
            @elseif($user->approval_status === 'approved')
              <span class="pill pill-green">مقبول</span>
            @elseif($user->approval_status === 'rejected')
              <span class="pill pill-rose">مرفوض</span>
            @else
              <span class="pill pill-amber">قيد المراجعة</span>
            @endif
          </td>
          <td>
            @if($user->status === 'active')
              <span class="pill pill-green">نشط</span>
            @else
              <span class="pill pill-rose">غير نشط</span>
            @endif
          </td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editUserModal{{ $user->id }}" title="تعديل">✏️</button>
              @if(!$user->isAdmin())
              <button class="btn btn-ghost py-1 px-2 text-xs toggle-status-btn" data-url="{{ route('admin.users.toggle-status', $user) }}" data-name="{{ $user->name }}" title="{{ $user->status === 'active' ? 'تعليق' : 'تفعيل' }}">
                {{ $user->status === 'active' ? '⏸️' : '▶️' }}
              </button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.users.destroy', $user) }}"
                data-name="{{ $user->name }}">🗑️</button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Edit User Modals --}}
@foreach($users as $user)
<div class="modal-overlay" id="editUserModal{{ $user->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $user->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.update', $user) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الاسم</label>
          <input class="input" name="name" value="{{ $user->name }}" required>
        </div>
        <div>
          <label class="label">البريد الإلكتروني</label>
          <input class="input" type="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" value="{{ $user->phone }}" dir="ltr">
        </div>
        <div>
          <label class="label">الحالة</label>
          <label class="relative inline-flex items-center cursor-pointer gap-3 status-wrapper">
            <input type="checkbox" class="sr-only peer status-toggle" {{ $user->status === 'active' ? 'checked' : '' }}>
            <input type="hidden" name="status" value="{{ $user->status }}">
            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
            <span class="text-sm font-semibold text-slate-700 status-label min-w-[60px]">{{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}</span>
          </label>
        </div>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-primary">💾 تحديث</button>
      </div>
    </form>
  </div>
</div>
@endforeach

{{-- Create User Modal --}}
<div class="modal-overlay" id="createUserModal">
  <div class="modal-content modal-content-xl p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مستخدم جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Row 1: name, mother_name, national_id, phone --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم الرباعى مع اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="name" placeholder="الاسم الرباعي مع اللقب" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأم الرباعى</label>
          <input class="input" name="mother_name" placeholder="اسم الأم الرباعي">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="رقم البطاقة الوطنية" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="077xxxxxxxx" required dir="ltr">
        </div>
      </div>

      {{-- Row 2: email, date_of_birth, age, gender --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">البريد الإلكتروني</label>
          <input class="input" type="email" name="email" placeholder="example@mail.com">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">تاريخ الميلاد</label>
          <input class="input" type="date" name="date_of_birth" id="createDateOfBirth" onchange="calculateAgeC()">
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" name="age" id="createAge" placeholder="--" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الجنس</label>
          <select class="input" name="gender">
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الحالة الاجتماعية</label>
          <select class="input" name="social_status" id="createSocialStatus" onchange="toggleChildrenCountC()">
            <option value="">اختر...</option>
            <option value="أعزب">أعزب</option>
            <option value="متزوج (بدون أطفال)">متزوج (بدون أطفال)</option>
            <option value="متزوج (لديه أطفال)">متزوج (لديه أطفال)</option>
            <option value="منفصل (بدون أطفال)">منفصل (بدون أطفال)</option>
            <option value="منفصل (لديه أطفال)">منفصل (لديه أطفال)</option>
            <option value="أرمل (بدون أطفال)">أرمل (بدون أطفال)</option>
            <option value="أرمل (لديه أطفال)">أرمل (لديه أطفال)</option>
          </select>
        </div>
      </div>

      {{-- Row 3: children count + qualification cascading --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-2" id="createChildrenCountWrap" style="display:none">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" id="createChildrenCount" min="0" placeholder="عدد الأولاد">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">التحصيل الدراسى</label>
          <select class="input" name="qualification_id" id="createQualificationId" onchange="loadFacultiesC()">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الكلية / المعهد</label>
          <select class="input" name="qualification_faculty_id" id="createQualificationFacultyId" disabled>
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <input class="input" type="number" name="graduation_year" placeholder="2024" min="1950" max="{{ date('Y') + 5 }}" required>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الحالة الوظيفية <span class="text-rose-500">*</span></label>
          <select class="input" name="job_status" required>
            <option value="">اختر...</option>
            <option value="موظف">موظف</option>
            <option value="غير موظف">غير موظف</option>
            <option value="طالب">طالب</option>
            <option value="صاحب عمل">صاحب عمل</option>
            <option value="متقاعد">متقاعد</option>
            <option value="أخرى">أخرى</option>
          </select>
        </div>
      </div>

      {{-- Row 4: cascading dropdowns (old fields for admin use) --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-2">
          <label class="label">المحافظة (قديم)</label>
          <select class="input" name="governorate" id="createGovernorate">
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">نوع المؤسسة</label>
          <select class="input" name="institution_type" id="createInstitutionType">
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">نوع الجامعة</label>
          <select class="input" name="university_type" id="createUniversityType">
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجامعة / المعهد</label>
          <select class="input" name="institution_id" id="createInstitution" disabled>
            <option value="">اختر أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">القسم / التخصص</label>
          <select class="input" name="department_id" id="createDepartment" disabled>
            <option value="">اختر أولاً...</option>
          </select>
        </div>
      </div>

      {{-- Row 5: password --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف" required>
        </div>
        <div class="col-span-12 md:col-span-6">
          <label class="label">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="label">عنوان السكن الحالى</label>
        <input class="input" name="address" placeholder="العنوان بالتفصيل">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="label">صورة الهوية (يمكن اختيار أكثر من صورة)</label>
          <input class="input" type="file" name="id_photos[]" multiple accept="image/*" onchange="previewImagesC(this, 'createIdPreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="createIdPreview"></div>
        </div>
        <div>
          <label class="label">إثبات السكن (يمكن اختيار أكثر من صورة)</label>
          <input class="input" type="file" name="residence_proof[]" multiple accept="image/*" onchange="previewImagesC(this, 'createResidencePreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="createResidencePreview"></div>
        </div>
      </div>

      <div class="flex gap-2 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 إضافة المستخدم</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var apiBase = '{{ url('/api') }}';

function loadSelectC(url, selectId, placeholder) {
  var sel = document.getElementById(selectId);
  sel.disabled = true;
  sel.innerHTML = '<option value="">جاري التحميل...</option>';
  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      sel.innerHTML = '<option value="">' + placeholder + '</option>';
      if (d.success && d.data) {
        d.data.forEach(function(item) {
          var opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.name;
          sel.appendChild(opt);
        });
      }
      sel.disabled = false;
    })
    .catch(function() {
      sel.innerHTML = '<option value="">خطأ في التحميل</option>';
      sel.disabled = false;
    });
}

function calculateAgeC() {
  var dob = document.getElementById('createDateOfBirth').value;
  var ageField = document.getElementById('createAge');
  if (!dob) { ageField.value = ''; return; }
  var birthDate = new Date(dob);
  var today = new Date();
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
  ageField.value = age >= 0 ? age : 0;
}

function toggleChildrenCountC() {
  var val = document.getElementById('createSocialStatus').value;
  var wrap = document.getElementById('createChildrenCountWrap');
  if (val.includes('أطفال')) {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
    document.getElementById('createChildrenCount').value = '';
  }
}

function loadFacultiesC() {
  var qualId = document.getElementById('createQualificationId').value;
  var facSel = document.getElementById('createQualificationFacultyId');

  if (!qualId) {
    facSel.innerHTML = '<option value="">اختر المؤهل أولاً...</option>';
    facSel.disabled = true;
    return;
  }

  facSel.disabled = true;
  facSel.innerHTML = '<option value="">جاري التحميل...</option>';

  fetch(apiBase + '/qualifications/' + qualId + '/faculties', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      facSel.innerHTML = '<option value="">اختر الكلية/المعهد...</option>';
      if (d.success && d.data) {
        d.data.forEach(function(item) {
          var opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.name;
          facSel.appendChild(opt);
        });
      }
      facSel.disabled = false;
    })
    .catch(function() {
      facSel.innerHTML = '<option value="">خطأ في التحميل</option>';
      facSel.disabled = false;
    });
}

document.addEventListener('DOMContentLoaded', function() {
  loadSelectC(apiBase + '/governorates', 'createGovernorate', 'اختر المحافظة...');
  loadSelectC(apiBase + '/institution-types', 'createInstitutionType', 'اختر النوع...');
  loadSelectC(apiBase + '/university-types', 'createUniversityType', 'اختر النوع...');
  loadSelectC(apiBase + '/qualifications', 'createQualificationId', 'اختر المؤهل...');
});

function loadInstitutionsC() {
  var govId = document.getElementById('createGovernorate').value;
  var instTypeId = document.getElementById('createInstitutionType').value;
  var uniTypeId = document.getElementById('createUniversityType').value;
  var instSel = document.getElementById('createInstitution');
  var deptSel = document.getElementById('createDepartment');

  deptSel.innerHTML = '<option value="">اختر الجامعة أولاً...</option>';
  deptSel.disabled = true;

  if (!govId || !instTypeId || !uniTypeId) {
    instSel.innerHTML = '<option value="">اختر المحافظة والنوع أولاً...</option>';
    instSel.disabled = true;
    return;
  }

  instSel.disabled = true;
  instSel.innerHTML = '<option value="">جاري التحميل...</option>';

  var url = apiBase + '/governorates/' + govId + '/institution-type/' + instTypeId + '/university-type/' + uniTypeId + '/institutions';
  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      instSel.innerHTML = '<option value="">اختر الجامعة/المعهد...</option>';
      if (d.success && d.data) {
        d.data.forEach(function(item) {
          var opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.name;
          instSel.appendChild(opt);
        });
      }
      instSel.disabled = false;
    })
    .catch(function() {
      instSel.innerHTML = '<option value="">خطأ في التحميل</option>';
      instSel.disabled = false;
    });
}

function loadDepartmentsC() {
  var instId = document.getElementById('createInstitution').value;
  var deptSel = document.getElementById('createDepartment');

  if (!instId) {
    deptSel.innerHTML = '<option value="">اختر الجامعة أولاً...</option>';
    deptSel.disabled = true;
    return;
  }

  deptSel.disabled = true;
  deptSel.innerHTML = '<option value="">جاري التحميل...</option>';

  fetch(apiBase + '/institutions/' + instId + '/departments', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      deptSel.innerHTML = '<option value="">اختر القسم/التخصص...</option>';
      if (d.success && d.data) {
        d.data.forEach(function(item) {
          var opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.name;
          deptSel.appendChild(opt);
        });
      }
      deptSel.disabled = false;
    })
    .catch(function() {
      deptSel.innerHTML = '<option value="">خطأ في التحميل</option>';
      deptSel.disabled = false;
    });
}

document.getElementById('createGovernorate').addEventListener('change', loadInstitutionsC);
document.getElementById('createInstitutionType').addEventListener('change', loadInstitutionsC);
document.getElementById('createUniversityType').addEventListener('change', loadInstitutionsC);
document.getElementById('createInstitution').addEventListener('change', loadDepartmentsC);

function previewImagesC(input, previewId) {
  var preview = document.getElementById(previewId);
  preview.innerHTML = '';
  if (input.files) {
    for (var i = 0; i < input.files.length; i++) {
      (function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'w-20 h-20 object-cover rounded-lg border border-slate-200';
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      })(input.files[i]);
    }
  }
}

// DataTable
$(document).ready(function() {
  $('#usersTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[1, 'desc']],
    columnDefs: [{ orderable: false, targets: [0, 8] }]
  });
});

function toggleSelectAll(source) {
  document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(function(cb) {
    cb.checked = source.checked;
  });
  updateBulkActions();
}

function updateBulkActions() {
  var checked = document.querySelectorAll('.user-checkbox:checked');
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
  document.querySelectorAll('.user-checkbox:checked').forEach(function(cb) {
    cb.checked = false;
  });
  document.getElementById('selectAll').checked = false;
  updateBulkActions();
}

function bulkActivate() {
  var checked = document.querySelectorAll('.user-checkbox:checked');
  var ids = Array.from(checked).map(function(cb) { return cb.value; });
  var count = ids.length;
  if (count === 0) return;
  if (!confirm('هل أنت متأكد من تفعيل ' + count + ' مستخدم؟')) return;

  fetch('{{ route('admin.users.bulk-activate') }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ ids: ids })
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      App.toast(d.message);
      location.reload();
    } else {
      alert(d.message || 'حدث خطأ');
    }
  })
  .catch(function() { alert('حدث خطأ في الاتصال'); });
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('status-toggle')) {
    const wrapper = e.target.closest('.status-wrapper');
    const hidden = wrapper.querySelector('input[type="hidden"]');
    const label = wrapper.querySelector('.status-label');
    hidden.value = e.target.checked ? 'active' : 'inactive';
    label.textContent = e.target.checked ? 'نشط' : 'غير نشط';
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
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
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

@endsection
