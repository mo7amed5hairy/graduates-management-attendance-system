@extends('layouts.admin')

@section('title', 'استيراد')
@section('page_title', 'استيراد الخريجين')
@section('page_subtitle', 'استيراد بيانات الخريجين من ملف CSV أو Excel')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="card p-6 mb-6">
    <h3 class="font-bold text-slate-900 mb-2">تعليمات الاستيراد</h3>
    <ul class="text-sm text-slate-600 space-y-1 list-disc pr-5">
      <li>الملف يجب أن يكون بصيغة CSV أو Excel (xlsx, xls)</li>
      <li>العمود <strong>name</strong> و <strong>email</strong> إلزاميان</li>
      <li>باقي الأعمدة اختيارية: phone, national_id, governorate, university, faculty, graduation_year, job_status, address, password</li>
      <li>إذا لم يتم توفير كلمة مرور، سيتم تعيين "password" ككلمة مرور افتراضية</li>
      <li>جميع الخريجين المستوردين سيكونون بحالة "قيد المراجعة" لحين اعتمادهم</li>
    </ul>
  </div>

  <div class="card p-6">
    <form data-ajax="true" action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div>
        <label class="label">اختر ملف البيانات</label>
        <input class="input" type="file" name="file" accept=".csv,.xlsx,.xls,.txt" required>
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
