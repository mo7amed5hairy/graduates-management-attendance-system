@extends('layouts.admin')

@section('title', 'تعديل البروفايل')
@section('page_title', 'تعديل البروفايل')
@section('page_subtitle', 'تحديث بياناتك الشخصية')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="card p-6">
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-sm text-amber-800">
      <strong>⚠️ تنبيه:</strong> بعد إرسال طلب التعديل، ستتم مراجعة بياناتك من قبل الإدارة. تبقى بياناتك الحالية كما هي لحين الموافقة على طلبك.
    </div>

    <form id="profileForm" action="{{ route('profile.change-request.submit') }}" method="POST" enctype="multipart/form-data">
      @csrf

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
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الإسم <span class="text-rose-500">*</span></label>
          <input class="input" name="first_name" value="{{ $user->first_name ?? $user->name }}" required>
        </div>
        <div>
          <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
          <input class="input" name="father_name" value="{{ $user->father_name }}" required>
        </div>
        <div>
          <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
          <input class="input" name="grandfather_name" value="{{ $user->grandfather_name }}" required>
        </div>
        <div>
          <label class="label">اللقب <span class="text-rose-500">*</span></label>
          <input class="input" name="family_name" value="{{ $user->family_name }}" required>
        </div>
        <div>
          <label class="label">اسم الأم</label>
          <input class="input" name="mother_name" value="{{ $user->mother_name }}">
        </div>
        <div>
          <label class="label">أب الأم</label>
          <input class="input" name="mother_father_name" value="{{ $user->mother_father_name }}">
        </div>
        <div>
          <label class="label">جد الأم</label>
          <input class="input" name="mother_grandfather_name" value="{{ $user->mother_grandfather_name }}">
        </div>
        <div>
          <label class="label">رقم البطاقة الوطنية</label>
          <input class="input" name="national_id" value="{{ $user->national_id }}" dir="ltr">
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" value="{{ $user->phone }}" dir="ltr">
        </div>
        <div>
          <label class="label">سنة الميلاد</label>
          <input class="input" type="number" name="date_of_birth" value="{{ $user->date_of_birth }}" min="1900" max="{{ date('Y') }}">
        </div>
        <div>
          <label class="label">الجنس</label>
          <select class="input" name="gender">
            <option value="">اختر</option>
            <option value="ذكر" {{ $user->gender === 'ذكر' ? 'selected' : '' }}>ذكر</option>
            <option value="أنثى" {{ $user->gender === 'أنثى' ? 'selected' : '' }}>أنثى</option>
          </select>
        </div>
        <div>
          <label class="label">الحالة الاجتماعية</label>
          <select class="input" name="social_status" id="social_status" onchange="toggleChildrenCountEdit()">
            <option value="">اختر...</option>
            <option value="أعزب" {{ $user->social_status === 'أعزب' ? 'selected' : '' }}>أعزب</option>
            <option value="متزوج" {{ $user->social_status === 'متزوج' ? 'selected' : '' }}>متزوج</option>
            <option value="مطلق" {{ $user->social_status === 'مطلق' ? 'selected' : '' }}>مطلق</option>
            <option value="أرمل" {{ $user->social_status === 'أرمل' ? 'selected' : '' }}>أرمل</option>
          </select>
        </div>
        <div id="childrenCountWrap" style="{{ in_array($user->social_status, ['متزوج', 'مطلق', 'أرمل']) ? 'display:block' : 'display:none' }}">
          <label class="label">عدد الأولاد</label>
          <input class="input" type="number" name="children_count" id="children_count" value="{{ $user->children_count }}" min="0" max="10">
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
          <label class="label">المحافظة</label>
          <input class="input" name="governorate" value="{{ $user->governorate }}">
        </div>
        <div>
          <label class="label">كلمة المرور (اتركها فارغة إن لم ترد التغيير)</label>
          <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف">
        </div>
        <div>
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور">
        </div>
        <div class="md:col-span-2">
          <label class="label">عنوان السكن الحالى</label>
          <textarea class="input" name="address" rows="2">{{ $user->address }}</textarea>
        </div>
      </div>

      <hr class="my-5 border-slate-100">

      <h3 class="font-extrabold text-slate-800 mb-3">📎 مرفقات التخرج</h3>
      <p class="text-xs text-slate-500 mb-3">يمكنك رفع صور أو مستندات متعلقة بالتخرج (شهادة، وثائق، صور، إلخ)</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">رفع ملفات</label>
          <input class="input" type="file" name="attachments[]" id="attachmentsInput" multiple accept="image/*,.pdf,.doc,.docx" onchange="previewAttachments(this)">
          <div class="flex flex-wrap gap-2 mt-2" id="attachmentsPreview"></div>
        </div>
        <div class="text-xs text-slate-400">
          <p>الصيغ المسموحة: JPG, PNG, PDF, DOC, DOCX</p>
          <p>الحد الأقصى: 10 MB لكل ملف</p>
        </div>
      </div>

      <hr class="my-5 border-slate-100">

      <h3 class="font-extrabold text-slate-800 mb-3">🪪 البطاقة الوطنية</h3>
      <p class="text-xs text-slate-500 mb-3">يمكنك رفع صورة البطاقة الوطنية (الوجه الأمامي والخلفي). غير إلزامي.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الوجه الأمامي للبطاقة</label>
          <input class="input" type="file" name="id_photo_front" accept="image/*">
        </div>
        <div>
          <label class="label">الوجه الخلفي للبطاقة</label>
          <input class="input" type="file" name="id_photo_back" accept="image/*">
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
        <button type="submit" class="btn btn-primary">📩 إرسال طلب التعديل</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function previewAttachments(input) {
  var preview = document.getElementById('attachmentsPreview');
  preview.innerHTML = '';
  if (input.files) {
    for (var i = 0; i < input.files.length; i++) {
      (function(file) {
        var div = document.createElement('div');
        div.className = 'flex items-center gap-2 bg-slate-50 rounded px-3 py-2 text-xs';
        if (file.type.startsWith('image/')) {
          var img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          img.className = 'w-10 h-10 object-cover rounded';
          div.appendChild(img);
        } else {
          var icon = document.createElement('span');
          icon.textContent = '📄';
          icon.className = 'text-lg';
          div.appendChild(icon);
        }
        var name = document.createElement('span');
        name.className = 'truncate max-w-[120px]';
        name.textContent = file.name;
        div.appendChild(name);
        preview.appendChild(div);
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

function toggleChildrenCountEdit() {
  var val = document.getElementById('social_status')?.value;
  var wrap = document.getElementById('childrenCountWrap');
  if (!wrap) return;
  if (val === 'متزوج' || val === 'مطلق' || val === 'أرمل') {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
    document.getElementById('children_count') && (document.getElementById('children_count').value = '');
  }
}
document.addEventListener('DOMContentLoaded', toggleChildrenCountEdit);
</script>
@endpush
