<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>نسيت كلمة المرور — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-md p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg">🔑</div>
      <h1 class="text-2xl font-extrabold mt-4 text-slate-900">نسيت كلمة المرور</h1>
      <p class="text-slate-500 text-sm mt-1">أدخل بريدك الإلكتروني أو رقم البطاقة الوطنية</p>
    </div>

    <form id="forgotPasswordForm" data-ajax="true" action="{{ route('password.email') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="label">البريد الإلكتروني أو رقم البطاقة الوطنية</label>
        @if($email)
          <input class="input" type="email" name="email" value="{{ $email }}" readonly style="background:#f1f5f9;cursor:not-allowed" dir="ltr">
          <p class="text-xs text-slate-400 mt-1">تم التعرف عليك تلقائياً. سيتم إرسال الرابط إلى هذا البريد.</p>
          <input type="hidden" name="identifier" value="{{ $email }}">
        @else
          <input class="input" name="identifier" value="{{ old('identifier') }}" placeholder="example@domain.com أو رقم البطاقة" required autofocus dir="ltr">
        @endif
      </div>
      <button type="submit" class="btn btn-primary w-full justify-center">إرسال رابط إعادة التعيين</button>
    </form>

    <div class="mt-4 text-center text-sm text-slate-500">
      <a href="{{ route('login') }}" class="text-sky-600 font-bold hover:underline">⬅ العودة لتسجيل الدخول</a>
    </div>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
