@extends('layouts.admin')

@section('title', 'البروفايل')
@section('page_title', 'البروفايل الشخصي')
@section('page_subtitle', 'معلومات حسابك')

@section('content')
@if(session('error'))
  <div class="max-w-3xl mx-auto mb-4">
    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">⚠️ {{ session('error') }}</div>
  </div>
@endif
@if(session('success'))
  <div class="max-w-3xl mx-auto mb-4">
    <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
  </div>
@endif

@if($pendingRequest && $pendingRequest->status === 'pending')
<div class="max-w-3xl mx-auto mb-4">
  <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 flex items-center gap-2">
    <span>⏳</span>
    <span>لديك طلب تعديل قيد المراجعة من قبل الإدارة (تم تقديمه في {{ $pendingRequest->created_at->format('Y/m/d H:i') }})</span>
  </div>
</div>
@endif

<div class="w-full max-w-full overflow-x-hidden">
  <div class="card p-4 sm:p-6 mb-6 overflow-x-auto">
    <div class="flex items-start sm:items-center gap-4 flex-wrap">
      @if($user->image)
        <img src="{{ $user->image_url }}" class="avatar avatar-lg shrink-0" style="object-fit:cover">
      @else
        <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white shrink-0">
          {{ substr($user->name, 0, 2) }}
        </div>
      @endif
      <div class="flex-1 min-w-0">
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 break-words">{{ $user->name }}</h2>
        <div class="flex flex-wrap gap-2 mt-2">
          @if($user->isAdmin())
            <span class="pill pill-violet">مدير</span>
          @else
            <span class="pill pill-blue">خريج</span>
          @endif
          <span class="pill pill-amber">{{ number_format($user->points) }} نقطة</span>
          @if($user->isApproved())
            <span class="pill pill-green">معتمد</span>
          @elseif($user->isRejected())
            <span class="pill pill-rose">مرفوض</span>
          @else
            <span class="pill pill-amber">قيد المراجعة</span>
          @endif
          @if($pendingRequest && $pendingRequest->status === 'pending')
            <span class="pill pill-amber">🔔 طلب تعديل معلق</span>
          @endif
        </div>
      </div>
      @if(!$user->isAdmin())
        <button onclick="openEditModal()" class="btn btn-primary">✏️ تعديل البروفايل</button>
      @endif
    </div>

    <hr class="my-5 border-slate-100">

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
        @if(in_array($user->social_status ?? '', ['متزوج', 'مطلق', 'أرمل']) && $user->children_count !== null)
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
        @if($user->graduation_attachments && count($user->graduation_attachments) > 0)
          <div class="mt-2 pt-2 border-t border-slate-100">
            <div class="text-xs text-slate-500 mb-2">📎 مرفقات التخرج ({{ count($user->graduation_attachments) }})</div>
            <div class="flex flex-wrap gap-2">
              @foreach($user->graduation_attachments as $att)
                @php $ext = pathinfo($att['original_name'], PATHINFO_EXTENSION); @endphp
                <a href="{{ route('file.serve', $att['file_path']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg hover:bg-sky-50 hover:border-sky-200 transition text-xs text-slate-600 hover:text-sky-700" title="{{ $att['original_name'] }}">
                  @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                    🖼️
                  @elseif($ext === 'pdf')
                    📄
                  @elseif(in_array($ext, ['doc','docx']))
                    📝
                  @else
                    📎
                  @endif
                  <span class="truncate max-w-[100px]">{{ $att['original_name'] }}</span>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>
      <div>
        <span class="text-xs text-slate-400 block">تاريخ التسجيل</span>
        <span class="font-semibold">{{ $user->created_at->format('Y/m/d') }}</span>
      </div>
    </div>

    @if($user->rejection_reason)
    <hr class="my-5 border-slate-100">
    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
      <span class="text-xs text-slate-400 block">سبب الرفض</span>
      <span class="font-semibold text-rose-700">{{ $user->rejection_reason }}</span>
    </div>
    @endif

    @if(!$user->isAdmin())
    <hr class="my-5 border-slate-100">
    <div class="bg-sky-50 border border-sky-100 rounded-xl p-4">
      <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="min-w-0">
          <span class="text-xs text-slate-400 block">رابط بياناتي الشخصية</span>
          <span class="text-sm font-bold text-slate-800 break-all" dir="ltr" id="detailsLink">{{ url('/checkmydetails/' . $user->access_token) }}</span>
        </div>
        <button class="btn btn-primary text-sm shrink-0" onclick="copyLink()">📋 نسخ الرابط</button>
      </div>
    </div>
    @endif

    @if($user->id_photos && count($user->id_photos) > 0)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">صور الهوية ({{ count($user->id_photos) }})</span>
      <div class="flex flex-wrap gap-3">
        @foreach($user->id_photos as $index => $photo)
          <img src="{{ route('file.serve', $photo) }}" class="w-32 h-32 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition cursor-pointer" onclick="openUserZoom('{{ route('file.serve', $photo) }}')" title="اضغط للتكبير">
        @endforeach
      </div>
    </div>
    @endif

    @if($user->residence_proof && count($user->residence_proof) > 0)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">إثباتات السكن</span>
      <div class="flex flex-wrap gap-3">
        @foreach($user->residence_proof as $proof)
          <a href="{{ route('file.serve', $proof) }}" target="_blank">
            <img src="{{ route('file.serve', $proof) }}" class="w-32 h-32 object-cover rounded-lg border border-slate-200 hover:shadow-lg transition">
          </a>
        @endforeach
      </div>
    </div>
    @endif

    @if($user->social_links)
    <hr class="my-5 border-slate-100">
    <div>
      <span class="text-xs text-slate-400 block mb-2">روابط التواصل</span>
      <div class="flex flex-wrap gap-2">
        @foreach($user->social_links as $platform => $link)
          @if($link)
          <a href="{{ $link }}" target="_blank" rel="noopener" class="btn btn-ghost text-sm">
            {{ $platform == 'facebook' ? 'فيسبوك' : ($platform == 'twitter' ? 'تويتر' : ($platform == 'instagram' ? 'انستغرام' : ($platform == 'linkedin' ? 'لينكد إن' : $platform))) }}
          </a>
          @endif
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <div class="card p-4 sm:p-5 overflow-x-auto">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📋 سجل النقاط</h2>
    <div class="table-wrap">
      <table class="data" id="profileTransactionsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>النقاط</th>
            <th>النوع</th>
            <th>السبب</th>
            <th>المهمة/الفعالية</th>
            <th>بواسطة</th>
            <th>التاريخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $index => $t)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td class="font-bold">{{ $t->points }}</td>
            <td>
              @if($t->type === 'add')
                <span class="pill pill-green">إضافة</span>
              @else
                <span class="pill pill-rose">خصم</span>
              @endif
            </td>
            <td>{{ $t->reason ?? '—' }}</td>
            <td>
              @if($t->task)
                <span class="pill pill-amber text-xs">{{ $t->task->title }}</span>
              @elseif($t->event)
                <span class="pill pill-violet text-xs">{{ $t->event->title }}</span>
              @else
                <span class="text-slate-400">—</span>
              @endif
            </td>
            <td>{{ $t->creator->name }}</td>
            <td class="text-xs text-slate-500">{{ $t->created_at->format('Y/m/d h:i A') }}</td>
          </tr>
          @empty
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Zoom Modal --}}
<div id="userZoomModal" class="fixed inset-0 z-[9999] bg-black/80 hidden items-center justify-center p-4" onclick="closeUserZoom(event)">
  <button class="absolute top-4 left-4 text-white text-3xl hover:text-slate-300 z-10" onclick="closeUserZoom()">&times;</button>
  <img id="userZoomImg" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl" onclick="event.stopPropagation()">
</div>

{{-- Edit Modal --}}
<div id="editProfileModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-start justify-center overflow-y-auto" style="padding: 2rem 1rem;">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-8 relative" style="max-height:90vh;overflow-y:auto">
    <div class="sticky top-0 bg-white z-10 flex items-center justify-between p-5 border-b border-slate-100 rounded-t-2xl">
      <h3 class="text-lg font-extrabold text-slate-800">✏️ تعديل البروفايل</h3>
      <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
    </div>

    <div class="p-5">
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-sm text-amber-800">
        <strong>⚠️ تنبيه:</strong> بعد إرسال طلب التعديل، ستتم مراجعة بياناتك من قبل الإدارة. تبقى بياناتك الحالية كما هي لحين الموافقة على طلبك.
      </div>

      <form id="editProfileForm" action="{{ route('profile.change-request.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col items-center mb-6">
          @if($user->image)
            <img id="editPreview" class="img-preview" src="{{ $user->image_url }}" style="width:96px;height:96px;border-radius:50%;object-fit:cover">
          @else
            <div class="avatar avatar-lg bg-gradient-to-br from-sky-500 to-indigo-600 text-white mb-3" id="editAvatar">
              {{ substr($user->name, 0, 2) }}
            </div>
            <img id="editPreview" class="img-preview hidden">
          @endif
          <div class="upload-area mt-2">
            <label class="btn btn-ghost text-sm cursor-pointer">
              📷 تغيير الصورة
              <input type="file" name="image" id="editImageInput" accept="image/*" class="hidden">
            </label>
          </div>
        </div>

        {{-- Row 1: Names --}}
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-12 md:col-span-3">
            <label class="label">الإسم <span class="text-rose-500">*</span></label>
            <input class="input" name="first_name" value="{{ $user->first_name ?? $user->name }}" required>
          </div>
          <div class="col-span-12 md:col-span-3">
            <label class="label">اسم الأب <span class="text-rose-500">*</span></label>
            <input class="input" name="father_name" value="{{ $user->father_name }}" required>
          </div>
          <div class="col-span-12 md:col-span-3">
            <label class="label">اسم الجد <span class="text-rose-500">*</span></label>
            <input class="input" name="grandfather_name" value="{{ $user->grandfather_name }}" required>
          </div>
          <div class="col-span-12 md:col-span-3">
            <label class="label">اللقب <span class="text-rose-500">*</span></label>
            <input class="input" name="family_name" value="{{ $user->family_name }}" required>
          </div>
        </div>

        {{-- Row 2: Mother names --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-4">
            <label class="label">اسم الأم</label>
            <input class="input" name="mother_name" value="{{ $user->mother_name }}">
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">أب الأم</label>
            <input class="input" name="mother_father_name" value="{{ $user->mother_father_name }}">
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">جد الأم</label>
            <input class="input" name="mother_grandfather_name" value="{{ $user->mother_grandfather_name }}">
          </div>
        </div>

        {{-- Row 3: National ID, Email, Phone --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-4">
            <label class="label">رقم البطاقة الوطنية</label>
            <input class="input" name="national_id" value="{{ $user->national_id }}" dir="ltr">
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">البريد الإلكتروني <small>(اختياري)</small></label>
            <input class="input" type="email" name="email" value="{{ $user->email }}" dir="ltr">
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">رقم الهاتف</label>
            <input class="input" name="phone" value="{{ $user->phone }}" dir="ltr" maxlength="11">
          </div>
        </div>

        {{-- Row 4: Birth year, Gender, Job status --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-3">
            <label class="label">سنة الميلاد</label>
            <select class="input text-base" name="date_of_birth" id="editDateOfBirth" onchange="calcEditAge()">
              <option value="">اختر سنة الميلاد...</option>
              @foreach(range(date('Y'), 1900) as $year)
                <option value="{{ $year }}" {{ ($user->date_of_birth == $year) ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-span-12 md:col-span-2">
            <label class="label">العمر</label>
            <input class="input" type="number" id="editAge" value="{{ $user->age }}" readonly style="background:#f1f5f9">
          </div>
          <div class="col-span-12 md:col-span-3">
            <label class="label">الجنس</label>
            <select class="input text-base" name="gender">
              <option value="">اختر</option>
              <option value="ذكر" {{ $user->gender === 'ذكر' ? 'selected' : '' }}>ذكر</option>
              <option value="أنثى" {{ $user->gender === 'أنثى' ? 'selected' : '' }}>أنثى</option>
            </select>
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">الحالة الوظيفية</label>
            <select class="input text-base" name="job_status">
              <option value="">اختر...</option>
              <option value="موظف" {{ $user->job_status === 'موظف' ? 'selected' : '' }}>موظف</option>
              <option value="غير موظف" {{ $user->job_status === 'غير موظف' ? 'selected' : '' }}>غير موظف</option>
              <option value="طالب" {{ $user->job_status === 'طالب' ? 'selected' : '' }}>طالب</option>
              <option value="صاحب عمل" {{ $user->job_status === 'صاحب عمل' ? 'selected' : '' }}>صاحب عمل</option>
              <option value="متقاعد" {{ $user->job_status === 'متقاعد' ? 'selected' : '' }}>متقاعد</option>
              <option value="أخرى" {{ $user->job_status === 'أخرى' ? 'selected' : '' }}>أخرى</option>
            </select>
          </div>
        </div>

        {{-- Row 5: Social status + Children count --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-6">
            <label class="label">الحالة الاجتماعية</label>
            <select class="input text-base" name="social_status" id="editSocialStatus" onchange="toggleEditChildren()">
              <option value="">اختر...</option>
              <option value="أعزب" {{ $user->social_status === 'أعزب' ? 'selected' : '' }}>أعزب / عزباء</option>
              <option value="متزوج" {{ $user->social_status === 'متزوج' ? 'selected' : '' }}>متزوج / متزوجة</option>
              <option value="مطلق" {{ $user->social_status === 'مطلق' ? 'selected' : '' }}>مطلق / مطلقة</option>
              <option value="أرمل" {{ $user->social_status === 'أرمل' ? 'selected' : '' }}>أرمل / أرملة</option>
            </select>
          </div>
          <div class="col-span-12 md:col-span-6" id="editChildrenWrap" style="{{ in_array($user->social_status, ['متزوج', 'مطلق', 'أرمل']) ? '' : 'display:none' }}">
            <label class="label">عدد الأولاد</label>
            <input class="input" type="number" name="children_count" id="editChildrenCount" value="{{ $user->children_count ?? 0 }}" min="0" max="10">
          </div>
        </div>

        {{-- Row 6: Governorate + Address --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-3">
            <label class="label">المحافظة</label>
            <select class="input text-base" name="governorate" id="editGovernorate">
              <option value="">اختر...</option>
              @foreach($governorates as $gov)
                <option value="{{ $gov }}" {{ $user->governorate === $gov ? 'selected' : '' }}>{{ $gov }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-span-12 md:col-span-9">
            <label class="label">عنوان السكن الحالى</label>
            <textarea class="input" name="address" rows="2">{{ $user->address }}</textarea>
          </div>
        </div>

        {{-- Row 7: Qualification, Faculty, Grad year --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-4">
            <label class="label">التحصيل الدراسى</label>
            <select class="input text-base" name="qualification_id" id="editQualificationId" onchange="loadEditFaculties()">
              <option value="">اختر المؤهل...</option>
              @foreach($qualifications as $q)
                <option value="{{ $q->id }}" {{ ($user->qualification_id == $q->id) ? 'selected' : '' }}>{{ $q->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">الكلية / المعهد</label>
            <select class="input text-base" name="qualification_faculty_id" id="editFacultyId">
              <option value="">اختر المؤهل أولاً...</option>
            </select>
          </div>
          <div class="col-span-12 md:col-span-4">
            <label class="label">سنة التخرج</label>
            <select class="input text-base" name="graduation_year">
              <option value="">اختر سنة التخرج...</option>
              @foreach(range(date('Y') + 5, 1950) as $year)
                <option value="{{ $year }}" {{ ($user->graduation_year == $year) ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Row 8: Password --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12 md:col-span-6">
            <label class="label">كلمة المرور (اتركها فارغة إن لم ترد التغيير)</label>
            <input class="input" type="password" name="password" placeholder="أقل شيء 8 أحرف">
          </div>
          <div class="col-span-12 md:col-span-6">
            <label class="label">تأكيد كلمة المرور</label>
            <input class="input" type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور">
          </div>
        </div>

        {{-- Social links --}}
        <div class="grid grid-cols-12 gap-4 mt-4">
          <div class="col-span-12">
            <label class="label font-extrabold">🌐 روابط التواصل الاجتماعي (اختياري)</label>
          </div>
          <div class="col-span-12 md:col-span-6">
            <input class="input" name="social_links[facebook]" dir="ltr" value="{{ $user->social_links['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
          </div>
          <div class="col-span-12 md:col-span-6">
            <input class="input" name="social_links[twitter]" dir="ltr" value="{{ $user->social_links['twitter'] ?? '' }}" placeholder="https://twitter.com/...">
          </div>
          <div class="col-span-12 md:col-span-6">
            <input class="input" name="social_links[instagram]" dir="ltr" value="{{ $user->social_links['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
          </div>
          <div class="col-span-12 md:col-span-6">
            <input class="input" name="social_links[linkedin]" dir="ltr" value="{{ $user->social_links['linkedin'] ?? '' }}" placeholder="https://linkedin.com/...">
          </div>
        </div>

        {{-- National ID photos --}}
        <hr class="my-5 border-slate-100">
        <h3 class="font-extrabold text-slate-800 mb-3">🪪 البطاقة الوطنية</h3>
        <p class="text-xs text-slate-500 mb-3">يمكنك رفع صورة البطاقة الوطنية (الوجه الأمامي والخلفي). غير إلزامي.</p>
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-12 md:col-span-6">
            <label class="label">الوجه الأمامي للبطاقة</label>
            <input class="input" type="file" name="id_photo_front" accept="image/*">
          </div>
          <div class="col-span-12 md:col-span-6">
            <label class="label">الوجه الخلفي للبطاقة</label>
            <input class="input" type="file" name="id_photo_back" accept="image/*">
          </div>
        </div>

        {{-- Attachments --}}
        <hr class="my-5 border-slate-100">
        <h3 class="font-extrabold text-slate-800 mb-3">📎 مرفقات التخرج</h3>
        <p class="text-xs text-slate-500 mb-3">يمكنك رفع صور أو مستندات متعلقة بالتخرج (شهادة، وثائق، صور، إلخ). غير إلزامي.</p>
        <div class="grid grid-cols-12 gap-4">
          <div class="col-span-12 md:col-span-8">
            <input class="input" type="file" name="attachments[]" id="editAttachmentsInput" multiple accept="image/*,.pdf,.doc,.docx" onchange="previewEditAttachments(this)">
            <div class="flex flex-wrap gap-2 mt-2" id="editAttachmentsPreview"></div>
          </div>
          <div class="col-span-12 md:col-span-4 text-xs text-slate-400">
            <p>الصيغ المسموحة: JPG, PNG, PDF, DOC, DOCX</p>
            <p>الحد الأقصى: 10 MB لكل ملف</p>
            <p>غير مطلوب — يمكنك التعديل بدون مرفقات</p>
          </div>
        </div>

        <div class="flex gap-2 mt-6 justify-end border-t border-slate-100 pt-5">
          <button type="button" onclick="closeEditModal()" class="btn btn-ghost">إلغاء</button>
          <button type="submit" class="btn btn-primary">📩 إرسال طلب التعديل</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var editModal = document.getElementById('editProfileModal');
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function openEditModal() {
  editModal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  loadEditFaculties();
}

function closeEditModal() {
  editModal.classList.add('hidden');
  document.body.style.overflow = '';
}

editModal?.addEventListener('click', function(e) {
  if (e.target === editModal) closeEditModal();
});

// Image preview
document.getElementById('editImageInput')?.addEventListener('change', function() {
  if (this.files?.[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var preview = document.getElementById('editPreview');
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      preview.style.cssText = 'width:96px;height:96px;border-radius:50%;object-fit:cover';
      var avatar = document.getElementById('editAvatar');
      if (avatar) avatar.classList.add('hidden');
    };
    reader.readAsDataURL(this.files[0]);
  }
});

// Age calculation
function calcEditAge() {
  var year = document.getElementById('editDateOfBirth').value;
  var ageField = document.getElementById('editAge');
  ageField.value = year ? (new Date().getFullYear() - parseInt(year)) : '';
}

// Children toggle
function toggleEditChildren() {
  var val = document.getElementById('editSocialStatus').value;
  var wrap = document.getElementById('editChildrenWrap');
  if (val === 'متزوج' || val === 'مطلق' || val === 'أرمل') {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
    document.getElementById('editChildrenCount').value = '0';
  }
}

// Faculty loader
function loadEditFaculties() {
  var qualId = document.getElementById('editQualificationId').value;
  var facSel = document.getElementById('editFacultyId');
  if (!qualId) {
    facSel.innerHTML = '<option value="">اختر المؤهل أولاً...</option>';
    facSel.disabled = true;
    return;
  }
  facSel.disabled = true;
  facSel.innerHTML = '<option value="">جاري التحميل...</option>';
  fetch('{{ url('/api/qualifications') }}/' + qualId + '/faculties', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      facSel.innerHTML = '<option value="">اختر الكلية/المعهد...</option>';
      if (d.success && d.data) {
        d.data.forEach(function(item) {
          var opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.name;
          if ({{ $user->qualification_faculty_id ?? 'null' }} === item.id) opt.selected = true;
          facSel.appendChild(opt);
        });
      }
      facSel.disabled = false;
    })
    .catch(function() {
      facSel.innerHTML = '<option value="">خطأ في التحميل</option>';
      facSel.disabled = false;
    });
}

// Attachments preview
function previewEditAttachments(input) {
  var preview = document.getElementById('editAttachmentsPreview');
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

// AJAX form submission
document.getElementById('editProfileForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  var form = this;
  var btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = '⏳ جاري الإرسال...';

  var formData = new FormData(form);

  fetch(form.action, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      App.toast ? App.toast(d.message, 'success') : alert(d.message);
      closeEditModal();
      if (d.redirect) setTimeout(function() { window.location.href = d.redirect; }, 1500);
      else setTimeout(function() { location.reload(); }, 1500);
    } else {
      App.toast ? App.toast(d.message || 'حدث خطأ', 'error') : alert(d.message || 'حدث خطأ');
      btn.disabled = false;
      btn.textContent = '📩 إرسال طلب التعديل';
    }
  })
  .catch(function() {
    App.toast ? App.toast('حدث خطأ في الاتصال', 'error') : alert('حدث خطأ في الاتصال');
    btn.disabled = false;
    btn.textContent = '📩 إرسال طلب التعديل';
  });
});

$(function() {
  $('#profileTransactionsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    columnDefs: [{ targets: [3, 4, 5], orderable: false }],
    order: [[6, 'desc']]
  });
});

// Zoom
function openUserZoom(src) {
  document.getElementById('userZoomImg').src = src;
  var m = document.getElementById('userZoomModal');
  m.classList.remove('hidden');
  m.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeUserZoom(e) {
  var m = document.getElementById('userZoomModal');
  if (e && e.target !== document.getElementById('userZoomImg')) {
    m.classList.add('hidden');
    m.style.display = 'none';
    document.body.style.overflow = '';
  } else if (!e) {
    m.classList.add('hidden');
    m.style.display = 'none';
    document.body.style.overflow = '';
  }
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { closeUserZoom(); }
});

function copyLink() {
  var link = document.getElementById('detailsLink');
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(link.textContent).then(function() {
      App.toast('تم نسخ الرابط', 'success');
    });
  } else {
    var range = document.createRange();
    range.selectNode(link);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    App.toast('تم نسخ الرابط', 'success');
  }
}
</script>
@endpush
