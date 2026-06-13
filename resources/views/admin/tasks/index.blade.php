@extends('layouts.admin')

@section('title', 'المهام')
@section('page_title', '📋 المهام')
@section('page_subtitle', 'إدارة المهام المسندة للمستخدمين')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-5">
  <div></div>
  <button class="btn btn-primary" data-modal="createTaskModal">➕ مهمة جديدة</button>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="adminTasksTable">
      <thead>
        <tr>
          <th>#</th>
          <th>المهمة</th>
          <th>مسندة إلى</th>
          <th>بواسطة</th>
          <th>المرفقات</th>
          <th>الحالة</th>
          <th>التاريخ</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tasks as $task)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td class="max-w-[200px] truncate" title="{{ $task->description ?? $task->title }}">
            <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-sky-600 transition">{{ $task->title }}</a>
            @if($task->description)
            <span class="text-xs text-slate-400 block truncate">{{ Str::limit($task->description, 50) }}</span>
            @endif
          </td>
          <td>
            <div class="flex items-center gap-2">
              @if($task->user->image)
                <img src="{{ $task->user->image_url }}" class="avatar avatar-sm" style="object-fit:cover">
              @else
                <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
                  {{ substr($task->user->name, 0, 2) }}
                </div>
              @endif
              <span>{{ $task->user->name }}</span>
            </div>
          </td>
          <td class="text-slate-600">{{ $task->creator->name }}</td>
          <td>
            @if($task->attachments->count() > 0)
              <span class="pill pill-blue">{{ $task->attachments->count() }}</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td>
            @if($task->status === 'assigned')
              <span class="pill pill-amber">مسندة</span>
            @elseif($task->status === 'completed')
              <span class="pill pill-green">مكتملة</span>
            @else
              <span class="pill pill-rose">غير مكتملة</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $task->created_at->format('Y/m/d') }}</td>
          <td>
            <div class="flex gap-1">
              <a href="{{ route('tasks.show', $task) }}" class="btn btn-ghost py-1 px-2 text-xs">👁️</a>
              <button class="btn btn-ghost py-1 px-2 text-xs" data-modal="editTaskModal{{ $task->id }}">✏️</button>
              <button class="btn btn-danger py-1 px-2 text-xs"
                data-delete="{{ route('admin.tasks.destroy', $task) }}"
                data-name="{{ $task->title }}">🗑️</button>
            </div>
          </td>
        </tr>
        @empty
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Create Task Modal --}}
<div class="modal-overlay" id="createTaskModal">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">➕ مهمة جديدة</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="space-y-4">
        <div>
          <label class="label">المستخدم</label>
          <select class="input" name="user_id" required>
            <option value="">-- اختر مستخدم --</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">عنوان المهمة</label>
          <input class="input" name="title" required placeholder="أدخل عنوان المهمة">
        </div>
        <div>
          <label class="label">الوصف</label>
          <textarea class="input" name="description" rows="3" placeholder="وصف المهمة والمطلوب من المستخدم..."></textarea>
        </div>
        <div>
          <label class="label">المرفقات</label>
          <input class="input" type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.zip">
          <p class="text-xs text-slate-400 mt-1">صور, PDF, مستندات (اختياري)</p>
        </div>
      </div>
      <div class="flex gap-2 mt-5 justify-end">
        <button type="button" class="btn btn-ghost" data-modal-close>إلغاء</button>
        <button type="submit" class="btn btn-success">💾 حفظ</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Task Modals --}}
@foreach($tasks as $task)
<div class="modal-overlay" id="editTaskModal{{ $task->id }}">
  <div class="modal-content p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-lg font-extrabold text-slate-900">✏️ تعديل: {{ $task->title }}</h3>
      <button class="text-slate-400 hover:text-slate-600 text-xl" data-modal-close>&times;</button>
    </div>
    <form data-ajax="true" action="{{ route('admin.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="space-y-4">
        <div>
          <label class="label">المستخدم</label>
          <select class="input" name="user_id" required>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ $task->user_id === $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">عنوان المهمة</label>
          <input class="input" name="title" value="{{ $task->title }}" required>
        </div>
        <div>
          <label class="label">الوصف</label>
          <textarea class="input" name="description" rows="3">{{ $task->description }}</textarea>
        </div>
        <div>
          <label class="label">الحالة</label>
          <select class="input" name="status" required>
            <option value="assigned" {{ $task->status === 'assigned' ? 'selected' : '' }}>مسندة</option>
            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>مكتملة</option>
            <option value="incomplete" {{ $task->status === 'incomplete' ? 'selected' : '' }}>غير مكتملة</option>
          </select>
        </div>
        @if($task->attachments->count() > 0)
        <div>
          <label class="label">المرفقات الحالية</label>
          <div class="flex flex-wrap gap-2">
            @foreach($task->attachments as $att)
            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="pill pill-blue text-sm no-underline">
              📎 {{ $att->original_name }}
            </a>
            @endforeach
          </div>
        </div>
        @endif
        <div>
          <label class="label">إضافة مرفقات جديدة</label>
          <input class="input" type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.zip">
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#adminTasksTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [4, 7] }]
  });
});
</script>
@endpush
