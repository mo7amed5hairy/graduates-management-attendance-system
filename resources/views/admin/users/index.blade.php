@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page_title', 'إدارة المستخدمين')
@section('page_subtitle', 'عرض وإضافة وتعديل وحذف المستخدمين')

@section('content')

{{-- Actions --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createUserModal">➕ إضافة مستخدم جديد</button>
</div>

{{-- Users Table --}}
<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="usersTable">
      <thead>
        <tr>
          <th>#</th>
          <th>المستخدم</th>
          <th>البريد الإلكتروني</th>
          <th>الهاتف</th>
          <th>النقاط</th>
          <th>الدور</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>{{ $user->id }}</td>
          <td>
            <div class="flex items-center gap-2">
              @if($user->image)
                <img src="{{ $user->image_url }}" class="avatar avatar-sm" style="object-fit:cover">
              @else
                <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
                  {{ substr($user->name, 0, 2) }}
                </div>
              @endif
              <span class="font-semibold">{{ $user->name }}</span>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->phone ?? '—' }}</td>
          <td><span class="pill pill-amber">{{ number_format($user->points) }}</span></td>
          <td>
            @if($user->isAdmin())
              <span class="pill pill-violet">مدير</span>
            @else
              <span class="pill pill-blue">مستخدم</span>
            @endif
          </td>
          <td>
            @if($user->status === 'active')
              <span class="pill pill-green">نشط</span>
            @else
              <span class="pill pill-rose">غير نشط</span>
            @endif
          </td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editUserModal{{ $user->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.users.destroy', $user) }}"
                data-name="{{ $user->name }}">🗑️</button>
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>
</div>

{{-- Create User Modal --}}
<div class="modal-overlay" id="createUserModal">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ إضافة مستخدم جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الاسم</label>
          <input class="input" name="name" required>
        </div>
        <div>
          <label class="label">البريد الإلكتروني</label>
          <input class="input" type="email" name="email" required>
        </div>
        <div>
          <label class="label">كلمة المرور</label>
          <input class="input" type="password" name="password" required>
        </div>
        <div>
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation" required>
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" dir="ltr">
        </div>
        <div>
          <label class="label">الدور</label>
          <select class="input" name="role" required>
            <option value="user">مستخدم</option>
            <option value="admin">مدير</option>
          </select>
        </div>
        <div>
          <label class="label">الحالة</label>
          <label class="relative inline-flex items-center cursor-pointer gap-3 status-wrapper">
            <input type="checkbox" class="sr-only peer status-toggle" checked>
            <input type="hidden" name="status" value="active">
            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
            <span class="text-sm font-semibold text-slate-700 status-label min-w-[60px]">نشط</span>
          </label>
        </div>
        <div>
          <label class="label">الصورة</label>
          <div class="upload-area border-2 border-dashed border-slate-300 rounded-xl p-4 text-center cursor-pointer hover:border-sky-400 hover:bg-sky-50 transition-all" onclick="document.getElementById('imageInput-create').click()">
            <div class="text-3xl mb-1" id="imageIcon-create">📷</div>
            <p class="text-xs text-slate-400" id="imageText-create">اضغط لاختيار صورة</p>
            <input type="file" id="imageInput-create" name="image" accept="image/*" class="hidden" onchange="handleImageSelect(this, 'imagePreview-create', 'imageIcon-create', 'imageText-create')">
          </div>
          <img id="imagePreview-create" class="img-preview mt-2 hidden" style="margin:0.5rem auto 0">
          <div class="progress-bar-wrap" style="display:none">
            <div class="progress-bar-fill" style="width:0%"></div>
            <span class="progress-text">0%</span>
          </div>
        </div>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit User Modals --}}
@foreach($users as $user)
<div class="modal-overlay" id="editUserModal{{ $user->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $user->name }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">الاسم</label>
          <input class="input" name="name" value="{{ $user->name }}" required>
        </div>
        <div>
          <label class="label">البريد الإلكتروني</label>
          <input class="input" type="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div>
          <label class="label">كلمة المرور (اترك فارغاً إن لم ترد التغيير)</label>
          <input class="input" type="password" name="password">
        </div>
        <div>
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation">
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" value="{{ $user->phone }}" dir="ltr">
        </div>
        <div>
          <label class="label">الدور</label>
          <select class="input" name="role" required>
            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>مستخدم</option>
            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مدير</option>
          </select>
        </div>
        <div>
          <label class="label">الحالة</label>
          <label class="relative inline-flex items-center cursor-pointer gap-3 status-wrapper">
            <input type="checkbox" class="sr-only peer status-toggle" {{ $user->status === 'active' ? 'checked' : '' }}>
            <input type="hidden" name="status" value="{{ $user->status }}">
            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
            <span class="text-sm font-semibold text-slate-700 status-label min-w-[60px]">{{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}</span>
          </label>
        </div>
        <div>
          <label class="label">الصورة</label>
          @if($user->image)
            <img src="{{ $user->image_url }}" class="avatar avatar-lg mx-auto mb-2" style="object-fit:cover">
          @endif
          <div class="upload-area border-2 border-dashed border-slate-300 rounded-xl p-4 text-center cursor-pointer hover:border-sky-400 hover:bg-sky-50 transition-all" onclick="document.getElementById('imageInput-{{ $user->id }}').click()">
            <div class="text-3xl mb-1" id="imageIcon-{{ $user->id }}">📷</div>
            <p class="text-xs text-slate-400" id="imageText-{{ $user->id }}">اضغط لاختيار صورة</p>
            <input type="file" id="imageInput-{{ $user->id }}" name="image" accept="image/*" class="hidden" onchange="handleImageSelect(this, 'imagePreview-{{ $user->id }}', 'imageIcon-{{ $user->id }}', 'imageText-{{ $user->id }}')">
          </div>
          <img id="imagePreview-{{ $user->id }}" class="img-preview mt-2 hidden" style="margin:0.5rem auto 0">
          <div class="progress-bar-wrap" style="display:none">
            <div class="progress-bar-fill" style="width:0%"></div>
            <span class="progress-text">0%</span>
          </div>
        </div>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-primary">💾 تحديث</button>
      </div>
    </form>
  </div>
</div>
@endforeach

@push('scripts')
<script>
$(document).ready(function() {
  $('#usersTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [7] }]
  });
});

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('status-toggle')) {
    const wrapper = e.target.closest('.status-wrapper');
    const hidden = wrapper.querySelector('input[type="hidden"]');
    const label = wrapper.querySelector('.status-label');
    hidden.value = e.target.checked ? 'active' : 'inactive';
    label.textContent = e.target.checked ? 'نشط' : 'غير نشط';
  }
});

function handleImageSelect(input, previewId, iconId, textId) {
  const preview = document.getElementById(previewId);
  const icon = document.getElementById(iconId);
  const text = document.getElementById(textId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
    icon.textContent = '🖼️';
    text.textContent = input.files[0].name;
  }
}
</script>
@endpush

@endsection
