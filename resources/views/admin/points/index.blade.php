@extends('layouts.admin')

@section('title', 'نظام النقاط')
@section('page_title', '⭐ نظام النقاط')
@section('page_subtitle', 'إدارة نقاط المستخدمين')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Add/Deduct Points Form --}}
  <div class="card p-5 lg:col-span-1">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">إضافة / خصم نقاط</h2>
    <form id="pointsForm" action="{{ route('admin.points.store') }}" method="POST">
      @csrf
      <div class="space-y-4">
        <div>
          <label class="label">اختر المستخدم</label>
          <select class="input" name="user_id" required>
            <option value="">-- اختر --</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }} ({{ number_format($u->points) }} نقطة)</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-500">رصيده الحالي:</span>
          <span class="text-xl font-extrabold text-slate-900" id="currentPoints">0</span>
          <span class="text-sm text-slate-500">نقطة</span>
        </div>
        <div>
          <label class="label">عدد النقاط</label>
          <input class="input" type="number" name="points" min="1" required placeholder="مثال: 50">
        </div>
        <div>
          <label class="label">نوع العملية</label>
          <select class="input" name="type" required>
            <option value="add">➕ إضافة نقاط</option>
            <option value="deduct">➖ خصم نقاط</option>
          </select>
        </div>
        <div>
          <label class="label">المهمة (اختياري)</label>
          <select class="input" name="task_id" id="taskSelect">
            <option value="">-- بدون مهمة --</option>
            @foreach($tasks as $t)
            <option value="{{ $t->id }}" data-user="{{ $t->user_id }}">{{ $t->title }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">السبب (اختياري)</label>
          <textarea class="input" name="reason" rows="2" placeholder="سبب العملية..."></textarea>
        </div>
        <button type="submit" class="btn btn-success w-full justify-center">⭐ تنفيذ</button>
      </div>
    </form>
  </div>

  {{-- Transactions History --}}
  <div class="card p-5 lg:col-span-2">
    <h2 class="text-lg font-extrabold text-slate-900 mb-4">📋 سجل المعاملات</h2>

    {{-- Filters --}}
    <form class="flex flex-wrap gap-3 mb-4" method="GET" action="{{ route('admin.points.index') }}">
      <select class="input w-auto" name="user_id">
        <option value="">كل المستخدمين</option>
        @foreach($users as $u)
        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
      </select>
      <select class="input w-auto" name="type" data-filter>
        <option value="">كل الأنواع</option>
        <option value="add" {{ request('type') === 'add' ? 'selected' : '' }}>إضافة</option>
        <option value="deduct" {{ request('type') === 'deduct' ? 'selected' : '' }}>خصم</option>
      </select>
      <input class="input w-auto" type="date" name="date_from" value="{{ request('date_from') }}" placeholder="من تاريخ">
      <input class="input w-auto" type="date" name="date_to" value="{{ request('date_to') }}" placeholder="ل تاريخ">
      <button class="btn btn-ghost">🔍 فلترة</button>
    </form>

    <div class="table-wrap">
      <table class="data" id="pointsTable">
        <thead>
          <tr>
            <th>#</th>
            <th>المستخدم</th>
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
            <td class="font-semibold">{{ $t->user->name }}</td>
            <td class="font-bold">{{ $t->points }}</td>
            <td>
              @if($t->type === 'add')
                <span class="pill pill-green">➕ إضافة</span>
              @else
                <span class="pill pill-rose">➖ خصم</span>
              @endif
            </td>
            <td class="text-slate-500">{{ $t->reason ?? '—' }}</td>
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#pointsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [4, 5, 6] }]
  });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const userSelect = document.querySelector('[name="user_id"]');
  const taskSelect = document.getElementById('taskSelect');
  if (userSelect && taskSelect) {
    function filterTasks() {
      const userId = userSelect.value;
      taskSelect.querySelectorAll('option').forEach(opt => {
        if (!opt.value) return;
        opt.style.display = opt.dataset.user === userId ? '' : 'none';
      });
      taskSelect.value = '';
    }
    userSelect.addEventListener('change', filterTasks);
    filterTasks();
  }
});
</script>
@endpush
