<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>بياناتي — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-3xl p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-lg">🎓</div>
      <h1 class="text-2xl font-extrabold mt-4 text-slate-900">بياناتي الشخصية</h1>
      <p class="text-slate-500 text-sm mt-1">{{ config('app.name') }}</p>
    </div>

    @if($user)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <span class="text-xs text-slate-400 block">الإسم الرباعى مع اللقب</span>
        <span class="font-semibold">{{ $user->name }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">اسم الأم الرباعى</span>
        <span class="font-semibold">{{ trim($user->mother_name . ' ' . $user->mother_father_name . ' ' . $user->mother_grandfather_name) ?: 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم البطاقة الوطنية</span>
        <span class="font-semibold">{{ $user->national_id ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">رقم الهاتف</span>
        <span class="font-semibold">{{ $user->phone ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ الميلاد</span>
        <span class="font-semibold">{{ $user->date_of_birth ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">العمر</span>
        <span class="font-semibold">{{ $user->age ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الجنس</span>
        <span class="font-semibold">{{ $user->gender ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الاجتماعية</span>
        <span class="font-semibold">{{ $user->social_status ?? 'غير محدد' }}</span>
        @if(str_contains($user->social_status ?? '', 'لديه أطفال') && $user->children_count !== null)
          <span class="text-xs text-slate-500">عدد الأولاد: {{ $user->children_count }}</span>
        @endif
      </div>
      <div>
        <span class="text-xs text-slate-400 block">التحصيل الدراسى</span>
        <span class="font-semibold">{{ $user->qualification?->name ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الكلية / المعهد</span>
        <span class="font-semibold">{{ $user->qualificationFaculty?->name ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">سنة التخرج</span>
        <span class="font-semibold">{{ $user->graduation_year ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">الحالة الوظيفية</span>
        <span class="font-semibold">{{ $user->job_status ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">المحافظة</span>
        <span class="font-semibold">{{ $user->governorate_name }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">عنوان السكن الحالى</span>
        <span class="font-semibold">{{ $user->address ?? 'غير محدد' }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ التسجيل</span>
        <span class="font-semibold">{{ $user->created_at->format('Y/m/d') }}</span>
      </div>
      <div>
        <span class="text-xs text-slate-400 block">حالة الحساب</span>
        <span class="font-semibold">
          @if($user->approval_status === 'approved')
            <span class="text-green-600">✅ معتمد</span>
          @elseif($user->approval_status === 'rejected')
            <span class="text-rose-600">❌ مرفوض</span>
            @if($user->rejection_reason)
              <div class="text-xs text-rose-500 mt-1">{{ $user->rejection_reason }}</div>
            @endif
          @else
            <span class="text-amber-600">⏳ قيد المراجعة</span>
          @endif
        </span>
      </div>
    </div>
    @else
    <div class="text-center py-12">
      <div class="text-6xl mb-4">🔍</div>
      <h2 class="text-xl font-extrabold text-slate-900 mb-2">لم يتم العثور على بيانات</h2>
      <p class="text-slate-500 text-sm mb-4">سجل دخول أولاً أو استخدم رابط البيانات المخصص المرسل إليك</p>
      <a href="{{ route('login') }}" class="btn btn-primary">تسجيل الدخول</a>
    </div>
    @endif
  </div>

  <script>
  @if(!$user)
  (function() {
    var token = null;
    try { token = localStorage.getItem('access_token'); } catch(e) {}
    if (token) {
      window.location.href = '{{ url('/checkmydetails') }}/' + token;
    }
  })();
  @endif
  </script>

</body>
</html>
