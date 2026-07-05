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
    <button class="btn btn-success text-sm" id="bulkActivateBtn" onclick="bulkActivate()">✅ تفعيل الجميع</button>
    <button class="btn btn-ghost text-sm" onclick="clearAllCheckboxes()">إلغاء التحديد</button>
  </div>
</div>

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
        @foreach($allGovernorates as $gov)
          <option value="{{ $gov }}">{{ $gov }}</option>
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
        @foreach($graduationYears as $y)
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
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">حالة الاعتماد</label>
      <select class="input text-sm" id="filterApprovalStatus">
        <option value="">الكل</option>
        <option value="pending">⏳ قيد المراجعة</option>
        <option value="approved">✅ مقبول</option>
        <option value="rejected">❌ مرفوض</option>
      </select>
    </div>
    <div class="col-span-6 md:col-span-1">
      <label class="label text-xs mb-1">حالة الحساب</label>
      <select class="input text-sm" id="filterActiveStatus">
        <option value="">الكل</option>
        <option value="active">نشط</option>
        <option value="inactive">غير نشط</option>
      </select>
    </div>
    <div class="col-span-12 md:col-span-1 flex gap-2">
      <button class="btn btn-primary text-sm py-2 px-3 w-full" id="resetFilters">🔄</button>
    </div>
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
          <th>رقم البطاقة الوطنية</th>
          <th>الهاتف</th>
          <th>النقاط</th>
          <th>الجنس</th>
          <th>المحافظة</th>
          <th>سنة الميلاد</th>
          <th>الحالة الاجتماعية</th>
          <th>عدد الأولاد</th>
          <th>المؤهل</th>
          <th>سنة التخرج</th>
          <th>حالة الاعتماد</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

{{-- Create User Modal --}}
<div class="modal-overlay" id="createUserModal">
  <div class="modal-content modal-content-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مستخدم جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.store') }}" method="POST">
      @csrf

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" placeholder="الإسم" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" placeholder="اسم الأب" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" placeholder="اسم الجد" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" placeholder="اللقب" required>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_name" placeholder="اسم الأم" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">أب الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_father_name" placeholder="أب الأم" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">جد الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_grandfather_name" placeholder="جد الأم" required>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="رقم البطاقة الوطنية" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">البريد الإلكتروني <small>(اختياري)</small></label>
          <input class="input" type="email" name="email" placeholder="example@mail.com">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="077xxxxxxxx أو 078xxxxxxxx" maxlength="11" required dir="ltr">
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد <span class="text-rose-500">*</span></label>
          <select class="input" name="date_of_birth" id="createDateOfBirth" required onchange="calcAgeC()">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(2015, 1970) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" id="createAge" placeholder="--" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجنس <span class="text-rose-500">*</span></label>
          <select class="input" name="gender" required>
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
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

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">الحالة الاجتماعية <span class="text-rose-500">*</span></label>
          <select class="input" name="social_status" id="createSocialStatus" required onchange="toggleChildrenCreate()">
            <option value="">اختر...</option>
            <option value="أعزب">أعزب</option>
            <option value="متزوج">متزوج</option>
            <option value="مطلق">مطلق</option>
            <option value="أرمل">أرمل</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-6" id="createChildrenWrap" style="display:none">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" id="createChildrenCount" min="0" max="20" placeholder="من 0 إلى 20">
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate" id="createGovernorate" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى <span class="text-rose-500">*</span></label>
          <textarea class="input" name="address" rows="3" placeholder="العنوان بالتفصيل" required></textarea>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_id" id="createQualification" required onchange="loadFacultiesCreate()">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_faculty_id" id="createFaculty" required disabled>
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <select class="input" name="graduation_year" required>
            <option value="">اختر سنة التخرج...</option>
            @foreach(range(2025, 2000) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

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

      <div class="flex gap-2 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 إضافة المستخدم</button>
      </div>
    </form>
  </div>
</div>

{{-- Single Edit User Modal (loaded dynamically) --}}
<div class="modal-overlay" id="editUserModal">
  <div class="modal-content modal-content-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900" id="editModalTitle">✏️ تعديل المستخدم</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" onclick="closeEditModal()">&times;</button>
    </div>
    <form id="editUserForm" data-ajax="true" method="POST">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" required>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_name" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">أب الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_father_name" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">جد الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_grandfather_name" required>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">البريد الإلكتروني <small>(اختياري)</small></label>
          <input class="input" type="email" name="email">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="077xxxxxxxx أو 078xxxxxxxx" maxlength="11" required dir="ltr">
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد <span class="text-rose-500">*</span></label>
          <select class="input" name="date_of_birth" id="editDateOfBirth" required onchange="calcEditAge()">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(2015, 1970) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" id="editAge" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجنس <span class="text-rose-500">*</span></label>
          <select class="input" name="gender" required>
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
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

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">الحالة الاجتماعية</label>
          <select class="input" name="social_status" id="editSocialStatus" onchange="toggleEditChildren()">
            <option value="">اختر...</option>
            <option value="أعزب">أعزب</option>
            <option value="متزوج">متزوج</option>
            <option value="مطلق">مطلق</option>
            <option value="أرمل">أرمل</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-6" id="editChildrenWrap" style="display:none">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" min="0" max="20">
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة</label>
          <select class="input" name="governorate" id="editGovernorate">
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى <span class="text-rose-500">*</span></label>
          <textarea class="input" name="address" rows="3" placeholder="العنوان بالتفصيل" required></textarea>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_id" id="editQualification" required onchange="loadEditFaculties()">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_faculty_id" id="editFaculty" required disabled>
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <select class="input" name="graduation_year" required>
            <option value="">اختر سنة التخرج...</option>
            @foreach(range(2025, 2000) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-5">
          <label class="label">كلمة المرور <small>(اتركه فارغاً إن لم ترد التغيير)</small></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف">
        </div>
        <div class="col-span-12 md:col-span-5">
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور">
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الحالة</label>
          <label class="relative inline-flex items-center cursor-pointer gap-3 status-wrapper mt-1">
            <input type="checkbox" class="sr-only peer status-toggle">
            <input type="hidden" name="status" value="active">
            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
            <span class="text-sm font-semibold text-slate-700 status-label min-w-[60px]">نشط</span>
          </label>
        </div>
      </div>

      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()">إلغاء</button>
        <button type="submit" class="btn btn-primary">💾 تحديث</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var apiBase = '{{ url('/api') }}';
var editUserId = null;

function openEditModal(userId) {
  editUserId = userId;
  var modal = document.getElementById('editUserModal');
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';

  // Set form action
  var form = document.getElementById('editUserForm');
  form.action = '{{ url('/admin/users') }}/' + userId;

  // Fetch user data
  fetch(form.action, {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (!d.success) return;
    var u = d.data;
    document.getElementById('editModalTitle').textContent = '✏️ تعديل: ' + (u.first_name || '') + ' ' + (u.father_name || '') + ' ' + (u.grandfather_name || '') + ' ' + (u.family_name || '');

    form.querySelector('[name="first_name"]').value = u.first_name || '';
    form.querySelector('[name="father_name"]').value = u.father_name || '';
    form.querySelector('[name="grandfather_name"]').value = u.grandfather_name || '';
    form.querySelector('[name="family_name"]').value = u.family_name || '';
    form.querySelector('[name="mother_name"]').value = u.mother_name || '';
    form.querySelector('[name="mother_father_name"]').value = u.mother_father_name || '';
    form.querySelector('[name="mother_grandfather_name"]').value = u.mother_grandfather_name || '';
    form.querySelector('[name="national_id"]').value = u.national_id || '';
    form.querySelector('[name="email"]').value = u.email || '';
    form.querySelector('[name="phone"]').value = u.phone || '';
    form.querySelector('[name="date_of_birth"]').value = u.date_of_birth || '';
    calcEditAge();
    form.querySelector('[name="gender"]').value = u.gender || '';
    form.querySelector('[name="job_status"]').value = u.job_status || '';
    form.querySelector('[name="social_status"]').value = u.social_status || '';
    toggleEditChildren();
    if (u.children_count !== null && u.children_count !== undefined) {
      form.querySelector('[name="children_count"]').value = u.children_count;
    }
    form.querySelector('[name="address"]').value = u.address || '';

    // Status toggle
    var statusCheck = form.querySelector('.status-toggle');
    var statusHidden = form.querySelector('[name="status"]');
    var statusLabel = form.querySelector('.status-label');
    var isActive = u.status === 'active';
    statusCheck.checked = isActive;
    statusHidden.value = u.status || 'active';
    statusLabel.textContent = isActive ? 'نشط' : 'غير نشط';

    // Load governorates
    loadSelect(apiBase + '/governorates', 'editGovernorate', 'اختر المحافظة...', u.governorate || null);

    // Load qualifications (and faculties)
    loadSelectQuals(apiBase + '/qualifications', 'editQualification', 'اختر المؤهل...', u.qualification_id || null, u.qualification_faculty_id || null);

    // Graduation year
    form.querySelector('[name="graduation_year"]').value = u.graduation_year || '';
  })
  .catch(function() {
    App.toast ? App.toast('حدث خطأ في تحميل بيانات المستخدم', 'error') : alert('حدث خطأ');
  });
}

function closeEditModal() {
  var modal = document.getElementById('editUserModal');
  modal.classList.remove('active');
  document.body.style.overflow = '';
  editUserId = null;
}

function calcEditAge() {
  var year = document.getElementById('editDateOfBirth').value;
  document.getElementById('editAge').value = year ? (new Date().getFullYear() - parseInt(year)) : '';
}

function toggleEditChildren() {
  var val = document.getElementById('editSocialStatus').value;
  var wrap = document.getElementById('editChildrenWrap');
  if (val === 'متزوج' || val === 'مطلق' || val === 'أرمل') {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
}

function loadEditFaculties() {
  var qualId = document.getElementById('editQualification').value;
  loadFacultiesFor('editFaculty', qualId, null);
}

// Close modal on overlay click
document.getElementById('editUserModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});

// Status toggle
document.addEventListener('change', function(e) {
  if (e.target.classList.contains('status-toggle')) {
    const wrapper = e.target.closest('.status-wrapper');
    const hidden = wrapper.querySelector('input[type="hidden"]');
    const label = wrapper.querySelector('.status-label');
    hidden.value = e.target.checked ? 'active' : 'inactive';
    label.textContent = e.target.checked ? 'نشط' : 'غير نشط';
  }
});

function loadSelect(url, selectId, placeholder, selectedValue) {
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
          opt.value = item.name;
          opt.textContent = item.name;
          if (selectedValue && item.name === selectedValue) opt.selected = true;
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

function loadSelectQuals(url, selectId, placeholder, selectedId, selectedFacultyId) {
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
          if (selectedId && parseInt(item.id) === parseInt(selectedId)) opt.selected = true;
          sel.appendChild(opt);
        });
      }
      sel.disabled = false;
      // Auto-load faculties if qualification is pre-selected
      if (selectedId) {
        var facId = sel.id.replace('editQualification', 'editFaculty');
        loadFacultiesFor(facId, selectedId, selectedFacultyId || null);
      }
    })
    .catch(function() {
      sel.innerHTML = '<option value="">خطأ في التحميل</option>';
      sel.disabled = false;
    });
}

function loadFacultiesFor(facultySelectId, qualId, selectedFacultyId) {
  var facSel = document.getElementById(facultySelectId);
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
          if (selectedFacultyId && parseInt(item.id) === parseInt(selectedFacultyId)) opt.selected = true;
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

// ===== CREATE MODAL FUNCTIONS =====
function calcAgeC() {
  var sel = document.getElementById('createDateOfBirth');
  var year = sel.value;
  var ageField = document.getElementById('createAge');
  if (!year) { ageField.value = ''; return; }
  ageField.value = new Date().getFullYear() - parseInt(year);
}

function toggleChildrenCreate() {
  var status = document.getElementById('createSocialStatus').value;
  var wrap = document.getElementById('createChildrenWrap');
  if (status === 'متزوج' || status === 'مطلق' || status === 'أرمل') {
    wrap.style.display = 'block';
    document.getElementById('createChildrenCount').required = true;
  } else {
    wrap.style.display = 'none';
    document.getElementById('createChildrenCount').required = false;
    document.getElementById('createChildrenCount').value = '';
  }
}

function loadFacultiesCreate() {
  var qualId = document.getElementById('createQualification').value;
  loadFacultiesFor('createFaculty', qualId, null);
}

document.addEventListener('DOMContentLoaded', function() {
  loadSelect(apiBase + '/governorates', 'createGovernorate', 'اختر المحافظة...');
  loadSelectQuals(apiBase + '/qualifications', 'createQualification', 'اختر المؤهل...');
});

// DataTable — server-side processing
$(document).ready(function() {
  var table = $('#usersTable').DataTable({
    serverSide: true,
    ajax: {
      url: '{{ route('admin.users.data') }}',
      type: 'GET',
      data: function(d) {
        d.gender = $('#filterGender').val();
        d.governorate = $('#filterGovernorate').val();
        d.birth_year = $('#filterBirthYear').val();
        d.social_status = $('#filterSocialStatus').val();
        d.children_count = $('#filterChildren').val();
        d.graduation_year = $('#filterGradYear').val();
        d.qualification_id = $('#filterQualification').val();
        d.approval_status = $('#filterApprovalStatus').val();
        d.active_status = $('#filterActiveStatus').val();
      }
    },
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[1, 'desc']],
    columnDefs: [
      { orderable: false, targets: [0, 16] }
    ],
    pageLength: 50,
    lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, 'الكل']],
    drawCallback: function() {
      // Re-bind delete/toggle events after table redraw
      updateBulkActions();
      document.getElementById('selectAll').checked = false;
    }
  });

  // Apply filters on change
  $('#filterGender, #filterGovernorate, #filterBirthYear, #filterSocialStatus, #filterChildren, #filterGradYear, #filterQualification, #filterApprovalStatus, #filterActiveStatus').on('change', function() {
    table.ajax.reload();
  });

  $('#resetFilters').on('click', function() {
    $('#filterGender').val('');
    $('#filterGovernorate').val('');
    $('#filterBirthYear').val('');
    $('#filterSocialStatus').val('');
    $('#filterChildren').val('');
    $('#filterGradYear').val('');
    $('#filterQualification').val('');
    $('#filterApprovalStatus').val('');
    $('#filterActiveStatus').val('');
    table.ajax.reload();
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
  var total = document.querySelectorAll('.user-checkbox:not(:disabled)');
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

function showLoadingOverlay() {
  var div = document.createElement('div');
  div.id = 'bulkLoadingOverlay';
  div.innerHTML = '<div class="fixed inset-0 bg-white/80 flex items-center justify-center" style="z-index:99999">' +
    '<div class="text-center bg-white rounded-2xl shadow-2xl p-8">' +
    '<div class="animate-spin w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full mx-auto mb-4"></div>' +
    '<div class="text-slate-600 font-semibold">جاري التفعيل...</div></div></div>';
  document.body.appendChild(div);
}
function hideLoadingOverlay() { var el = document.getElementById('bulkLoadingOverlay'); if (el) el.remove(); }

function bulkActivate() {
  var checked = document.querySelectorAll('.user-checkbox:checked');
  var ids = Array.from(checked).map(function(cb) { return cb.value; });
  var count = ids.length;
  if (count === 0) return;
  if (!confirm('هل أنت متأكد من تفعيل ' + count + ' مستخدم؟')) return;

  showLoadingOverlay();

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
    hideLoadingOverlay();
    if (d.success) {
      App.toast(d.message);
      location.reload();
    } else {
      alert(d.message || 'حدث خطأ');
    }
  })
  .catch(function() {
    hideLoadingOverlay();
    alert('حدث خطأ في الاتصال');
  });
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

  if (u.id_photos && u.id_photos.length > 0) {
    html += '<hr class="my-4 border-slate-100"><div><span class="text-xs text-slate-400 block mb-2">🪪 صور الهوية</span><div class="flex flex-wrap gap-3">';
    u.id_photos.forEach(function(p) {
      var src = '{{ url('/files') }}/' + p;
      html += '<a href="javascript:void(0)" onclick="openImageZoom(\'' + src + '\')"><img src="' + src + '" class="w-28 h-28 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition cursor-pointer"></a>';
    });
    html += '</div></div>';
  }

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
@endpush

@endsection
