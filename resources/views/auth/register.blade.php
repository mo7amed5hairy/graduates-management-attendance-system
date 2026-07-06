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
    
    <script>
    // خدعة برمجية مباشرة بداخل الصفحة للقط وترجمة رسالة السيرفر فوراً وتخطي الكاش
    (function() {
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            try {
                const response = await originalFetch(...args);
                if (response.status === 422) {
                    const clone = response.clone();
                    const data = await clone.json();
                    if (data.errors) {
                        for (let key in data.errors) {
                            data.errors[key] = data.errors[key].map(msg => {
                                if (msg.toLowerCase().includes('format is invalid') || msg.toLowerCase().includes('phone field format')) {
                                    return 'صيغة رقم الهاتف غير صحيحة، يجب أن يتكون من 11 رقماً ويبدأ بـ 077 أو 078.';
                                }
                                return msg;
                            });
                        }
                        return new Response(JSON.stringify(data), {
                            status: 422,
                            headers: response.headers
                        });
                    }
                }
                return response;
            } catch (e) {
                return originalFetch(...args);
            }
        };
    })();
    </script>

    {{-- صندوق عرض الأخطاء الديناميكي المحدث --}}
    <div id="errorAlertContainer" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 leading-6 hidden mb-4 shadow-sm"></div>

    <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
      @csrf

      {{-- Row 1: الإسم, اسم الأب, اسم الجد, اللقب (محددة بـ 15 حرفاً) --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" maxlength="15" placeholder="الإسم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، الإسم مطلوب')" oninput="this.setCustomValidity('')">
          <!-- 💡 التوضيح المخصص للاسم الأول فقط أسفل الحقل -->
          <small class="text-slate-400 block mt-0.5 text-xs">* الأسم الأول فقط</small>
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" maxlength="15" placeholder="اسم الأب" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الأب مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" maxlength="15" placeholder="اسم الجد" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الجد مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-3">
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" maxlength="15" placeholder="اللقب" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اللقب مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 1.5: اسم الأم, أب الأم, جد الأم (محددة بـ 15 حرفاً) --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">اسم الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_name" maxlength="15" placeholder="اسم الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">أب الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_father_name" maxlength="15" placeholder="أب الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم أب الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">جد الأم <span class="text-rose-500">*</span></label>
          <input class="input" name="mother_grandfather_name" maxlength="15" placeholder="جد الأم" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، اسم جد الأم مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 2: رقم البطاقة, البريد (اختياري), الهاتف --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم البطاقة الوطنية <span class="text-rose-500">*</span></label>
          <input class="input" 
                 name="national_id" 
                 id="nationalId" 
                 placeholder="رقم البطاقة الوطنية (12 رقماً)" 
                 maxlength="12" 
                 required 
                 dir="rtl" 
                 oninput="this.value = this.value.replace(/[^0-9]/g, ''); this.setCustomValidity('');" 
                 oninvalid="this.setCustomValidity('يرجى إدخال رقم البطاقة الوطنية المكون من 12 رقماً')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">البريد الإلكتروني <small>(اختياري — لاسترجاع كلمة المرور)</small></label>
          <input class="input" type="email" name="email" placeholder="example@mail.com" oninvalid="this.setCustomValidity('يرجى إدخال بريد إلكتروني صحيح')" oninput="this.setCustomValidity('')">
        </div>
        <div class="col-span-12 md:col-span-4">
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" id="phoneInput" placeholder="077xxxxxxxx أو 078xxxxxxxx" maxlength="11" required dir="ltr" oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل، رقم الهاتف مطلوب')" oninput="this.setCustomValidity('')">
        </div>
      </div>

      {{-- Row 3: سنة الميلاد, العمر, الجنس, الحالة الوظيفية --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-3">
          <label class="label">سنة الميلاد <span class="text-rose-500">*</span></label>
          <select class="input text-base" name="date_of_birth" id="dateOfBirth" required oninvalid="this.setCustomValidity('يرجى اختيار سنة الميلاد')" onchange="calcAgeFromYear();">
            <option value="">اختر سنة الميلاد...</option>
            @foreach(range(2015, 1970) as $year)
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

      {{-- Row 3.5: الحالة الاجتماعية + عدد الأولاد --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12" id="socialStatusContainer">
          <label class="label">الحالة الاجتماعية <span class="text-rose-500">*</span></label>
          <select class="input w-full text-base" name="social_status" id="socialStatus" required oninvalid="this.setCustomValidity('يرجى تحديد الحالة الاجتماعية')" onchange="toggleChildrenInput();">
            <option value="">اختر الحالة الاجتماعية...</option>
            <option value="أعزب">أعزب / عزباء</option>
            <option value="متزوج">متزوج / متزوجة</option>
            <option value="مطلق">مطلق / مطلقة</option>
            <option value="أرمل">أرمل / أرملة</option>
          </select>
        </div>
        <div class="col-span-12 md:col-span-6 hidden" id="childrenCountContainer">
          <label class="label">عدد الأولاد <span class="text-rose-500">*</span></label>
          <select class="input w-full text-base" name="children_count" id="childrenCount" oninvalid="this.setCustomValidity('يرجى تحديد عدد الأولاد')" onchange="this.setCustomValidity('')">
            @foreach(range(0, 20) as $count)
              <option value="{{ $count }}">{{ $count }}</option>
            @endforeach
          </select>
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
            @foreach(range(2025, 1990) as $year)
              <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Row 6: كلمة المرور --}}
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <div class="relative">
            <input class="input pl-10" type="password" name="password" id="regPassword" placeholder="أقل شيء 8 أحرف" required oninvalid="this.setCustomValidity('حقل كلمة المرور مطلوب')" oninput="this.setCustomValidity('')">
            <button type="button" onclick="togglePassword('regPassword', this)" class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1" tabindex="-1">
              <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
          </div>
        </div>
        <div class="col-span-12 md:col-span-6">
          <label class="label">تأكيد كلمة المرور <span class="text-rose-500">*</span></label>
          <div class="relative">
            <input class="input pl-10" type="password" name="password_confirmation" id="regPasswordConfirm" placeholder="تأكيد كلمة المرور" required oninvalid="this.setCustomValidity('يرجى تأكيد كلمة المرور المطابقة')" oninput="this.setCustomValidity('')">
            <button type="button" onclick="togglePassword('regPasswordConfirm', this)" class="absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1" tabindex="-1">
              <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
          </div>
        </div>
      </div>

      <button type="submit" id="submitBtn" class="btn btn-success w-full justify-center">تسجيل</button>
    </form>

    <div class="mt-4 text-center text-sm text-slate-500">
      لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-sky-600 font-bold hover:underline">تسجيل دخول</a>
    </div>
  </div>

  {{-- نافذة منبثقة تظهر بعد اكتمال التسجيل بنجاح --}}
  <div id="successRegisterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full text-center shadow-2xl border border-slate-100 transform scale-95 transition-transform duration-300" id="modalContentBox">
      
      <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-3xl mb-4 shadow-inner">
        ✓
      </div>
      
      <h3 class="text-xl font-extrabold text-slate-900 mb-2">تم تسجيل بياناتك بنجاح</h3>
      <p class="text-slate-600 text-sm mb-6 leading-relaxed">
        سيتم مراجعة المدخلات من قبل اللجنة التنسيقية خلال أقرب وقت.
      </p>
      
      <hr class="border-slate-100 my-4">
      
      <p class="text-slate-500 text-xs font-semibold mb-4 block">
        📣 يرجى متابعة حساباتنا في إنستغرام:
      </p>

      {{-- الأزرار الثلاثة الملوّنة بالحسابات الرسمية --}}
      <div class="grid grid-cols-1 gap-2 mb-6">
        <a href="https://www.instagram.com/_u/graduates.of.basra?igsh=amJqZ3RoOTM2OHJm" 
           target="_blank" 
           class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-purple-600 via-pink-500 to-yellow-500 text-white font-bold text-sm shadow-sm hover:opacity-95 transition">
          📱 حساب الخريجين (graduates.of.basra)
        </a>
        <a href="https://www.instagram.com/_u/update_iraq?igsh=MW5nNTJxZmV1b3hpZA==" 
           target="_blank" 
           class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 text-white font-bold text-sm shadow-sm hover:opacity-95 transition">
          📱 حساب الشركة المنفذة (update_iraq)
        </a>
        <a href="https://www.instagram.com/_u/s14mv?igsh=MTRiZnRpZ2kzZzdtdA==" 
           target="_blank" 
           class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500 text-white font-bold text-sm shadow-sm hover:opacity-95 transition">
          📱 حساب المطور (s14mv)
        </a>
      </div>

      <button onclick="redirectToLogin()" class="w-full py-3 px-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition text-sm shadow-md">
        حسنًا، الانتقال لتسجيل الدخول
      </button>
    </div>
  </div>

  <script>
  var loginUrl = "{{ route('login') }}"; 

  function showSuccessModal() {
      var modal = document.getElementById('successRegisterModal');
      var box = document.getElementById('modalContentBox');
      if (modal && box) {
          modal.classList.remove('opacity-0', 'pointer-events-none');
          box.classList.remove('scale-95');
          box.classList.add('scale-100');
      }
  }

  function redirectToLogin() {
      window.location.href = loginUrl;
  }

  function cleanArabicNumbers(str) {
    if(!str) return "";
    var res = str.trim();
    var map = { '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4', '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9' };
    for (var key in map) {
      res = res.replace(new RegExp(key, 'g'), map[key]);
    }
    return res.replace(/\s+/g, '');
  }

  // معالجة الإرسال البرمجي الذكي والتحقق الصارم من الـ 15 حرفاً لكل حقول الأسماء وأسماء الأم
  document.getElementById('registerForm').addEventListener('submit', function(e) {
    
    // 🛠️ فحص جافا سكريبت الصارم لمنع كتابة أكثر من 15 حرف في حقول الأسماء وأسماء الأم
    var fName = document.querySelector('input[name="first_name"]')?.value || '';
    var faName = document.querySelector('input[name="father_name"]')?.value || '';
    var gName = document.querySelector('input[name="grandfather_name"]')?.value || '';
    var famName = document.querySelector('input[name="family_name"]')?.value || '';
    var mName = document.querySelector('input[name="mother_name"]')?.value || '';
    var mfName = document.querySelector('input[name="mother_father_name"]')?.value || '';
    var mgName = document.querySelector('input[name="mother_grandfather_name"]')?.value || '';

    if (
      fName.length > 15 || faName.length > 15 || gName.length > 15 || famName.length > 15 || 
      mName.length > 15 || mfName.length > 15 || mgName.length > 15
    ) {
        e.preventDefault(); 
        var errAlert = document.getElementById('errorAlertContainer');
        errAlert.classList.remove('hidden');
        errAlert.innerHTML = '⚠️ عذراً، يجب ألا يتجاوز طول أي من حقول الأسماء أو أسماء الأم 15 حرفاً.';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    e.preventDefault();
    
    var pInput = document.getElementById('phoneInput');
    if(pInput) pInput.value = cleanArabicNumbers(pInput.value);
    
    var nInput = document.getElementById('nationalId');
    if(nInput) nInput.value = cleanArabicNumbers(nInput.value); 

    var form = this;
    var formData = new FormData(form);
    var submitBtn = document.getElementById('submitBtn');
    var errAlert = document.getElementById('errorAlertContainer');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loader-sm"></span> جاري التسجيل...';
    errAlert.classList.add('hidden');
    errAlert.innerHTML = '';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function(res) {
        if (res.status === 422 || !res.ok) {
            return res.json().then(function(errData) { throw errData; });
        }
        return res.json();
    })
    .then(function(data) {
        if (data.success) {
            showSuccessModal();
        } else {
            throw data;
        }
    })
    .catch(function(error) {
        submitBtn.disabled = false;
        submitBtn.innerText = 'تسجيل';
        errAlert.classList.remove('hidden');
        
        if (error.errors) {
            var errorMessages = [];
            for (var key in error.errors) {
                if (error.errors.hasOwnProperty(key)) {
                    errorMessages.push('⚠️ ' + error.errors[key][0]);
                }
            }
            errAlert.innerHTML = errorMessages.join('<br>');
        } else if (error.message) {
            errAlert.innerHTML = '⚠️ ' + error.message;
        } else {
            errAlert.innerHTML = '⚠️ حدث خطأ أثناء معالجة البيانات، يرجى مراجعة المدخلات والمحاولة لاحقاً.';
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });

  function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var openEye = btn.querySelector('.eye-open');
    var closedEye = btn.querySelector('.eye-closed');
    if (input.type === 'password') {
      input.type = 'text';
      openEye.classList.add('hidden');
      closedEye.classList.remove('hidden');
    } else {
      input.type = 'password';
      openEye.classList.remove('hidden');
      closedEye.classList.add('hidden');
    }
  }

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
      statusContainer.className = "col-span-12 md:col-span-6";
    } else {
      childrenContainer.classList.add('hidden');
      statusContainer.className = "col-span-12";
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
            opt.value = item.id;
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
</body>
</html>