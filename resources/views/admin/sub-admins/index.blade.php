@extends('layouts.admin')

@section('title', 'المشرفين والصلاحيات')
@section('page_title', 'المشرفين والصلاحيات')
@section('page_subtitle', 'إدارة المشرفين وتحديد صلاحياتهم')

@section('content')

<div class="mb-4">
  <button class="btn btn-primary" onclick="openCreateModal()">➕ إضافة مشرف جديد</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="subAdminsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد الإلكتروني</th>
          <th>الهاتف</th>
          <th>تاريخ الإضافة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subAdmins as $sa)
        <tr>
          <td>{{ $sa->id }}</td>
          <td class="font-semibold">{{ $sa->name }}</td>
          <td>{{ $sa->email }}</td>
          <td>{{ $sa->phone ?? '—' }}</td>
          <td class="text-xs text-slate-500">{{ $sa->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <button class="btn btn-ghost py-1 px-2 text-xs" onclick="openEditModal({{ $sa->id }})">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.sub-admins.destroy', $sa) }}"
                data-name="{{ $sa->name }}">🗑️</button>
            </div>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Create / Edit Modal --}}
<div class="modal-overlay" id="subAdminModal">
  <div class="modal-content modal-content-xl p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900" id="modalTitle">➕ إضافة مشرف جديد</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" method="POST" id="subAdminForm">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="user_id" id="userId">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <label class="label">الاسم <span class="text-rose-500">*</span></label>
          <input class="input" name="name" id="editName" placeholder="اسم المشرف" required>
        </div>
        <div>
          <label class="label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
          <input class="input" type="email" name="email" id="editEmail" placeholder="example@mail.com" required>
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input class="input" name="phone" id="editPhone" placeholder="077xxxxxxxx" dir="ltr">
        </div>
        <div>
          <label class="label">كلمة المرور <span class="text-rose-500">*</span></label>
          <input class="input" type="password" name="password" id="editPassword" placeholder="أقل شيء 8 أحرف">
        </div>
        <div>
          <label class="label">تأكيد كلمة المرور</label>
          <input class="input" type="password" name="password_confirmation" id="editPasswordConfirmation" placeholder="تأكيد كلمة المرور">
        </div>
      </div>

      <hr class="my-4 border-slate-200">
      <h4 class="font-extrabold text-slate-800 mb-4">🔐 الصلاحيات</h4>

      @php
        $permissionGroups = [
          'الرئيسية' => [
            'dashboard' => 'لوحة التحكم',
          ],
          'المحافظات' => [
            'governorates.view' => 'عرض',
            'governorates.create' => 'إضافة',
            'governorates.edit' => 'تعديل',
            'governorates.delete' => 'حذف',
          ],
          'أنواع المؤسسات' => [
            'institution-types.view' => 'عرض',
            'institution-types.create' => 'إضافة',
            'institution-types.edit' => 'تعديل',
            'institution-types.delete' => 'حذف',
          ],
          'أنواع الجامعات' => [
            'university-types.view' => 'عرض',
            'university-types.create' => 'إضافة',
            'university-types.edit' => 'تعديل',
            'university-types.delete' => 'حذف',
          ],
          'المؤسسات' => [
            'institutions.view' => 'عرض',
            'institutions.create' => 'إضافة',
            'institutions.edit' => 'تعديل',
            'institutions.delete' => 'حذف',
          ],
          'الأقسام' => [
            'departments.view' => 'عرض',
            'departments.create' => 'إضافة',
            'departments.edit' => 'تعديل',
            'departments.delete' => 'حذف',
          ],
          'الإحصائيات' => [
            'statistics.view' => 'عرض',
          ],
          'المؤهلات الدراسية' => [
            'qualifications.view' => 'عرض',
            'qualifications.create' => 'إضافة',
            'qualifications.edit' => 'تعديل',
            'qualifications.delete' => 'حذف',
          ],
          'كليات المؤهلات' => [
            'qualification-faculties.view' => 'عرض',
            'qualification-faculties.create' => 'إضافة',
            'qualification-faculties.edit' => 'تعديل',
            'qualification-faculties.delete' => 'حذف',
          ],
          'المستخدمين' => [
            'users.view' => 'عرض',
            'users.create' => 'إضافة',
            'users.edit' => 'تعديل',
            'users.delete' => 'حذف',
            'users.toggle-status' => 'تعليق/تفعيل',
            'users.bulk-activate' => 'تفعيل جماعي',
          ],
          'النقاط' => [
            'points.view' => 'عرض',
            'points.create' => 'إضافة',
          ],
          'المهام' => [
            'tasks.view' => 'عرض',
            'tasks.create' => 'إضافة',
            'tasks.edit' => 'تعديل',
            'tasks.delete' => 'حذف',
          ],
          'الخريجين' => [
            'graduates.view' => 'عرض',
            'graduates.show' => 'عرض التفاصيل',
            'graduates.approve' => 'اعتماد/رفض',
          ],
          'الفعاليات' => [
            'events.view' => 'عرض',
            'events.create' => 'إضافة',
            'events.edit' => 'تعديل',
            'events.delete' => 'حذف',
          ],
          'الحضور' => [
            'attendance.view' => 'عرض',
            'attendance.scan' => 'مسح QR',
            'attendance.mark' => 'تسجيل حضور',
          ],
          'استيراد بيانات' => [
            'import.view' => 'عرض',
            'import.process' => 'استيراد',
          ],
          'المشرفين والصلاحيات' => [
            'sub-admins.view' => 'عرض',
            'sub-admins.create' => 'إضافة',
            'sub-admins.edit' => 'تعديل',
            'sub-admins.delete' => 'حذف',
          ],
        ];
      @endphp

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="permissionsContainer">
        @foreach($permissionGroups as $groupName => $perms)
        <div class="border border-slate-200 rounded-xl p-4">
          <h5 class="font-bold text-sm text-slate-700 mb-3 pb-2 border-b border-slate-100">{{ $groupName }}</h5>
          <div class="space-y-2">
            @foreach($perms as $key => $label)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
              <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded text-sky-600 permission-checkbox">
              <span>{{ $label }}</span>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      <div class="flex gap-2 mt-6 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success" id="submitBtn">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var apiBase = '{{ url('/') }}';

function openCreateModal() {
  document.getElementById('modalTitle').textContent = '➕ إضافة مشرف جديد';
  document.getElementById('subAdminForm').action = '{{ route('admin.sub-admins.store') }}';
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('userId').value = '';
  document.getElementById('editName').value = '';
  document.getElementById('editEmail').value = '';
  document.getElementById('editPhone').value = '';
  document.getElementById('editPassword').required = true;
  document.getElementById('editPassword').value = '';
  document.getElementById('editPasswordConfirmation').value = '';
  document.getElementById('submitBtn').textContent = '💾 حفظ';
  document.querySelectorAll('.permission-checkbox').forEach(function(cb) { cb.checked = false; });
  document.getElementById('subAdminModal').classList.add('active');
}

function openEditModal(id) {
  document.getElementById('modalTitle').textContent = '✏️ تعديل المشرف';
  document.getElementById('subAdminForm').action = '{{ url('admin/sub-admins') }}/' + id;
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('userId').value = id;
  document.getElementById('editPassword').required = false;
  document.getElementById('submitBtn').textContent = '💾 تحديث';

  fetch(apiBase + '/admin/sub-admins/' + id + '/edit', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success) {
      document.getElementById('editName').value = d.data.name;
      document.getElementById('editEmail').value = d.data.email;
      document.getElementById('editPhone').value = d.data.phone || '';
      document.getElementById('editPassword').value = '';
      document.getElementById('editPasswordConfirmation').value = '';

      var perms = d.data.permissions || [];
      document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
        cb.checked = perms.indexOf(cb.value) !== -1;
      });
    }
  })
  .catch(function() { alert('حدث خطأ في تحميل البيانات'); });

  document.getElementById('subAdminModal').classList.add('active');
}

$(function() {
  $('#subAdminsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [5] }]
  });
});
</script>
@endpush
@endsection
