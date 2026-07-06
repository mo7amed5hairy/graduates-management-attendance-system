@extends('layouts.admin')
@section('title', 'إدارة البوابة')
@section('page_title', 'إدارة الصفحة الرئيسية')
@section('page_subtitle', 'التحكم في محتوى الصفحة الترحيبية')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

  @if(session('success'))
  <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
  @endif

  {{-- Settings --}}
  <div class="card p-6">
    <h3 class="font-bold text-slate-900 mb-4">⚙️ إعدادات النصوص</h3>
    <form method="POST" action="{{ route('admin.portal.settings') }}">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach([
          'hero_title' => 'عنوان الهيرو',
          'hero_subtitle' => 'نص الهيرو الفرعي',
          'hero_motto' => 'الشعار (الباند الذهبي)',
          'stats_title' => 'عنوان قسم الإحصائيات',
          'ticker_badge' => 'نص شريط الأخبار',
          'news_section_title' => 'عنوان قسم الأخبار',
          'cta_title' => 'نص الدعوة للإجراء',
          'cta_instagram_url' => 'رابط إنستغرام',
          'cta_video_url' => 'رابط شروحات الفيديو',
          'footer_copyright' => 'نص الحقوق (يسار)',
          'footer_rights' => 'نص جميع الحقوق (يمين)',
        ] as $key => $label)
        <div>
          <label class="label">{{ $label }}</label>
          <input class="input" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}">
        </div>
        @endforeach
      </div>
      <button type="submit" class="btn btn-primary mt-4">💾 حفظ الإعدادات</button>
    </form>
  </div>

  {{-- News --}}
  <div class="card p-6">
    <h3 class="font-bold text-slate-900 mb-4">📰 الأخبار</h3>
    <form method="POST" action="{{ route('admin.portal.news.store') }}" class="flex flex-wrap gap-3 mb-4 p-4 bg-slate-50 rounded-xl">
      @csrf
      <input class="input flex-1 min-w-[150px]" name="title" placeholder="العنوان" required>
      <input class="input flex-1 min-w-[200px]" name="url" placeholder="الرابط URL" required>
      <input class="input w-32" name="image" placeholder="صورة (اختياري)">
      <input class="input w-36" name="news_date" type="date">
      <button type="submit" class="btn btn-success shrink-0">➕ إضافة</button>
    </form>
    <div class="overflow-x-auto">
      <table class="data w-full text-sm">
        <thead><tr><th>العنوان</th><th>الرابط</th><th>الصورة</th><th>التاريخ</th><th>فعال</th><th></th></tr></thead>
        <tbody>
          @foreach($news as $item)
          <tr>
            <td class="font-semibold">{{ $item->title }}</td>
            <td class="text-xs"><a href="{{ $item->url }}" target="_blank" class="text-sky-600 hover:underline">{{ $item->url }}</a></td>
            <td class="text-xs">{{ $item->image ?? '—' }}</td>
            <td class="text-xs">{{ $item->news_date?->format('Y-m-d') ?? '—' }}</td>
            <td>
              <a href="{{ route('admin.portal.news.toggle', $item) }}" class="btn btn-ghost py-1 px-2 text-xs">{{ $item->is_active ? '🟢' : '🔴' }}</a>
            </td>
            <td>
              <form method="POST" action="{{ route('admin.portal.news.destroy', $item) }}" style="display:inline" onsubmit="return confirm('حذف؟')">
                @csrf @method('DELETE')
                <button class="btn btn-danger py-1 px-2 text-xs">🗑️</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Videos --}}
  <div class="card p-6">
    <h3 class="font-bold text-slate-900 mb-4">🎬 الفيديوهات</h3>
    <form method="POST" action="{{ route('admin.portal.videos.store') }}" class="flex flex-wrap gap-3 mb-4 p-4 bg-slate-50 rounded-xl">
      @csrf
      <input class="input flex-1 min-w-[150px]" name="title" placeholder="العنوان" required>
      <input class="input flex-1 min-w-[200px]" name="url" placeholder="الرابط URL" required>
      <input class="input w-32" name="image" placeholder="صورة (اختياري)">
      <button type="submit" class="btn btn-success shrink-0">➕ إضافة</button>
    </form>
    <div class="overflow-x-auto">
      <table class="data w-full text-sm">
        <thead><tr><th>العنوان</th><th>الرابط</th><th>الصورة</th><th>فعال</th><th></th></tr></thead>
        <tbody>
          @foreach($videos as $item)
          <tr>
            <td class="font-semibold">{{ $item->title }}</td>
            <td class="text-xs"><a href="{{ $item->url }}" target="_blank" class="text-sky-600 hover:underline">{{ $item->url }}</a></td>
            <td class="text-xs">{{ $item->image ?? '—' }}</td>
            <td>
              <a href="{{ route('admin.portal.videos.toggle', $item) }}" class="btn btn-ghost py-1 px-2 text-xs">{{ $item->is_active ? '🟢' : '🔴' }}</a>
            </td>
            <td>
              <form method="POST" action="{{ route('admin.portal.videos.destroy', $item) }}" style="display:inline" onsubmit="return confirm('حذف؟')">
                @csrf @method('DELETE')
                <button class="btn btn-danger py-1 px-2 text-xs">🗑️</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- FAQs --}}
  <div class="card p-6">
    <h3 class="font-bold text-slate-900 mb-4">❓ الأسئلة المتكررة</h3>
    <form method="POST" action="{{ route('admin.portal.faqs.store') }}" class="flex flex-wrap gap-3 mb-4 p-4 bg-slate-50 rounded-xl">
      @csrf
      <input class="input flex-1 min-w-[200px]" name="question" placeholder="السؤال" required>
      <textarea class="input flex-[2] min-w-[250px]" name="answer" placeholder="الإجابة" required></textarea>
      <button type="submit" class="btn btn-success shrink-0">➕ إضافة</button>
    </form>
    <div class="space-y-2">
      @foreach($faqs as $item)
      <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl">
        <div class="flex-1">
          <div class="font-semibold text-sm">{{ $item->question }}</div>
          <div class="text-xs text-slate-500 mt-1">{{ Str::limit($item->answer, 100) }}</div>
        </div>
        <form method="POST" action="{{ route('admin.portal.faqs.destroy', $item) }}" onsubmit="return confirm('حذف؟')">
          @csrf @method('DELETE')
          <button class="btn btn-danger py-1 px-2 text-xs">🗑️</button>
        </form>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
