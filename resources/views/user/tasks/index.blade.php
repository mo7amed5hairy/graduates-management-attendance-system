@extends('layouts.admin')

@section('title', 'مهامي')
@section('page_title', '📋 مهامي')
@section('page_subtitle', 'عرض المهام المسندة إليك')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="stat" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
    <div class="l">إجمالي المهام</div>
    <div class="v">{{ $taskStats['total'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="l">المهام المنجزة</div>
    <div class="v">{{ $taskStats['completed'] }}</div>
  </div>
  <div class="stat" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
    <div class="l">المهام المتبقية</div>
    <div class="v">{{ $taskStats['assigned'] + $taskStats['incomplete'] }}</div>
  </div>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="userTasksTable">
      <thead>
        <tr>
          <th>#</th>
          <th>المهمة</th>
          <th>بواسطة</th>
          <th>المرفقات</th>
          <th>الحالة</th>
          <th>التاريخ</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($tasks as $i => $task)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>
            <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 hover:text-sky-600 transition">{{ $task->title }}</a>
            @if($task->description)
            <div class="text-xs text-slate-500 mt-1">{{ Str::limit($task->description, 50) }}</div>
            @endif
          </td>
          <td class="text-slate-600">{{ $task->creator->name }}</td>
          <td>
            @if($task->attachments->count() > 0)
              <a href="{{ asset('storage/' . $task->attachments->first()->file_path) }}" target="_blank" class="pill pill-blue text-xs no-underline">📎 {{ $task->attachments->count() }}</a>
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
            <a href="{{ route('tasks.show', $task) }}" class="text-slate-400 hover:text-sky-600 transition text-lg">👁️</a>
          </td>
        </tr>
        @empty
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#userTasksTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [3, 6] }]
  });
});
</script>
@endpush
