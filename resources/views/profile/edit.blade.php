@extends('layouts.admin')

@section('title', 'تعديل البروفايل')
@section('page_title', 'تعديل البروفايل')
@section('page_subtitle', 'تحديث بياناتك الشخصية')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="card p-6">
    <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="flex flex-col items-center mb-6">
        @if($user->image)
          <img id="profilePreview" class="img-preview" src="{{ $user->image_url }}" style="width:96px;height:96px;border-radius:50%;object-fit:cover">
        @else
          <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white mb-3" id="profileAvatar">
            {{ substr($user->name, 0, 2) }}
          </div>
          <img id="profilePreview" class="img-preview hidden">
        @endif

        <div class="upload-area mt-2">
          <label class="btn btn-ghost text-sm cursor-pointer">
            📷 تغيير الصورة
            <input type="file" name="image" id="imageInput" accept="image/*" class="hidden">
          </label>
          <div class="progress-bar-wrap" id="uploadProgress" style="display:none">
            <div class="progress-bar-fill" id="progressFill"></div>
            <span class="progress-text" id="progressText">0%</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الاسم الرباعي</label>
          <input class="input" name="name" value="{{ $user->name }}" required>
        </div>
        <div>
          <label class="label">الرقم القومي</label>
          <input class="input" name="national_id" value="{{ $user->national_id }}" dir="ltr">
        </div>
        <div>
          <label class="label">البريد الإلكتروني</label>
          <input class="input" type="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" value="{{ $user->phone }}" dir="ltr">
        </div>
        <div>
          <label class="label">المحافظة</label>
          <input class="input" name="governorate" value="{{ $user->governorate }}">
        </div>
        <div>
          <label class="label">الجامعة</label>
          <input class="input" name="university" value="{{ $user->university }}">
        </div>
        <div>
          <label class="label">الكلية</label>
          <input class="input" name="faculty" value="{{ $user->faculty }}">
        </div>
        <div>
          <label class="label">سنة التخرج</label>
          <input class="input" type="number" name="graduation_year" value="{{ $user->graduation_year }}" min="1950" max="{{ date('Y') + 5 }}">
        </div>
        <div>
          <label class="label">الحالة الوظيفية</label>
          <select class="input" name="job_status">
            <option value="">اختر...</option>
            <option value="موظف" {{ $user->job_status === 'موظف' ? 'selected' : '' }}>موظف</option>
            <option value="غير موظف" {{ $user->job_status === 'غير موظف' ? 'selected' : '' }}>غير موظف</option>
            <option value="طالب" {{ $user->job_status === 'طالب' ? 'selected' : '' }}>طالب</option>
            <option value="صاحب عمل" {{ $user->job_status === 'صاحب عمل' ? 'selected' : '' }}>صاحب عمل</option>
            <option value="متقاعد" {{ $user->job_status === 'متقاعد' ? 'selected' : '' }}>متقاعد</option>
            <option value="أخرى" {{ $user->job_status === 'أخرى' ? 'selected' : '' }}>أخرى</option>
          </select>
        </div>
        <div>
          <label class="label">كلمة المرور (اتركها فارغة إن لم ترد التغيير)</label>
          <input class="input" type="password" name="password">
        </div>
        <div>
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation">
        </div>
        <div class="md:col-span-2">
          <label class="label">العنوان</label>
          <textarea class="input" name="address" rows="2">{{ $user->address }}</textarea>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="label">صور الهوية (إضافة صور جديدة)</label>
          <input class="input" type="file" name="id_photos[]" multiple accept="image/*" onchange="previewImages(this, 'idPreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="idPreview"></div>
          @if($user->id_photos && count($user->id_photos) > 0)
            <div class="text-xs text-slate-400 mt-1">لديك {{ count($user->id_photos) }} صورة هوية مرفوعة</div>
          @endif
        </div>
        <div>
          <label class="label">إثبات السكن (إضافة صور جديدة)</label>
          <input class="input" type="file" name="residence_proof[]" multiple accept="image/*" onchange="previewImages(this, 'residencePreview')">
          <div class="flex flex-wrap gap-2 mt-2" id="residencePreview"></div>
          @if($user->residence_proof && count($user->residence_proof) > 0)
            <div class="text-xs text-slate-400 mt-1">لديك {{ count($user->residence_proof) }} إثبات سكن مرفوع</div>
          @endif
        </div>
      </div>

      <hr class="my-5 border-slate-100">

      <h3 class="font-extrabold text-slate-800 mb-3">🌐 روابط التواصل الاجتماعي (اختياري)</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">فيسبوك</label>
          <input class="input" name="social_links[facebook]" dir="ltr" value="{{ $user->social_links['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
        </div>
        <div>
          <label class="label">تويتر</label>
          <input class="input" name="social_links[twitter]" dir="ltr" value="{{ $user->social_links['twitter'] ?? '' }}" placeholder="https://twitter.com/...">
        </div>
        <div>
          <label class="label">انستغرام</label>
          <input class="input" name="social_links[instagram]" dir="ltr" value="{{ $user->social_links['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
        </div>
        <div>
          <label class="label">لينكد إن</label>
          <input class="input" name="social_links[linkedin]" dir="ltr" value="{{ $user->social_links['linkedin'] ?? '' }}" placeholder="https://linkedin.com/...">
        </div>
      </div>

      <div class="flex gap-2 mt-5 justify-end">
        <a href="{{ route('profile.show') }}" class="btn btn-ghost">إلغاء</a>
        <button type="submit" class="btn btn-primary">💾 حفظ التغييرات</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
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

document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('imageInput');
  const preview = document.getElementById('profilePreview');
  const avatar = document.getElementById('profileAvatar');

  fileInput?.addEventListener('change', function() {
    if (this.files?.[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        preview.style.cssText = 'width:96px;height:96px;border-radius:50%;object-fit:cover';
        if (avatar) avatar.classList.add('hidden');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
});
</script>
@endpush
