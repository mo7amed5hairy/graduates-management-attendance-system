<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $settings['hero_title'] ?? 'بوابة الخريجين العراقيين' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<style>
html { scroll-behavior: smooth; }
* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family:'Cairo',sans-serif;
  background:#f7f5f0;
  min-height:100vh; position:relative; overflow-x:hidden;
}
body::before {
  content:''; position:fixed; top:0; left:0; right:0; bottom:0;
  background-image:url('{{ asset("images/portal/bg_left.png") }}'), url('{{ asset("images/portal/bg_right.png") }}');
  background-repeat:no-repeat,no-repeat;
  background-position:left center,right center;
  background-size:350px auto,350px auto;
  opacity:0.10; z-index:0; pointer-events:none;
}
.page-wrapper { position:relative; z-index:1; }
.arabic-display { font-family:'Amiri',serif; }
.navy { background-color:#0d2f44; }
.navy-text { color:#0d2f44; }
.gold { color:#c9a24a; }
.gold-bg { background-color:#c9a24a; }

/* === TOP BAR === */
.top-bar { background:#0d2f44; color:white; font-size:11px; padding:5px 24px; text-align:left; direction:ltr; }

/* === HEADER === */
.main-header { background:white; box-shadow:0 1px 4px rgba(0,0,0,0.08); padding:10px 0; position:sticky; top:0; z-index:100; }
.main-header .inner { max-width:1100px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 24px; gap:12px; }
.main-header .logo { height:60px; width:60px; object-fit:contain; flex-shrink:0; }
.main-header nav { display:flex; align-items:center; gap:20px; font-size:14px; font-weight:600; color:#0d2f44; flex-wrap:wrap; }
.main-header nav a { text-decoration:none; color:#0d2f44; transition:color 0.2s; white-space:nowrap; }
.main-header nav a:hover { color:#c9a24a; }
.main-header nav a.active { color:#c9a24a; border-bottom:2px solid #c9a24a; padding-bottom:3px; }
.login-btn { background:#0d2f44; color:white; border:none; padding:8px 22px; border-radius:6px; font-size:13px; font-weight:700; font-family:'Cairo',sans-serif; cursor:pointer; white-space:nowrap; }
.login-btn:hover { opacity:0.9; }
.mobile-menu-btn { display:none; background:none; border:none; font-size:24px; color:#0d2f44; cursor:pointer; }

/* === HERO === */
.hero-wrapper { max-width:1100px; margin:16px auto 0; padding:0 16px; }
.hero-box { background:linear-gradient(180deg,#0b2a3e 0%,#0f3b52 40%,#14567a 100%); border:3px solid #c9a24a; border-radius:14px; overflow:hidden; position:relative; }
.hero-box::before { content:''; position:absolute; top:0; left:0; right:0; bottom:0; background:radial-gradient(ellipse at center top,rgba(201,162,74,0.08) 0%,transparent 60%); pointer-events:none; }
.hero-title-area { text-align:center; padding:28px 16px 12px; position:relative; z-index:1; }
.hero-title-area h1 { font-family:'Amiri',serif; font-size:clamp(22px,5vw,36px); font-weight:700; color:#c9a24a; margin-bottom:6px; line-height:1.5; text-shadow:0 2px 12px rgba(201,162,74,0.25); }
.hero-title-area p { color:rgba(255,255,255,0.9); font-size:clamp(12px,2.5vw,14px); margin-bottom:10px; }
.hero-divider { width:70%; max-width:500px; height:1px; background:linear-gradient(90deg,transparent,#c9a24a,transparent); margin:0 auto 8px; }
.hero-content { padding:0; }
.medalions-area { width:100%; }
.medalions-area img { width:100%; height:auto; }

/* === BOTTOM BAND === */
.bottom-band-wrapper { max-width:1100px; margin:0 auto; padding:0 16px; }
.bottom-band { position:relative; background:linear-gradient(180deg,#0b2a3e 0%,#113d56 40%,#0b2a3e 100%); border-top:3px solid #c9a24a; border-bottom:3px solid #c9a24a; text-align:center; padding:14px 40px; overflow:hidden; }
.bottom-band span { font-family:'Amiri',serif; font-size:clamp(14px,3vw,18px); font-weight:700; color:#c9a24a; text-shadow:0 0 20px rgba(201,162,74,0.3); position:relative; z-index:1; }
.bottom-band .line-top,.bottom-band .line-bottom { position:absolute; left:80px; right:80px; height:1px; background:linear-gradient(90deg,transparent,#c9a24a,transparent); }
.bottom-band .line-top { top:6px; }
.bottom-band .line-bottom { bottom:6px; }

/* === SECTION TITLES === */
.section-title { text-align:center; font-size:clamp(18px,4vw,24px); font-weight:700; color:#0d2f44; margin-bottom:4px; }
.section-divider { width:200px; max-width:60%; height:2px; background:linear-gradient(90deg,transparent,#c9a24a,transparent); margin:0 auto 20px; }

/* === STATS === */
.stats-wrapper { max-width:1100px; margin:32px auto 0; padding:0 16px; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:14px; }
.stat-card { background:white; border-radius:12px; border:1px solid #e6dcc5; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
.stat-card h3 { font-size:15px; font-weight:700; color:#0d2f44; margin-bottom:12px; text-align:right; }
.stat-number { font-size:36px; font-weight:900; color:#0d2f44; text-align:center; }
.stat-label { font-size:13px; color:#666; text-align:center; margin-top:4px; }
.stat-row { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #f1f1f1; font-size:13px; }
.stat-row:last-child { border-bottom:none; }

/* === TICKER === */
.ticker-bar { display:flex; align-items:center; gap:8px; margin-bottom:16px; }
.ticker-badge { background:#c0392b; color:white; font-size:12px; font-weight:700; padding:8px 14px; border-radius:6px; white-space:nowrap; flex-shrink:0; }
.ticker-track { flex:1; background:white; border:1px solid #e6dcc5; border-radius:6px; padding:8px 14px; font-size:13px; color:#0d2f44; overflow:hidden; position:relative; height:36px; }
.ticker-scroll { display:flex; align-items:center; white-space:nowrap; animation:tickerScroll 20s linear infinite; position:absolute; right:14px; top:50%; transform:translateY(-50%); }
.ticker-scroll span { padding-left:60px; }
@keyframes tickerScroll { 0%{transform:translateY(-50%) translateX(0);} 100%{transform:translateY(-50%) translateX(-50%);} }

/* === NEWS BODY === */
.news-wrapper { max-width:1100px; margin:36px auto 0; padding:0 16px; }
.news-body { display:grid; grid-template-columns:1fr 2fr; gap:14px; }

/* FAQ */
.faq-card { background:white; border-radius:12px; border:1px solid #e6dcc5; padding:16px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
.faq-card h3 { font-size:15px; font-weight:700; color:#0d2f44; margin-bottom:12px; text-align:right; }
.faq-list { list-style:none; display:flex; flex-direction:column; gap:8px; }
.faq-item-wrap { border:1px solid #d9c78a; border-radius:8px; overflow:hidden; transition:all 0.3s ease; }
.faq-item-wrap.active { border-color:#c9a24a; }
.faq-item { display:flex; align-items:center; justify-content:space-between; background:#fdf7e8; padding:10px 14px; font-size:13px; color:#0d2f44; cursor:pointer; user-select:none; transition:background 0.2s; }
.faq-item:hover { background:#f5edd5; }
.faq-item svg { width:16px; height:16px; color:#888; flex-shrink:0; transition:transform 0.3s ease; }
.faq-item-wrap.active .faq-item svg { transform:rotate(180deg); color:#c9a24a; }
.faq-answer { max-height:0; overflow:hidden; transition:max-height 0.35s ease,padding 0.35s ease; background:white; padding:0 14px; font-size:12px; line-height:1.7; color:#555; }
.faq-item-wrap.active .faq-answer { max-height:200px; padding:12px 14px; }

/* News Grid */
.news-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; }
.news-card { background:white; border-radius:10px; overflow:hidden; border:1px solid #e6dcc5; box-shadow:0 2px 10px rgba(0,0,0,0.06); cursor:pointer; transition:transform 0.25s ease,box-shadow 0.25s ease; }
.news-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,0.12); }
.news-card .thumb { position:relative; width:100%; height:100px; overflow:hidden; }
.news-card .thumb img { width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease; }
.news-card:hover .thumb img { transform:scale(1.08); }
.news-card .thumb .play-overlay { position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease; }
.news-card:hover .thumb .play-overlay { opacity:1; }
.play-btn { width:36px; height:36px; background:rgba(255,255,255,0.92); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 12px rgba(0,0,0,0.25); }
.play-btn svg { width:14px; height:14px; fill:#0d2f44; margin-right:-1px; }
.news-card .info { padding:8px 8px 10px; }
.news-card .info p.title { font-size:11px; font-weight:700; color:#0d2f44; margin-bottom:3px; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.news-card .info p.date { font-size:10px; color:#999; }

/* Video Pagination */
.video-pagination { display:flex; justify-content:center; align-items:center; gap:4px; margin-top:16px; flex-wrap:wrap; grid-column:1/-1; }
.video-pagination button { background:white; border:1px solid #d9c78a; color:#0d2f44; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:600; font-family:'Cairo',sans-serif; cursor:pointer; transition:all 0.2s; }
.video-pagination button:hover { background:#fdf7e8; }
.video-pagination button.active { background:#c9a24a; color:white; border-color:#c9a24a; }
.video-pagination button:disabled { opacity:0.4; cursor:default; }

/* === VIDEO MODAL === */
.video-modal-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease; }
.video-modal-overlay.show { display:flex; opacity:1; }
.video-modal { background:#000; border-radius:12px; overflow:hidden; width:92%; max-width:800px; position:relative; box-shadow:0 20px 60px rgba(0,0,0,0.5); }
.video-modal .video-header { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#111; color:white; font-size:13px; font-weight:600; }
.video-modal .video-header .close-btn { background:none; border:none; color:white; font-size:22px; cursor:pointer; padding:0 4px; line-height:1; }
.video-modal .video-player { width:100%; aspect-ratio:16/9; background:#000; display:flex; align-items:center; justify-content:center; color:#555; font-size:14px; }
.video-modal .video-player iframe { width:100%; height:100%; border:none; }

/* === CTA === */
.cta-wrapper { max-width:1100px; margin:36px auto 0; padding:0 16px; text-align:center; }
.cta-wrapper h3 { font-size:clamp(17px,4vw,22px); font-weight:700; color:#0d2f44; margin-bottom:14px; }
.cta-buttons { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; }
.cta-btn-outline,.cta-btn-solid { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; font-family:'Cairo',sans-serif; cursor:pointer; }
.cta-btn-outline { background:#fdf7e8; border:1px solid #d9c78a; color:#0d2f44; }
.cta-btn-solid { background:#c9a24a; color:white; border:none; }

/* === FOOTER === */
.site-footer { background:#0d2f44; color:white; margin-top:44px; }
.footer-inner { max-width:1100px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:20px; padding:32px 20px 20px; }
.footer-col h4 { color:#c9a24a; font-weight:700; font-size:13px; margin-bottom:10px; }
.footer-col ul { list-style:none; display:flex; flex-direction:column; gap:6px; }
.footer-col ul li { font-size:12px; opacity:0.85; display:flex; align-items:center; gap:6px; }
.footer-logo { display:flex; flex-direction:column; align-items:center; justify-content:flex-start; padding-top:4px; }
.footer-logo img { width:80px; height:80px; object-fit:contain; }
.footer-bottom { border-top:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:space-between; padding:14px 20px; font-size:11px; opacity:0.7; max-width:1100px; margin:0 auto; flex-wrap:wrap; gap:8px; }
.footer-socials { display:flex; gap:10px; }
.footer-socials a { color:white; text-decoration:none; font-size:14px; }

/* === RESPONSIVE === */
@media(max-width:768px){
  .mobile-menu-btn { display:block; }
  .main-header nav { display:none; width:100%; flex-direction:column; align-items:stretch; gap:8px; padding:12px 0; }
  .main-header nav.open { display:flex; }
  .main-header .inner { flex-wrap:wrap; }
  .main-header nav a.active { border-bottom:none; background:#fdf7e8; padding:8px 12px; border-radius:6px; }
  .news-body { grid-template-columns:1fr; }
  .news-grid { grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); }
  .bottom-band { padding:12px 20px; }
  .bottom-band .line-top,.bottom-band .line-bottom { left:30px; right:30px; }
}
@media(max-width:480px){
  .main-header .inner { padding:0 12px; }
  .hero-title-area h1 { font-size:20px; }
  .stats-grid { grid-template-columns:1fr; }
  .news-grid { grid-template-columns:repeat(2,1fr); }
  .footer-inner { grid-template-columns:1fr 1fr; }
}
</style>
</head>
<body>
<div class="page-wrapper">

  {{-- Top Bar --}}
  <div class="top-bar">بوابة الخريجين العراقيين</div>

  {{-- Header --}}
  <header class="main-header">
    <div class="inner">
      <img src="{{ asset('images/portal/logo_grad.jpeg') }}" alt="logo" class="logo" />
      <button class="mobile-menu-btn" onclick="document.querySelector('.main-header nav').classList.toggle('open')">☰</button>
      <nav>
        <a href="#hero" class="active" onclick="closeMobileNav()">الرئيسية</a>
        <a href="#faq-section" onclick="closeMobileNav()">الأسئلة المتكررة</a>
        <a href="#news-section" onclick="closeMobileNav()">أخبارنا</a>
        <a href="#stats-section" onclick="closeMobileNav()">الإحصائيات</a>
        <a href="#footer" onclick="closeMobileNav()">من نحن</a>
      </nav>
      <a href="{{ route('login') }}"><button class="login-btn">تسجيل الدخول</button></a>
    </div>
  </header>

  {{-- Hero --}}
  <div id="hero" class="hero-wrapper">
    <div class="hero-box">
      <div class="hero-title-area">
        <h1 class="arabic-display">{{ $settings['hero_title'] ?? 'بوابة الخريجين العراقيين: مستقبل بيدينا' }}</h1>
        <p>{{ $settings['hero_subtitle'] ?? '' }}</p>
      </div>
      <div class="hero-divider"></div>
      <div class="hero-content">
        <div class="medalions-area">
          <img src="{{ asset('images/portal/medalions.png') }}" alt="medalions" />
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom Band --}}
  <div class="bottom-band-wrapper">
    <div class="bottom-band">
      <div class="line-top"></div>
      <span>{{ $settings['hero_motto'] ?? 'نحن ليس هدفنا التظاهر .. ولكن بأيدينا نصنع المستقبل' }}</span>
      <div class="line-bottom"></div>
    </div>
  </div>

  {{-- Stats --}}
  <div id="stats-section" class="stats-wrapper">
    <h2 class="section-title">{{ $settings['stats_title'] ?? 'الإحصائيات والبيانات' }}</h2>
    <div class="section-divider"></div>
    <div class="stats-grid">
      <div class="stat-card">
        <h3>إجمالي المستخدمين</h3>
        <div class="stat-number">{{ number_format($totalUsers) }}</div>
        <div class="stat-label">مستخدم مسجل</div>
      </div>
      <div class="stat-card">
        <h3>حسب الجنس</h3>
        <div class="stat-row"><span>ذكر</span><span class="font-bold">{{ number_format($maleCount) }}</span></div>
        <div class="stat-row"><span>أنثى</span><span class="font-bold">{{ number_format($femaleCount) }}</span></div>
      </div>
      <div class="stat-card">
        <h3>حالة الاعتماد</h3>
        <div class="stat-row"><span>✅ مقبول</span><span class="font-bold">{{ number_format($approvedCount) }}</span></div>
        <div class="stat-row"><span>⏳ قيد المراجعة</span><span class="font-bold">{{ number_format($pendingCount) }}</span></div>
      </div>
      <div class="stat-card">
        <h3>المحافظات</h3>
        <div class="stat-number">{{ number_format($governoratesCount) }}</div>
        <div class="stat-label">محافظة عراقية</div>
      </div>
    </div>
  </div>

  {{-- News & FAQ --}}
  <div id="news-section" class="news-wrapper">
    <h2 class="section-title">{{ $settings['news_section_title'] ?? 'آخر الأخبار والمستجدات' }}</h2>
    <div class="section-divider"></div>

    {{-- Ticker --}}
    @php
      $tickerNews = $news->take(8)->pluck('title')->implode(' ● ');
    @endphp
    @if($tickerNews)
    <div class="ticker-bar">
      <span class="ticker-badge">{{ $settings['ticker_badge'] ?? 'شريط الأخبار' }}</span>
      <div class="ticker-track">
        <div class="ticker-scroll">
          <span>{{ $tickerNews }} ● </span>
          <span>{{ $tickerNews }} ● </span>
        </div>
      </div>
    </div>
    @endif

    <div class="news-body">
      {{-- FAQ --}}
      <div id="faq-section" class="faq-card">
        <h3>الأسئلة المتكررة</h3>
        @if($faqs->count() > 0)
        <ul class="faq-list">
          @foreach($faqs as $faq)
          <li class="faq-item-wrap">
            <div class="faq-item" onclick="toggleFaq(this)">
              <span>{{ $faq->question }}</span>
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-answer">{{ $faq->answer }}</div>
          </li>
          @endforeach
        </ul>
        @else
        <p class="text-slate-400 text-sm">لا توجد أسئلة مضافة بعد</p>
        @endif
      </div>

      {{-- News/Video Grid --}}
      <div class="news-grid">
        @forelse($news as $item)
        <div class="news-card" onclick="openVideoModal('{{ addslashes($item->title) }}', '{{ $item->url }}')">
          <div class="thumb">
            <img src="{{ $item->image ? asset('images/portal/'.$item->image) : asset('images/portal/news1.png') }}" alt="{{ $item->title }}" loading="lazy" onerror="this.src='{{ asset('images/portal/ticker.png') }}'" />
            <div class="play-overlay"><div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></div>
          </div>
          <div class="info">
            <p class="title">{{ $item->title }}</p>
            <p class="date">{{ $item->news_date?->format('Y-m-d') ?? '' }}</p>
          </div>
        </div>
        @empty
        <p class="text-slate-400 text-sm col-span-full">لا توجد أخبار مضافة بعد</p>
        @endforelse

        @foreach($videos as $item)
        <div class="news-card video-item" onclick="openVideoModal('{{ addslashes($item->title) }}', '{{ $item->url }}')">
          <div class="thumb">
            <img src="{{ $item->image ? asset('images/portal/'.$item->image) : asset('images/portal/ticker.png') }}" alt="{{ $item->title }}" loading="lazy" onerror="this.src='{{ asset('images/portal/ticker.png') }}'" />
            <div class="play-overlay"><div class="play-btn"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div></div>
          </div>
          <div class="info">
            <p class="title">{{ $item->title }}</p>
            <p class="date">📹 فيديو</p>
          </div>
        </div>
        @endforeach

        {{-- Video Pagination --}}
        <div class="video-pagination" id="videoPagination"></div>
      </div>
    </div>
  </div>

  {{-- CTA --}}
  <div class="cta-wrapper">
    <h3>{{ $settings['cta_title'] ?? '✦ دعمك هو نجاحنا ✦' }}</h3>
    <div class="cta-buttons">
      @if(!empty($settings['cta_instagram_url']))
      <a href="{{ $settings['cta_instagram_url'] }}" target="_blank" rel="noopener" class="cta-btn-outline">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#E1306C"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        متابعة على الانستجرام
      </a>
      @endif
      @if(!empty($settings['cta_video_url']))
      <a href="{{ $settings['cta_video_url'] }}" target="_blank" rel="noopener" class="cta-btn-solid">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
        مشاهدة شروحات الفيديو
      </a>
      @endif
    </div>
  </div>

  {{-- Footer --}}
  <footer id="footer" class="site-footer">
    <div class="footer-inner">
      <div class="footer-col">
        <h4>المعلومات:</h4>
        <ul>
          <li>هيئة الخريجين العامة</li>
          <li>أمين السفراء، سعود الفلاح</li>
          <li>نائب المجالي</li>
          <li>أوائل الفصل</li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>أرقامنا:</h4>
        <ul>
          <li><span>📞</span> 0323302525</li>
          <li><span>📞</span> 0322396925</li>
          <li><span>📞</span> 0323963596</li>
          <li><span>📱</span> تواصل عبر واتساب</li>
        </ul>
      </div>
      <div class="footer-logo footer-col">
        <img src="{{ asset('images/portal/logo_grad.jpeg') }}" alt="logo" />
      </div>
      <div class="footer-col">
        <h4>Sitemap</h4>
        <ul>
          <li>روابط</li>
          <li>الأسئلة</li>
          <li>الدستور</li>
          <li>المعلم</li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>الاقبال</h4>
        <ul>
          <li>الخريجون حسب المؤهل</li>
          <li>الخريجون حسب المستقبل</li>
          <li>سياسة الاستخدام</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-socials">
        @if(!empty($settings['cta_instagram_url']))
        <a href="{{ $settings['cta_instagram_url'] }}" target="_blank" rel="noopener">📷</a>
        @endif
        <a href="#">📘</a>
        <a href="#">🐦</a>
        <a href="#">📺</a>
      </div>
      <div class="footer-copyright-center">{{ $settings['footer_copyright'] ?? 'نصنع المستقبل' }}</div>
      <div>{{ $settings['footer_rights'] ?? 'جميع الحقوق محفوظة' }}</div>
    </div>
  </footer>

</div>

{{-- Video Modal --}}
<div class="video-modal-overlay" id="videoModal">
  <div class="video-modal">
    <div class="video-header">
      <span id="videoModalTitle">عنوان الفيديو</span>
      <button class="close-btn" onclick="closeVideoModal()">&times;</button>
    </div>
    <div class="video-player" id="videoPlayer">
      <p>سيتم تحميل الفيديو هنا...</p>
    </div>
  </div>
</div>

<script>
function closeMobileNav() {
  document.querySelector('.main-header nav')?.classList.remove('open');
}

function toggleFaq(el) {
  const wrap = el.parentElement;
  const wasActive = wrap.classList.contains('active');
  document.querySelectorAll('.faq-item-wrap').forEach(item => item.classList.remove('active'));
  if (!wasActive) wrap.classList.add('active');
}

function openVideoModal(title, url) {
  const modal = document.getElementById('videoModal');
  document.getElementById('videoModalTitle').textContent = title;
  var player = document.getElementById('videoPlayer');
  if (url.includes('youtube.com') || url.includes('youtu.be')) {
    var videoId = '';
    if (url.includes('youtube.com/watch?v=')) videoId = url.split('v=')[1]?.split('&')[0];
    else if (url.includes('youtu.be/')) videoId = url.split('youtu.be/')[1]?.split('?')[0];
    if (videoId) {
      player.innerHTML = '<iframe src="https://www.youtube.com/embed/' + videoId + '" allowfullscreen></iframe>';
    } else {
      player.innerHTML = '<p>رابط فيديو غير صالح</p>';
    }
  } else if (url.includes('instagram.com')) {
    player.innerHTML = '<p>تم فتح الرابط: <a href="' + url + '" target="_blank" style="color:#c9a24a">اضغط هنا</a></p>';
  } else {
    player.innerHTML = '<p>تم فتح الرابط: <a href="' + url + '" target="_blank" style="color:#c9a24a">اضغط هنا</a></p>';
  }
  modal.style.display = 'flex';
  setTimeout(() => modal.classList.add('show'), 10);
}

function closeVideoModal() {
  const modal = document.getElementById('videoModal');
  modal.classList.remove('show');
  setTimeout(() => {
    modal.style.display = 'none';
    document.getElementById('videoPlayer').innerHTML = '<p>سيتم تحميل الفيديو هنا...</p>';
  }, 300);
}

document.getElementById('videoModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeVideoModal();
});

// Smooth scroll for nav links
document.querySelectorAll('nav a[href^="#"]').forEach(a => {
  a.addEventListener('click', function(e) {
    e.preventDefault();
    var target = document.querySelector(this.getAttribute('href'));
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// Video pagination
(function() {
  var items = document.querySelectorAll('.video-item');
  var perPage = 8;
  var total = items.length;
  var totalPages = Math.ceil(total / perPage);
  var container = document.getElementById('videoPagination');
  if (total <= perPage || !container) return;

  function showPage(page) {
    var start = (page - 1) * perPage;
    var end = start + perPage;
    items.forEach(function(el, i) {
      el.style.display = (i >= start && i < end) ? '' : 'none';
    });
    var btns = container.querySelectorAll('button');
    btns.forEach(function(b) {
      b.classList.toggle('active', parseInt(b.dataset.page) === page);
    });
  }

  var prevBtn = document.createElement('button');
  prevBtn.textContent = '‹';
  prevBtn.disabled = true;
  prevBtn.addEventListener('click', function() {
    var active = container.querySelector('.active');
    if (active) {
      var p = parseInt(active.dataset.page);
      if (p > 1) showPage(p - 1);
    }
  });
  container.appendChild(prevBtn);

  for (var i = 1; i <= totalPages; i++) {
    var btn = document.createElement('button');
    btn.textContent = i;
    btn.dataset.page = i;
    if (i === 1) btn.className = 'active';
    btn.addEventListener('click', function() {
      showPage(parseInt(this.dataset.page));
    });
    container.appendChild(btn);
  }

  var nextBtn = document.createElement('button');
  nextBtn.textContent = '›';
  nextBtn.addEventListener('click', function() {
    var active = container.querySelector('.active');
    if (active) {
      var p = parseInt(active.dataset.page);
      if (p < totalPages) showPage(p + 1);
    }
  });
  container.appendChild(nextBtn);

  showPage(1);
})();
</script>

</body>
</html>
