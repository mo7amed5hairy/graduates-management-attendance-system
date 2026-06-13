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

      {{-- Row 1: الاسم الرباعي, الرقم القومي, البريد, الهاتف, العمر, الجنس --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الاسم الرباعي <span class="text-rose-500">*</span></label>
          <input class="input" name="name" placeholder="الاسم الكامل" required>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">الرقم القومي <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="الرقم القومي" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
          <input class="input" type="email" name="email" placeholder="example@mail.com" required>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="05xxxxxxxx" required dir="ltr">
        </div>
        <div class="col-span-12 md:col-span-1">
          <label class="label">العمر</label>
          <input class="input" type="number" name="age" placeholder="25" min="1" max="150">
        </div>
        <div class="col-span-12 md:col-span-1">
          <label class="label">الجنس</label>
          <select class="input" name="gender">
            <option value="">اختر</option>
            <option value="ذكر">ذكر</option>
            <option value="أنثى">أنثى</option>
          </select>
        </div>
      </div>

      {{-- Row 2: المحافظة, نوع المؤسسة, نوع الجامعة, الجامعة/معهد, القسم/تخصص --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-2">
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <select class="input" name="governorate" id="governorate" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">نوع المؤسسة <span class="text-rose-500">*</span></label>
          <select class="input" name="institution_type" id="institutionType" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-2">
          <label class="label">نوع الجامعة <span class="text-rose-500">*</span></label>
          <select class="input" name="university_type" id="universityType" required>
            <option value="">اختر...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">الجامعة / المعهد <span class="text-rose-500">*</span></label>
          <select class="input" name="institution_id" id="institution" required disabled>
            <option value="">اختر أولاً...</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">القسم / التخصص <span class="text-rose-500">*</span></label>
          <select class="input" name="department_id" id="department" required disabled>
            <option value="">اختر أولاً...</option>
          </select>
        </div>
      </div>

      {{-- Row 3: سنة التخرج, الحالة الوظيفية, كلمة المرور, تأكيد كلمة المرور --}}
      <div class="grid grid-cols-12 gap-4">
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
        <div class="col-span-12 md:col-span-4">
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف" required>
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
        </div>
      </div>

      <div>
        <label class="label">العنوان</label>
        <textarea class="input" name="address" placeholder="العنوان بالتفصيل" rows="2"></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">صورة الهوية (يمكن اختيار أكثر من صورة)</label>
          <input class="input" type="file" name="id_photos[]" multiple accept="image/*" onchange="previewImages(this, 'idPreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="idPreview"></div>
        </div>
        <div>
          <label class="label">إثبات السكن (يمكن اختيار أكثر من صورة)</label>
          <input class="input" type="file" name="residence_proof[]" multiple accept="image/*" onchange="previewImages(this, 'residencePreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="residencePreview"></div>
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

  document.addEventListener('DOMContentLoaded', function() {
    loadSelect(apiBase + '/governorates', 'governorate', 'اختر المحافظة...');
    loadSelect(apiBase + '/institution-types', 'institutionType', 'اختر النوع...');
    loadSelect(apiBase + '/university-types', 'universityType', 'اختر النوع...');
  });

  function loadInstitutions() {
    var govId = document.getElementById('governorate').value;
    var instTypeId = document.getElementById('institutionType').value;
    var uniTypeId = document.getElementById('universityType').value;
    var instSel = document.getElementById('institution');
    var deptSel = document.getElementById('department');

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

  function loadDepartments() {
    var instId = document.getElementById('institution').value;
    var deptSel = document.getElementById('department');

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

  document.getElementById('governorate').addEventListener('change', loadInstitutions);
  document.getElementById('institutionType').addEventListener('change', loadInstitutions);
  document.getElementById('universityType').addEventListener('change', loadInstitutions);
  document.getElementById('institution').addEventListener('change', loadDepartments);

  function previewImages(input, previewId) {
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
  </script>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
