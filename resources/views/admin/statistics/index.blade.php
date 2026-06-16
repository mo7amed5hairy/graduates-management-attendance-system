@extends('layouts.admin')

@section('title', 'الإحصائيات')
@section('page_title', 'الإحصائيات')
@section('page_subtitle', 'إحصائيات الخريجين المسجلين في النظام')

@section('content')

<div class="card p-4 mb-4">
  <div class="grid grid-cols-12 gap-3 items-end">
    <div class="col-span-6 md:col-span-3">
      <label class="label text-xs mb-1">النوع</label>
      <select class="input text-sm" id="filterGender">
        <option value="">الكل</option>
        @foreach($genders as $g)
          <option value="{{ $g }}">{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-3">
      <label class="label text-xs mb-1">سنة التخرج</label>
      <select class="input text-sm" id="filterYear">
        <option value="">الكل</option>
        @foreach($years as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-3">
      <label class="label text-xs mb-1">المحافظة</label>
      <select class="input text-sm" id="filterGovernorate">
        <option value="">الكل</option>
        @foreach($allGovernorates as $gov)
          <option value="{{ $gov }}">{{ $gov }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-span-6 md:col-span-2">
      <label class="label text-xs mb-1">الحالة</label>
      <select class="input text-sm" id="filterStatus">
        <option value="">الكل</option>
        <option value="active">نشط</option>
        <option value="inactive">غير نشط</option>
      </select>
    </div>
    <div class="col-span-12 md:col-span-1 flex gap-2">
      <button class="btn btn-primary text-sm py-2 px-3 w-full" id="resetFilters">🔄</button>
    </div>
  </div>
</div>

<div class="card p-0">
  <div class="table-wrap">
    <table class="data" id="statisticsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>النوع</th>
          <th>سنة التخرج</th>
          <th>المحافظة</th>
          <th>الحالة</th>
          <th>تاريخ التسجيل</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
        <tr>
          <td>{{ $u->id }}</td>
          <td class="font-semibold">{{ $u->name }}</td>
          <td>
            @if($u->gender === 'ذكر')
              <span class="pill pill-blue">ذكر</span>
            @elseif($u->gender === 'أنثى')
              <span class="pill pill-rose">أنثى</span>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td>{{ $u->graduation_year ?? '—' }}</td>
          <td>{{ $u->governorate ?? '—' }}</td>
          <td>
            @if($u->status === 'active')
              <span class="pill pill-green">نشط</span>
            @else
              <span class="pill pill-rose">غير نشط</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $u->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script>
$(document).ready(function() {
  var table = $('#statisticsTable').DataTable({
    language: { url: '{{ asset('js/ar.json') }}' },
    order: [[0, 'desc']],
    columnDefs: [{ orderable: false, targets: [] }],
    dom: '<"flex items-center justify-between mb-3"B><"flex items-center gap-4"lf>tip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '📥 تصدير إكسل',
        className: 'btn btn-success text-sm',
        title: 'احصائيات الخريجين',
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6],
          modifier: { search: 'applied', order: 'applied' }
        }
      }
    ],
    initComplete: function() {
      $('.dt-buttons').addClass('mb-0');
    }
  });

  function applyFilters() {
    var gender = $('#filterGender').val();
    var year = $('#filterYear').val();
    var gov = $('#filterGovernorate').val();
    var status = $('#filterStatus').val();

    $.fn.dataTable.ext.search = [];

    if (gender) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[2] === gender;
      });
    }
    if (year) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[3] === year;
      });
    }
    if (gov) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        return data[4] === gov;
      });
    }
    if (status) {
      $.fn.dataTable.ext.search.push(function(settings, data) {
        var statusMap = { 'نشط': 'active', 'غير نشط': 'inactive' };
        return statusMap[data[5]] === status;
      });
    }

    table.draw();
  }

  $('#filterGender, #filterYear, #filterGovernorate, #filterStatus').on('change', applyFilters);

  $('#resetFilters').on('click', function() {
    $('#filterGender').val('');
    $('#filterYear').val('');
    $('#filterGovernorate').val('');
    $('#filterStatus').val('');
    $.fn.dataTable.ext.search = [];
    table.draw();
  });
});
</script>
@endpush

@endsection
