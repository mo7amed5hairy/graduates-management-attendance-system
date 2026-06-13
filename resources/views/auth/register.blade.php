<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>تسجيل خريج جديد — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-2xl p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg">🎓</div>
      <h1 class="text-2xl font-extrabold mt-4 text-slate-900">{{ config('app.name') }}</h1>
      <p class="text-slate-500 text-sm mt-1">تسجيل خريج جديد — البيانات ستكون قيد المراجعة</p>
    </div>

    <form id="registerForm" data-ajax="true" action="{{ route('register') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الاسم الرباعي <span class="text-rose-500">*</span></label>
          <input class="input" name="name" placeholder="الاسم الكامل" required>
        </div>
        <div>
          <label class="label">الرقم القومي <span class="text-rose-500">*</span></label>
          <input class="input" name="national_id" placeholder="الرقم القومي" required dir="ltr">
        </div>
        <div>
          <label class="label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
          <input class="input" type="email" name="email" placeholder="example@mail.com" required>
        </div>
        <div>
          <label class="label">رقم الهاتف <span class="text-rose-500">*</span></label>
          <input class="input" name="phone" placeholder="05xxxxxxxx" required dir="ltr">
        </div>
        <div>
          <label class="label">المحافظة <span class="text-rose-500">*</span></label>
          <input class="input" name="governorate" placeholder="المحافظة" required>
        </div>
        <div>
          <label class="label">الجامعة <span class="text-rose-500">*</span></label>
          <input class="input" name="university" placeholder="الجامعة" required>
        </div>
        <div>
          <label class="label">الكلية <span class="text-rose-500">*</span></label>
          <input class="input" name="faculty" placeholder="الكلية" required>
        </div>
        <div>
          <label class="label">سنة التخرج <span class="text-rose-500">*</span></label>
          <input class="input" type="number" name="graduation_year" placeholder="مثال: 2024" min="1950" max="{{ date('Y') + 5 }}" required>
        </div>
        <div>
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
        <div>
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف" required>
        </div>
        <div>
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
