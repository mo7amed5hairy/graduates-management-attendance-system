<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>تسجيل خريج جديد — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
select.input:disabled { opacity: 0.5; cursor: not-allowed; }
.loader-sm { display: inline-block; width: 1rem; height: 1rem; border: 2px solid #e2e8f0; border-top-color: #3b82f6; border-radius: 50%; animation: spin 0.6s linear infinite; vertical-align: middle; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-6xl p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg">🎓</div>
      <h1 class="text-2xl font-extrabold mt-4 text-slate-900">{{ config('app.name') }}</h1>
      <p class="text-slate-500 text-sm mt-1">تسجيل خريج جديد — البيانات ستكون قيد المراجعة</p>
    </div>

    <form id="registerForm" data-ajax="true" action="{{ route('register') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
      @csrf

      {{-- Row 1: الإسم الرباعى مع اللقب, اسم الأم الرباعى, رقم البطاقة الوطنية --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">الإسم الرباعى مع اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="name" placeholder="الاسم الرباعي مع اللقب" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم الرباعى <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_name" placeholder="اسم الأم الرباعي" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="رقم البطاقة الوطنية" required dir="ltr">
        </div>
      </div>

      {{-- Row 2: تاريخ الميلاد, العمر (auto), الجنس --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">تاريخ الميلاد <span class="text-rose-500">*</span></label>
          <input class="input" type="date" name="date_of_birth" id="dateOfBirth" required onchange="calculateAge()">
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" name="age" id="age" placeholder="--" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الجنس</label>
          <select class="input" name="gender">
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="077xxxxxxxx" required dir="ltr" maxlength="11" pattern="077\d{8}" title="يجب أن يبدأ ب 077 ويتكون من 11 رقماً">
        </div>
      </div>

      {{-- Row 3: الحالة الاجتماعية --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">الحالة الاجتماعية <span class="text-rose-500">*</span></label>
          <select class="input" name="social_status" id="socialStatus" required onchange="toggleChildrenCount()">
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
        <div class="col-span-12 md:col-span-2" id="childrenCountWrap" style="display:none">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" id="childrenCount" min="0" placeholder="أدخل عدد الأولاد">
        </div>
      </div>

      {{-- Row 4: التحصيل الدراسى والكلية --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_id" id="qualificationId" required>
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد <span class="text-rose-500">*</span></label>
          <select class="input" name="qualification_faculty_id" id="qualificationFacultyId" required disabled>
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

      {{-- Row 5: المحافظة وعنوان السكن الحالى --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate" id="governorate" required>
            <option value="">اختر المحافظة...</option>
            @foreach($governorates as $gov)
              <option value="{{ $gov->name }}">{{ $gov->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى <span class="text-rose-500">*</span></label>
          <input class="input" name="address" placeholder="العنوان بالتفصيل" required>
        </div>
      </div>

      {{-- Row 6: كلمة المرور --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف" required>
        </div>
        <div class="col-span-12 md:col-span-6">
          <label class="label">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
        </div>
      </div>

      <button type="submit" class="btn btn-success w-full justify-center">تسجيل</button>
    </form>

    <div class="mt-4 text-center text-sm text-slate-500">
      لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-sky-600 font-bold hover:underline">تسجيل دخول</a>
    </div>

    @if($errors->any())
    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-800 leading-6">
      @foreach($errors->all() as $error)
        <div>⚠️ {{ $error }}</div>
      @endforeach
    </div>
    @endif
  </div>

  <script>
  var apiBase = '{{ url('/api') }}';

  function calculateAge() {
    var dob = document.getElementById('dateOfBirth').value;
    var ageField = document.getElementById('age');
    if (!dob) { ageField.value = ''; return; }
    var birthDate = new Date(dob);
    var today = new Date();
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
    ageField.value = age >= 0 ? age : 0;
  }

  function toggleChildrenCount() {
    var val = document.getElementById('socialStatus').value;
    var wrap = document.getElementById('childrenCountWrap');
    if (val.includes('لديه أطفال')) {
      wrap.style.display = 'block';
    } else {
      wrap.style.display = 'none';
      document.getElementById('childrenCount').value = '';
    }
  }

  function loadSelect(url, selectId, placeholder) {
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

  function loadFaculties() {
    var qualId = document.getElementById('qualificationId').value;
    var facSel = document.getElementById('qualificationFacultyId');

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
    loadSelect(apiBase + '/qualifications', 'qualificationId', 'اختر المؤهل...');
  });

  document.getElementById('qualificationId').addEventListener('change', loadFaculties);
  </script>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
