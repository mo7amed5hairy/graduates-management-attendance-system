@extends('layouts.admin')

@section('title', 'استيراد')
@section('page_title', 'استيراد الخريجين')
@section('page_subtitle', 'استيراد بيانات الخريجين من ملف Excel')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="card p-6 mb-6">
    <h3 class="font-bold text-slate-900 mb-2">تعليمات الاستيراد</h3>
    <ul class="text-sm text-slate-600 space-y-1 list-disc pr-5">
      <li>الملف يجب أن يكون بصيغة Excel (xlsx, xls) أو CSV</li>
      <li>صيغة Excel المدعومة تحتوي على الأعمدة التالية بالترتيب:
        <span class="block mt-1 text-xs text-slate-500">ت | الحالة | فحص المكرر | الاسم الكامل مع اللقب | العمر | الجنس | عنوان السكن الحالي | سنة تخرج | التحصيل الدراسي | رقم الهاتف | اسم الأم الرباعي | الحالة الاجتماعية</span>
      </li>
      <li>الاسم الرباعي يتم تقسيمه تلقائياً (الاسم، الأب، الجد، اللقب)</li>
      <li>اسم الأم الرباعي يتم تقسيمه تلقائياً (الأم، أب الأم، جد الأم)</li>
      <li>رقم الهاتف يستخدم للتحقق من التكرار — إذا كان موجوداً مسبقاً يتم تخطي الصف</li>
      <li>جميع الخريجين المستوردين سيكونون بحالة <strong>معتمد</strong> تلقائياً</li>
      <li>يتم إنشاء بريد إلكتروني تلقائي لكل خريج</li>
      <li>كلمة المرور الافتراضية للجميع: <code class="bg-slate-100 px-1 rounded">password</code></li>
    </ul>
  </div>

  <div class="card p-6">
    <form data-ajax="true" action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div>
        <label class="label">اختر ملف البيانات</label>
        <input class="input" type="file" name="file" accept=".xlsx,.xls,.csv" required>
      </div>
      <button type="submit" class="btn btn-success w-full justify-center mt-4">📥 استيراد</button>
    </form>
  </div>

  @if(session('import_errors') && count(session('import_errors')) > 0)
  <div class="card p-5 mt-6">
    <h4 class="font-bold text-slate-900 mb-3">أخطاء الاستيراد</h4>
    <div class="text-sm text-red-600 space-y-1">
      @foreach(session('import_errors') as $error)
        <div>⚠️ {{ $error }}</div>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
