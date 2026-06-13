@extends('layouts.admin')

@section('title', 'مسح QR')
@section('page_title', 'مسح QR للحضور')
@section('page_subtitle', $event->title)

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="card p-6 text-center mb-6">
    <p class="text-sm text-slate-500 mb-4">امسح QR الخاص بالخريج لتسجيل حضوره في الفعالية</p>
    <div class="mb-4">
      <p class="text-lg font-bold text-slate-900">{{ $event->title }}</p>
      <p class="text-sm text-slate-500">{{ $event->event_date->format('Y/m/d h:i A') }}</p>
    </div>

    <div class="max-w-sm mx-auto">
      <div id="reader" style="width:100%"></div>
    </div>

    <div class="mt-6">
      <p class="text-sm text-slate-500 mb-2">أو أدخل رمز QR يدوياً</p>
      <div class="flex gap-2 max-w-md mx-auto">
        <input class="input flex-1" id="manualQr" placeholder="رمز QR">
        <button class="btn btn-primary" onclick="processManualQr()">تحقق</button>
      </div>
    </div>

    <div id="scanResult" class="mt-4 hidden"></div>
  </div>

  <div class="card p-5">
    <h3 class="font-bold text-slate-900 mb-4">آخر عمليات المسح</h3>
    <div id="recentScans">
      <p class="text-sm text-slate-400 text-center">لم يتم مسح أي QR بعد</p>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var eventId = {{ $event->id }};

function processQrCode(qrData) {
  var parts = qrData.split('-');
  if (parts.length < 2) {
    showResult('رمز QR غير صالح', 'error');
    return;
  }

  var attId = parts[0];
  var userId = parts[parts.length - 1];

  fetch('{{ route('admin.attendance.mark') }}', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ event_id: eventId, user_id: userId })
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) {
      showResult('✓ تم تسجيل حضور: ' + d.user_name, 'success');
      addToRecent(d.user_name);
    } else {
      showResult('✕ ' + d.message, 'error');
    }
  }).catch(function(e) {
    showResult('حدث خطأ في الاتصال', 'error');
  });
}

function processManualQr() {
  var qr = document.getElementById('manualQr').value.trim();
  if (!qr) { App.toast('أدخل رمز QR', 'error'); return; }
  processQrCode(qr);
}

function showResult(msg, type) {
  var div = document.getElementById('scanResult');
  div.classList.remove('hidden');
  div.className = 'mt-4 p-3 rounded-xl text-sm font-bold ' +
    (type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200');
  div.textContent = msg;
  setTimeout(function() { div.classList.add('hidden'); }, 5000);
}

function addToRecent(name) {
  var div = document.getElementById('recentScans');
  var item = document.createElement('div');
  item.className = 'flex items-center gap-2 py-2 border-b border-slate-50 text-sm';
  item.innerHTML = '<span class="text-green-600">✓</span> <span class="font-semibold">' + name + '</span> <span class="text-xs text-slate-400">' + new Date().toLocaleString('ar-EG') + '</span>';
  div.insertBefore(item, div.firstChild);
  var empty = div.querySelector('p');
  if (empty) empty.remove();
}
</script>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function onScanSuccess(decodedText, decodedResult) {
  processQrCode(decodedText);
}

var html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
  { facingMode: "environment" },
  { fps: 10, qrbox: { width: 250, height: 250 } },
  onScanSuccess
).catch(function(err) {
  document.getElementById('reader').innerHTML = '<p class="text-sm text-slate-400 py-8">تعذر الوصول إلى الكاميرا. استخدم الإدخال اليدوي.</p>';
});
</script>
@endpush
