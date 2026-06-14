<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>QR — {{ $attendance->event->title }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    @media print {
      @page { size: auto; margin: 8mm; }
      body { margin: 0; padding: 0; min-height: auto !important; }
      .card { box-shadow: none; border: 1px solid #e2e8f0; page-break-inside: avoid; }
      #qrcode canvas { width: 160px !important; height: 160px !important; }
      .btn, .btn-ghost { display: none !important; }
    }
  </style>
</head>
<body class="flex items-center justify-center p-6" style="min-height:100vh">
  <div class="card w-full max-w-sm p-8 text-center">
    <div class="text-4xl mb-4">🎓</div>
    <h1 class="text-xl font-extrabold text-slate-900">{{ $attendance->event->title }}</h1>
    <p class="text-sm text-slate-500 mb-2">{{ $attendance->event->type }}</p>
    <p class="text-xs text-slate-400 mb-6">{{ $attendance->event->event_date->format('Y/m/d h:i A') }}</p>

    <div class="bg-white p-4 rounded-xl border border-slate-200 inline-block mb-4">
      <div id="qrcode"></div>
    </div>

    <p class="text-sm font-bold text-slate-700">{{ $attendance->user->name }}</p>
    <p class="text-xs text-slate-400 mb-4">{{ $attendance->user->national_id ?? '' }}</p>

    @if($attendance->isAttended())
      <span class="pill pill-green">✓ تم تسجيل الحضور</span>
    @else
      <span class="pill pill-amber">لم يتم الحضور بعد</span>
    @endif

    <div class="mt-6 flex gap-2 justify-center">
      <button class="btn btn-sm btn-primary" onclick="window.print()">🖨 طباعة</button>
      <a href="{{ route('events.index') }}" class="btn btn-sm btn-ghost">⬅ عودة</a>
    </div>
  </div>

  <script src="{{ asset('js/qrcode.min.js') }}"></script>
  <script>
  new QRCode(document.getElementById('qrcode'), {
    text: '{{ $attendance->qr_code }}',
    width: 200,
    height: 200,
  });
  </script>
</body>
</html>
