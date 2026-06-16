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

{{-- Edit User Modals (مطابق لصفحة التسجيل) --}}
@foreach($users as $user)
<div class="modal-overlay" id="editUserModal{{ $user->id }}">
  <div class="modal-content modal-content-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $user->first_name }} {{ $user->father_name }} {{ $user->grandfather_name }} {{ $user->family_name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.update', $user) }}" method="POST">
      @csrf
      @method('PUT')

      {{-- Row 1: first_name, father_name, grandfather_name, family_name --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" value="{{ $user->first_name }}" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" value="{{ $user->father_name }}" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" value="{{ $user->grandfather_name }}" required>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" value="{{ $user->family_name }}" required>
        </div>
      </div>

      {{-- Row 1.5: mother_name, mother_father_name, mother_grandfather_name --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم</label>
          <input class="input" name="mother_name" value="{{ $user->mother_name }}">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">أب الأم</label>
          <input class="input" name="mother_father_name" value="{{ $user->mother_father_name }}">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">جد الأم</label>
          <input class="input" name="mother_grandfather_name" value="{{ $user->mother_grandfather_name }}">
        </div>
      </div>

      {{-- Row 2: national_id, email (optional), phone --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" value="{{ $user->national_id }}" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">البريد الإلكتروني <small>(اختياري)</small></label>
          <input class="input" type="email" name="email" value="{{ $user->email }}">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" value="{{ $user->phone }}" placeholder="077xxxxxxxx" required dir="ltr">
        </div>
      </div>

      {{-- Row 3: date_of_birth (year), age (auto), gender, job_status --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد</label>
          <select class="input" name="date_of_birth" id="editDateOfBirth{{ $user->id }}" onchange="calcAgeEdit({{ $user->id }})">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(date('Y'), 1900) as $year)
              <option value="{{ $year }}" {{ (int)$user->date_of_birth === $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" id="editAge{{ $user->id }}" value="{{ $user->age }}" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجنس</label>
          <select class="input" name="gender">
            <option value="">اختر</option>
            <option value="ذكر" {{ $user->gender === 'ذكر' ? 'selected' : '' }}>ذكر</option>
            <option value="أنثى" {{ $user->gender === 'أنثى' ? 'selected' : '' }}>أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الحالة الوظيفية <span class="text-rose-500">*</span></label>
          <select class="input" name="job_status" required>
            <option value="">اختر...</option>
            <option value="موظف" {{ $user->job_status === 'موظف' ? 'selected' : '' }}>موظف</option>
            <option value="غير موظف" {{ $user->job_status === 'غير موظف' ? 'selected' : '' }}>غير موظف</option>
            <option value="طالب" {{ $user->job_status === 'طالب' ? 'selected' : '' }}>طالب</option>
            <option value="صاحب عمل" {{ $user->job_status === 'صاحب عمل' ? 'selected' : '' }}>صاحب عمل</option>
            <option value="متقاعد" {{ $user->job_status === 'متقاعد' ? 'selected' : '' }}>متقاعد</option>
            <option value="أخرى" {{ $user->job_status === 'أخرى' ? 'selected' : '' }}>أخرى</option>
          </select>
        </div>
      </div>

      {{-- Row 3.5: social_status + children_count --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">الحالة الاجتماعية <span class="text-rose-500">*</span></label>
          <select class="input" name="social_status" id="editSocialStatus{{ $user->id }}" required onchange="toggleChildrenEdit({{ $user->id }})">
            <option value="">اختر...</option>
            <option value="أعزب" {{ $user->social_status === 'أعزب' ? 'selected' : '' }}>أعزب</option>
            <option value="متزوج" {{ $user->social_status === 'متزوج' ? 'selected' : '' }}>متزوج</option>
            <option value="مطلق" {{ $user->social_status === 'مطلق' ? 'selected' : '' }}>مطلق</option>
            <option value="أرمل" {{ $user->social_status === 'أرمل' ? 'selected' : '' }}>أرمل</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-6" id="editChildrenWrap{{ $user->id }}" style="{{ in_array($user->social_status, ['متزوج', 'مطلق', 'أرمل']) ? '' : 'display:none' }}">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" value="{{ $user->children_count ?? 0 }}" min="0" max="10">
        </div>
      </div>

      {{-- Row 4: governorate + address --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate" id="editGovernorate{{ $user->id }}" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى</label>
          <input class="input" name="address" value="{{ $user->address }}" placeholder="العنوان بالتفصيل">
        </div>
      </div>

      {{-- Row 5: qualification, faculty, graduation_year --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى</label>
          <select class="input" name="qualification_id" id="editQualification{{ $user->id }}" onchange="loadFacultiesEdit({{ $user->id }})">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد</label>
          <select class="input" name="qualification_faculty_id" id="editFaculty{{ $user->id }}" disabled>
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <select class="input" name="graduation_year" required>
            <option value="">اختر سنة التخرج...</option>
            @foreach(range(date('Y') + 5, 1950) as $year)
              <option value="{{ $year }}" {{ (int)$user->graduation_year === $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Row 6: password (اختياري في التعديل) + status --}}
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

{{-- Create User Modal (مطابق لصفحة التسجيل) --}}
<div class="modal-overlay" id="createUserModal">
  <div class="modal-content modal-content-xl p-6 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مستخدم جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.store') }}" method="POST">
      @csrf

      {{-- Row 1: first_name, father_name, grandfather_name, family_name --}}
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

      {{-- Row 1.5: mother_name, mother_father_name, mother_grandfather_name --}}
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

      {{-- Row 2: national_id, email (optional), phone --}}
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
          <input class="input" name="phone" placeholder="077xxxxxxxx" required dir="ltr">
        </div>
      </div>

      {{-- Row 3: date_of_birth (year), age (auto), gender, job_status --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد <span class="text-rose-500">*</span></label>
          <select class="input" name="date_of_birth" id="createDateOfBirth" required onchange="calcAgeC()">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(date('Y'), 1900) as $year)
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

      {{-- Row 3.5: social_status + children_count --}}
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
          <input class="input" type="number" name="children_count" id="createChildrenCount" min="0" max="10" placeholder="من 0 إلى 10">
        </div>
      </div>

      {{-- Row 4: governorate + address --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate" id="createGovernorate" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى</label>
          <input class="input" name="address" placeholder="العنوان بالتفصيل">
        </div>
      </div>

      {{-- Row 5: qualification, faculty, graduation_year --}}
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى</label>
          <select class="input" name="qualification_id" id="createQualification" onchange="loadFacultiesCreate()">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد</label>
          <select class="input" name="qualification_faculty_id" id="createFaculty" disabled>
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <select class="input" name="graduation_year" required>
            <option value="">اختر سنة التخرج...</option>
            @foreach(range(date('Y') + 5, 1950) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Row 6: password --}}
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

@push('scripts')
<script>
var apiBase = '{{ url('/api') }}';

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

function loadSelectQuals(url, selectId, placeholder, selectedId) {
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
        loadFacultiesFor(facId, selectedId, null);
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

// Load governorates & qualifications for edit modals
document.addEventListener('DOMContentLoaded', function loadEditDropdowns() {
  @foreach($users as $user)
    loadSelect(apiBase + '/governorates', 'editGovernorate{{ $user->id }}', 'اختر المحافظة...', '{{ $user->governorate }}');
    loadSelectQuals(apiBase + '/qualifications', 'editQualification{{ $user->id }}', 'اختر المؤهل...', '{{ $user->qualification_id }}');
  @endforeach
});

// ===== EDIT MODAL FUNCTIONS =====
function calcAgeEdit(userId) {
  var sel = document.getElementById('editDateOfBirth' + userId);
  var year = sel.value;
  var ageField = document.getElementById('editAge' + userId);
  if (!year) { ageField.value = ''; return; }
  ageField.value = new Date().getFullYear() - parseInt(year);
}

function toggleChildrenEdit(userId) {
  var status = document.getElementById('editSocialStatus' + userId).value;
  var wrap = document.getElementById('editChildrenWrap' + userId);
  if (status === 'متزوج' || status === 'مطلق' || status === 'أرمل') {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
}

function loadFacultiesEdit(userId) {
  var qualId = document.getElementById('editQualification' + userId).value;
  loadFacultiesFor('editFaculty' + userId, qualId, null);
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
