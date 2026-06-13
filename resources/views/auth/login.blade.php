<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>تسجيل الدخول — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-md p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg">👥</div>
      <h1 class="text-2xl font-extrabold mt-4 text-slate-900">{{ config('app.name') }}</h1>
      <p class="text-slate-500 text-sm mt-1">تسجيل الدخول</p>
    </div>

    <form id="loginForm" data-ajax="true" action="{{ route('login') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="label">البريد الإلكتروني</label>
        <input class="input @error('email') border-red-400 @enderror" type="email" name="email" value="{{ old('email') }}" placeholder="example@mail.com" required autofocus>
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="label">كلمة المرور</label>
        <input class="input" type="password" name="password" placeholder="••••••••" required>
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded"> تذكرني
      </label>
      <button type="submit" class="btn btn-primary w-full justify-center">دخول</button>
    </form>

    <div  class="mt-4 text-center text-sm text-slate-500">
      ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-sky-600 font-bold hover:underline">تسجيل جديد</a>
    </div>

    <div class="mt-4 p-3 bg-sky-50 border border-sky-100 rounded-xl text-xs text-sky-800 leading-6">
      🛡️ قم بالتسجيل لتتمكن من الدخول الى النظام
    </div>
	
	  <div style="display:none;" class="mt-4 p-3 bg-sky-50 border border-sky-100 rounded-xl text-xs text-sky-800 leading-6">
      🛡️ حساب الأدمن: admin@admin.com / password
    </div>
	
  </div>

  @if($errors->any())
  <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-800 leading-6">
    @foreach($errors->all() as $error)
      <div>⚠️ {{ $error }}</div>
    @endforeach
  </div>
  @endif

  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
