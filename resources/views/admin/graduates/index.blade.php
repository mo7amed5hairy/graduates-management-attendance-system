@extends('layouts.admin')

@section('title', 'الخريجين')
@section('page_title', 'إدارة الخريجين')
@section('page_subtitle', 'مراجعة واعتماد بيانات الخريجين')

@section('content')
<div class="card p-4 mb-4">
  <div class="grid grid-cols-12 gap-3 items-end">
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">الجنس</label>
      <select class="input text-sm" id="filterGender">
        <option value="">الكل</option>
        @foreach($genders as $g)
          <option value="{{ $g }}">{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">المحافظة</label>
      <select class="input text-sm" id="filterGovernorate">
        <option value="">الكل</option>
        @foreach($governorates as $g)
          @php $govName = is_numeric($g) && isset($governorateMap[$g]) ? $governorateMap[$g] : $g; @endphp
          <option value="{{ $g }}">{{ $govName }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">سنة الميلاد</label>
      <select class="input text-sm" id="filterBirthYear">
        <option value="">الكل</option>
        @foreach($birthYears as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">الحالة الاجتماعية</label>
      <select class="input text-sm" id="filterSocialStatus">
        <option value="">الكل</option>
        <option value="أعزب">أعزب</option>
        <option value="متزوج">متزوج</option>
        <option value="مطلق">مطلق</option>
        <option value="أرمل">أرمل</option>
      </select>
    </div>
    <div class="col-span-6 md:col-span-1">
      <label class="label text-xs mb-1">عدد الأولاد</label>
      <select class="input text-sm" id="filterChildren">
        <option value="">الكل</option>
        @for($c = 0; $c <= 10; $c++)
          <option value="{{ $c }}">{{ $c }}</option>
        @endfor
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">سنة التخرج</label>
      <select class="input text-sm" id="filterGradYear">
        <option value="">الكل</option>
        @foreach($years as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-1">
      <label class="label text-xs mb-1">المؤهل</label>
      <select class="input text-sm" id="filterQualification">
        <option value="">الكل</option>
        @foreach($qualifications as $q)
          <option value="{{ $q->id }}">{{ $q->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-12 md:col-span-1 flex gap-2">
      <button class="btn btn-primary text-sm py-2 px-3 w-full" id="resetFilters">🔄</button>
    </div>
  </div>
</div>

<div class="card p-5">
  <div class="table-wrap">
    <table class="data" id="graduatesTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>البريد</th>
          <th>رقم البطاقة الوطنية</th>
          <th>الجنس</th>
          <th>سنة الميلاد</th>
          <th>المحافظة</th>
          <th>الحالة الاجتماعية</th>
          <th>عدد الأولاد</th>
          <th>المؤهل</th>
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
          <td>
            @if($g->gender === 'ذكر')
              <span class="pill pill-blue">ذكر</span>
            @elseif($g->gender === 'أنثى')
              <span class="pill pill-rose">أنثى</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td>{{ $g->date_of_birth ?? '—' }}</td>
          <td>{{ $g->governorate_name }}</td>
          <td>{{ $g->social_status ?? '—' }}</td>
          <td>{{ $g->children_count ?? '—' }}</td>
          <td>{{ $g->qualification?->name ?? '—' }}</td>
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
  var table = $('#graduatesTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'asc']],
    columnDefs: [
      { orderable: false, targets: [15] }
    ]
  });

  function applyFilters() {
    var gender = $('#filterGender').val();
    var gov = $('#filterGovernorate').val();
    var birth = $('#filterBirthYear').val();
    var social = $('#filterSocialStatus').val();
    var children = $('#filterChildren').val();
    var gradYear = $('#filterGradYear').val();
    var qual = $('#filterQualification').val();

    $.fn.dataTable.ext.search = [];

    if (gender) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[4] === gender;
      });
    }
    if (birth) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[5] === birth;
      });
    }
    if (gov) {
      var govText = $('#filterGovernorate option:selected').text();
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[6] === govText;
      });
    }
    if (social) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[7] === social;
      });
    }
    if (children !== '') {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[8] === children;
      });
    }
    if (qual) {
      var qualText = $('#filterQualification option:selected').text();
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[9] === qualText;
      });
    }
    if (gradYear) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[12] === gradYear;
      });
    }

    table.draw();
  }

  $('#filterGender, #filterGovernorate, #filterBirthYear, #filterSocialStatus, #filterChildren, #filterGradYear, #filterQualification').on('change', applyFilters);

  $('#resetFilters').on('click', function() {
    $('#filterGender').val('');
    $('#filterGovernorate').val('');
    $('#filterBirthYear').val('');
    $('#filterSocialStatus').val('');
    $('#filterChildren').val('');
    $('#filterGradYear').val('');
    $('#filterQualification').val('');
    $.fn.dataTable.ext.search = [];
    table.draw();
  });
});
</script>
@endpush
