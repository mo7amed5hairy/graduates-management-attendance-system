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

      {{-- Row 1: الإسم, اسم الأب, اسم الجد, اللقب --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" placeholder="الإسم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، الإسم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" placeholder="اسم الأب" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الأب مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" placeholder="اسم الجد" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الجد مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" placeholder="اللقب" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اللقب مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 1.5: اسم الأم, أب الأم, جد الأم --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_name" placeholder="اسم الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">أب الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_father_name" placeholder="أب الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم أب الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">جد الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_grandfather_name" placeholder="جد الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم جد الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 2: رقم البطاقة, البريد (اختياري), الهاتف --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="رقم البطاقة الوطنية" required dir="rtl" oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، رقم البطاقة الوطنية مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">البريد الإلكتروني <small>(اختياري — لاسترجاع كلمة المرور)</small></label>
          <input class="input" type="email" name="email" placeholder="example@mail.com" oninvalid="this.setCustomValidity('يرجى إدخال بريد إلكتروني صحيح')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="077xxxxxxxx" maxlength="11" required dir="ltr" oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، رقم الهاتف مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 3: تاريخ الميلاد (سنة فقط), العمر (auto), الجنس, الحالة الوظيفية --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="date_of_birth" id="dateOfBirth" required oninvalid="this.setCustomValidity('يرجى اختيار سنة الميلاد')" onchange="calcAgeFromYear();">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(date('Y'), 1900) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">العمر</label>
          <input class="input" type="number" name="age" id="age" placeholder="--" readonly style="background:#f1f5f9">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجنس <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="gender" required oninvalid="this.setCustomValidity('يرجى تحديد الجنس')" onchange="this.setCustomValidity('')">
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الحالة الوظيفية <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="job_status" required oninvalid="this.setCustomValidity('يرجى تحديد الحالة الوظيفية')" onchange="this.setCustomValidity('')">
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

      {{-- Row 3.5: الحالة الاجتماعية + عدد الأولاد (تم تعديل الـ values لتطابق قواعد البيانات) --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6 transition-all duration-300" id="socialStatusContainer">
          <label class="label">الحالة الاجتماعية <span class="text-rose-500">*</span></label>
          <select class="input w-full text-base" name="social_status" id="socialStatus" required oninvalid="this.setCustomValidity('يرجى تحديد الحالة الاجتماعية')" onchange="toggleChildrenInput();">
            <option value="">اختر الحالة الاجتماعية...</option>
            <option value="أعزب">أعزب / عزباء</option>
            <option value="متزوج">متزوج / متزوجة</option>
            <option value="مطلق">مطلق / مطلقة</option>
            <option value="أرمل">أرمل / أرملة</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-6 hidden transition-all duration-300" id="childrenCountContainer">
          <label class="label">عدد الأولاد <span class="text-rose-500">*</span></label>
          <input class="input w-full" type="number" name="children_count" id="childrenCount" placeholder="من 0 إلى 10" min="0" max="10" value="0" oninvalid="this.setCustomValidity('يرجى تحديد عدد الأولاد من 0 إلى 10')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 4: المحافظة + عنوان السكن الحالى --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="governorate" id="governorate" required oninvalid="this.setCustomValidity('يرجى اختيار المحافظة')" onchange="this.setCustomValidity('')">
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-9">
          <label class="label">عنوان السكن الحالى <span class="text-rose-500">*</span></label>
          <textarea class="input" name="address" rows="3" placeholder="العنوان بالتفصيل" required oninvalid="this.setCustomValidity('يرجى كتابة عنوان السكن التفصيلي')" oninput="this.setCustomValidity('')"></textarea>
        </div>
      </div>

      {{-- Row 5: التحصيل الدراسى, الكلية/المعهد, سنة التخرج --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">التحصيل الدراسى <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="qualification_id" id="qualificationId" required oninvalid="this.setCustomValidity('يرجى اختيار التحصيل الدراسي')" onchange="loadFacultiesR();">
            <option value="">اختر المؤهل...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">الكلية / المعهد <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="qualification_faculty_id" id="facultyId" required disabled oninvalid="this.setCustomValidity('يرجى اختيار الكلية أو المعهد')" onchange="this.setCustomValidity('')">
            <option value="">اختر المؤهل أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="graduation_year" required oninvalid="this.setCustomValidity('يرجى تحديد سنة التخرج')" onchange="this.setCustomValidity('')">
            <option value="">اختر سنة التخرج...</option>
            @foreach(range(date('Y') + 5, 1950) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Row 6: كلمة المرور --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف" required oninvalid="this.setCustomValidity('حقل كلمة المرور مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-6">
          <label class="label">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required oninvalid="this.setCustomValidity('يرجى تأكيد كلمة المرور المطابقة')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      <button type="submit" class="btn btn-success w-full justify-center">تسجيل</button>
    </form>

    <div class="mt-4 text-center text-sm text-slate-500">
      لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-sky-600 font-bold hover:underline">تسجيل دخول</a>
    </div>

    @if($errors->any())
    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-800 leading-6">
      @foreach($errors->all() as $error)
        <div>
          ⚠️ 
          @if(str_contains($error, 'phone'))
            صيغة رقم الهاتف غير صحيحة، يرجى كتابة الرقم بالكامل (مثال: 07705666666)
          @elseif(str_contains($error, 'email'))
            البريد الإلكتروني المستخدم مسجل مسبقاً أو غير صحيح
          @elseif(str_contains($error, 'password'))
            كلمة المرور غير متطابقة أو أقل من 8 أحرف
          @else
            {{ $error }}
          @endif
        </div>
      @endforeach
    </div>
    @endif
  </div>

  <script>
  function calcAgeFromYear() {
    var selectField = document.getElementById('dateOfBirth');
    var year = selectField.value;
    var ageField = document.getElementById('age');
    
    selectField.setCustomValidity('');
    
    if (!year) { ageField.value = ''; return; }
    ageField.value = new Date().getFullYear() - parseInt(year);
  }

  function toggleChildrenInput() {
    var selectField = document.getElementById('socialStatus');
    var status = selectField.value;
    var childrenContainer = document.getElementById('childrenCountContainer');
    var childrenInput = document.getElementById('childrenCount');
    var statusContainer = document.getElementById('socialStatusContainer');
    
    selectField.setCustomValidity('');
    
    if (status === 'متزوج' || status === 'مطلق' || status === 'أرمل') {
      childrenContainer.classList.remove('hidden');
      statusContainer.classList.remove('col-span-12');
      statusContainer.classList.add('md:col-span-6');
      childrenInput.required = true;
    } else {
      childrenContainer.classList.add('hidden');
      statusContainer.classList.remove('md:col-span-6');
      statusContainer.classList.add('col-span-12');
      childrenInput.required = false;
      childrenInput.value = '0';
    }
  }

  function loadFacultiesR() {
    var selectField = document.getElementById('qualificationId');
    var qualId = selectField.value;
    var facSel = document.getElementById('facultyId');
    
    selectField.setCustomValidity('');
    
    if (!qualId) {
      facSel.innerHTML = '<option value="">اختر المؤهل أولاً...</option>';
      facSel.disabled = true;
      return;
    }
    facSel.disabled = true;
    facSel.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch('{{ url('/api/qualifications') }}' + '/' + qualId + '/faculties', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
    var gSel = document.getElementById('governorate');
    gSel.disabled = true;
    gSel.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch('{{ url('/api/governorates') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        gSel.innerHTML = '<option value="">اختر المحافظة...</option>';
        if (d.success && d.data) {
          d.data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.name;
            opt.textContent = item.name;
            gSel.appendChild(opt);
          });
        }
        gSel.disabled = false;
      })
      .catch(function() {
        gSel.innerHTML = '<option value="">خطأ في التحميل</option>';
        gSel.disabled = false;
      });

    var qSel = document.getElementById('qualificationId');
    qSel.disabled = true;
    qSel.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch('{{ url('/api/qualifications') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        qSel.innerHTML = '<option value="">اختر المؤهل...</option>';
        if (d.success && d.data) {
          d.data.forEach(function(item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.name;
            qSel.appendChild(opt);
          });
        }
        qSel.disabled = false;
      })
      .catch(function() {
        qSel.innerHTML = '<option value="">خطأ في التحميل</option>';
        qSel.disabled = false;
      });
  });
  </script>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>