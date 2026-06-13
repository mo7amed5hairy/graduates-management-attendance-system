@extends('layouts.admin')

@section('title', 'الحضور')
@section('page_title', 'إدارة الحضور')
@section('page_subtitle', 'تسجيل حضور الخريجين في الفعاليات')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  @forelse($events as $e)
  <div class="card p-5 hover:shadow-lg transition">
    <div class="flex items-start justify-between mb-3">
      <div>
        <h3 class="font-extrabold text-slate-900">{{ $e->title }}</h3>
        <p class="text-xs text-slate-500">{{ $e->type }} · {{ $e->event_date->format('Y/m/d h:i A') }}</p>
      </div>
      @if($e->status === 'upcoming')
        <span class="pill pill-blue">قادم</span>
      @else
        <span class="pill pill-green">جاري</span>
      @endif
    </div>
    <p class="text-sm text-slate-600 mb-4">{{ $e->location ?? '—' }}</p>
    <div class="flex gap-2">
      <a href="{{ route('admin.attendance.event', $e) }}" class="btn btn-sm btn-primary">📋 إدارة الحضور</a>
      <a href="{{ route('admin.attendance.scan', $e) }}" class="btn btn-sm btn-success">📱 مسح QR</a>
    </div>
  </div>
  @empty
  <div class="md:col-span-2">
    <div class="card p-8 text-center">
      <p class="text-slate-400">لا توجد فعاليات قادمة أو جارية</p>
      <a href="{{ route('admin.events.create') }}" class="btn btn-primary mt-4">➕ إنشاء فعالية</a>
    </div>
  </div>
  @endforelse
</div>
@endsection
