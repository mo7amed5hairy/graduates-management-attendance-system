@extends('layouts.admin')

@section('title', 'الخريجين')
@section('page_title', 'إدارة الخريجين')
@section('page_subtitle', 'مراجعة واعتماد بيانات الخريجين')

@section('content')
<div class="card p-5 mb-6">
  <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="label">حالة الاعتماد</label>
      <select class="input" name="approval_status" onchange="this.form.submit()">
        <option value="">الكل</option>
        <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
        <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>مقبول</option>
        <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
      </select>
    </div>
    <div>
      <label class="label">المحافظة</label>
      <select class="input" name="governorate" onchange="this.form.submit()">
        <option value="">الكل</option>
        @foreach($governorates as $g)
          @php $govName = is_numeric($g) && isset($governorateMap[$g]) ? $governorateMap[$g] : $g; @endphp
          <option value="{{ $g }}" {{ request('governorate') == $g ? 'selected' : '' }}>{{ $govName }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">الجامعة</label>
      <input class="input" name="university" placeholder="بحث..." value="{{ request('university') }}">
    </div>
    <div>
      <label class="label">سنة التخرج</label>
      <select class="input" name="graduation_year" onchange="this.form.submit()">
        <option value="">الكل</option>
        @foreach($years as $y)
          <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="md:col-span-4 flex gap-2">
      <input class="input flex-1" name="search" placeholder="بحث بالاسم أو البريد أو الرقم القومي..." value="{{ request('search') }}">
      <button type="submit" class="btn btn-primary">🔍 بحث</button>
      <a href="{{ route('admin.graduates.index') }}" class="btn btn-ghost">إلغاء</a>
    </div>
  </form>
</div>

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="graduatesTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>الرقم القومي</th>
          <th>المحافظة</th>
          <th>الجامعة</th>
          <th>الكلية</th>
          <th>سنة التخرج</th>
          <th>الحالة</th>
          <th>تاريخ التسجيل</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($graduates as $i => $g)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td class="font-bold">{{ $g->name }}</td>
          <td class="text-xs">{{ $g->email }}</td>
          <td class="text-xs">{{ $g->national_id ?? '—' }}</td>
          <td>{{ $g->governorate_name }}</td>
          <td>{{ $g->university ?? '—' }}</td>
          <td>{{ $g->faculty ?? '—' }}</td>
          <td>{{ $g->graduation_year ?? '—' }}</td>
          <td>
            @if($g->approval_status === 'approved')
              <span class="pill pill-green">مقبول</span>
            @elseif($g->approval_status === 'rejected')
              <span class="pill pill-rose">مرفوض</span>
            @else
              <span class="pill pill-amber">قيد المراجعة</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $g->created_at->format('Y/m/d') }}</td>
          <td>
            <a href="{{ route('admin.graduates.show', $g) }}" class="btn btn-sm btn-primary">🔍 عرض</a>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
  $('#graduatesTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [{ orderable: false, targets: [10] }]
  });
});
</script>
@endpush
