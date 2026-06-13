@extends('layouts.admin')

@section('title', $task->title)
@section('page_title', '📋 ' . $task->title)
@section('page_subtitle', 'تفاصيل المهمة')

@section('content')

<div class="max-w-3xl mx-auto">
  <div class="card p-6 mb-6">
    <div class="flex items-start justify-between mb-5">
      <div>
        <h2 class="text-2xl font-extrabold text-slate-900">{{ $task->title }}</h2>
        <div class="flex items-center gap-2 mt-2">
          @if($task->status === 'assigned')
            <span class="pill pill-amber">مسندة</span>
          @elseif($task->status === 'completed')
            <span class="pill pill-green">مكتملة</span>
          @else
            <span class="pill pill-rose">غير مكتملة</span>
          @endif
          <span class="text-xs text-slate-400">{{ $task->created_at->format('Y/m/d h:i A') }}</span>
        </div>
      </div>
      <a href="{{ route('tasks.index') }}" class="btn btn-ghost text-sm">🔙 رجوع</a>
    </div>

    <hr class="border-slate-100 mb-5">

    <div class="space-y-5">
      {{-- Assigned user --}}
      <div>
        <span class="text-xs text-slate-400 block mb-1">مسندة إلى</span>
        <div class="flex items-center gap-2">
          @if($task->user->image)
            <img src="{{ $task->user->image_url }}" class="avatar avatar-sm" style="object-fit:cover">
          @else
            <div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">
              {{ substr($task->user->name, 0, 2) }}
            </div>
          @endif
          <span class="font-semibold">{{ $task->user->name }}</span>
        </div>
      </div>

      {{-- Created by --}}
      <div>
        <span class="text-xs text-slate-400 block mb-1">بواسطة</span>
        <span class="font-semibold">{{ $task->creator->name }}</span>
      </div>

      {{-- Description --}}
      @if($task->description)
      <div>
        <span class="text-xs text-slate-400 block mb-1">الوصف</span>
        <div class="bg-slate-50 rounded-xl p-4 text-slate-700 leading-7 whitespace-pre-wrap">{{ $task->description }}</div>
      </div>
      @endif

      {{-- Attachments --}}
      @if($task->attachments->count() > 0)
      <div>
        <span class="text-xs text-slate-400 block mb-2">المرفقات ({{ $task->attachments->count() }})</span>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          @foreach($task->attachments as $att)
            @php
              $ext = strtolower(pathinfo($att->original_name, PATHINFO_EXTENSION));
              $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            @endphp
            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition">
              @if($isImage)
                <img src="{{ asset('storage/' . $att->file_path) }}" class="w-full h-32 object-cover">
              @else
                <div class="h-32 flex items-center justify-center bg-slate-50 text-slate-400">
                  <div class="text-center">
                    <div class="text-3xl">📎</div>
                    <div class="text-xs mt-1 px-2 truncate max-w-[120px]">{{ $att->original_name }}</div>
                  </div>
                </div>
              @endif
            </a>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection
