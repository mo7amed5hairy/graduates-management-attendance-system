<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'لوحة التحكم') — {{ config('app.name') }}</title>
<script src="{{ asset('js/tailwind.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTables.min.css') }}">
</head>
<body>

<div class="loader-overlay" id="globalLoader">
  <div class="spinner"></div>
</div>

<div class="layout-wrapper">

  <aside class="side" id="sidebar">
    <div class="flex items-center gap-3 mb-6 px-2">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xl">👥</div>
      <div>
        <div class="font-extrabold text-slate-900">نظام المستخدمين</div>
        <div class="text-xs text-slate-500">لوحة التحكم</div>
      </div>
    </div>
    <nav>
      <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('home') }}" class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('home') ? 'active' : '' }}">
        <span>🏠</span><span>الرئيسية</span>
      </a>
      @if(auth()->user()->isAdmin())
      <a href="{{ route('admin.graduates.index') }}" class="{{ request()->routeIs('admin.graduates.*') ? 'active' : '' }}">
        <span>🎓</span><span>الخريجين</span>
      </a>
      <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
        <span>📅</span><span>الفعاليات</span>
      </a>
      <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
        <span>📋</span><span>الحضور</span>
      </a>
      <a href="{{ route('admin.points.index') }}" class="{{ request()->routeIs('admin.points.*') ? 'active' : '' }}">
        <span>⭐</span><span>النقاط</span>
      </a>
      <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <span>👥</span><span>إدارة المستخدمين</span>
      </a>
      <a href="{{ route('admin.tasks.index') }}" class="{{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
        <span>📋</span><span>المهام</span>
      </a>
      <a href="{{ route('admin.import.index') }}" class="{{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
        <span>📥</span><span>استيراد</span>
      </a>
      @endif
      @if(!auth()->user()->isAdmin())
      <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.index') ? 'active' : '' }}">
        <span>📋</span><span>مهامي</span>
      </a>
      <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}">
        <span>📅</span><span>فعالياتي</span>
      </a>
      @endif
      <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <span>👤</span><span>البروفايل</span>
      </a>
      <hr class="my-3 border-slate-100">
      <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <span>↩️</span><span>تسجيل الخروج</span>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </nav>
  </aside>

  <div class="main-area">
    <header class="topbar">
      <div class="flex items-center gap-3">
        <button class="btn btn-ghost p-2 sidebar-toggle" id="sidebarToggle">☰</button>
        <div>
          <h1 class="text-xl font-extrabold text-slate-900">@yield('page_title', 'لوحة التحكم')</h1>
          <p class="text-sm text-slate-500">@yield('page_subtitle', '')</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        {{-- Notifications --}}
        <div class="relative" id="notifWrap">
          <button class="relative p-2 text-slate-500 hover:text-slate-700 transition" id="notifBtn" onclick="toggleNotif()">
            <span class="text-xl">🔔</span>
            <span class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1" id="notifBadge" style="display:none">0</span>
          </button>
          <div class="absolute left-0 top-full mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 hidden" id="notifDropdown">
            <div class="p-3 border-b border-slate-100 flex justify-between items-center">
              <span class="font-bold text-sm text-slate-900">الإشعارات</span>
              <button class="text-xs text-sky-600 hover:underline" id="markAllNotif" style="display:none">تحديد الكل مقروء</button>
            </div>
            <div class="max-h-64 overflow-y-auto" id="notifList">
              <div class="p-4 text-center text-sm text-slate-400">لا توجد إشعارات</div>
            </div>
            <a href="{{ route('notifications.index') }}" class="block p-3 text-center text-sm text-sky-600 font-bold hover:bg-sky-50 rounded-b-xl border-t border-slate-100">
              📋 عرض جميع الإشعارات
            </a>
          </div>
        </div>
        <div class="text-left">
          <div class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</div>
          <div class="text-xs text-slate-500">
            @if(auth()->user()->isAdmin())
              <span class="pill pill-violet">مدير</span>
            @else
              <span class="pill pill-blue">مستخدم</span>
            @endif
          </div>
        </div>
        @if(auth()->user()->image)
          <img src="{{ auth()->user()->image_url }}" alt="{{ auth()->user()->name }}" class="avatar" style="object-fit:cover">
        @else
          <div class="avatar bg-gradient-to-br from-sky-500 to-indigo-600 text-white">
            {{ substr(auth()->user()->name, 0, 2) }}
          </div>
        @endif
      </div>
    </header>

    <main class="p-6">
      @yield('content')
    </main>
  </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
// Notification system
let notifVisible = false;
var notifBase = '{{ route('notifications.index') }}'.replace(/\/+$/, '');
var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

function toggleNotif() {
  const dd = document.getElementById('notifDropdown');
  notifVisible = !notifVisible;
  dd.classList.toggle('hidden', !notifVisible);
  if (notifVisible) fetchNotifList();
}

document.addEventListener('click', function(e) {
  if (!e.target.closest('#notifWrap')) {
    document.getElementById('notifDropdown')?.classList.add('hidden');
    notifVisible = false;
  }
});

async function fetchNotifCount() {
  try {
    const res = await fetch(notifBase + '/unread-count', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) { console.warn('notif count error', res.status); return; }
    const data = await res.json();
    const badge = document.getElementById('notifBadge');
    if (!badge) return;
    if (data.count > 0) {
      badge.textContent = data.count;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  } catch(e) { console.warn('fetchNotifCount failed', e); }
}

async function fetchNotifList() {
  try {
    const res = await fetch(notifBase + '/latest', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) { console.warn('notif list error', res.status); return; }
    const data = await res.json();
    const list = document.getElementById('notifList');
    const markBtn = document.getElementById('markAllNotif');
    if (!list) return;
    if (!data.data || data.data.length === 0) {
      list.innerHTML = '<div class="p-4 text-center text-sm text-slate-400">لا توجد إشعارات جديدة</div>';
      if (markBtn) markBtn.style.display = 'none';
      return;
    }
    if (markBtn) markBtn.style.display = 'block';
    list.innerHTML = data.data.map(function(n) {
      var taskId = (n.data && n.data.task_id) || 0;
      var detailUrl = '{{ route('tasks.index') }}' + '/' + taskId;
      return '<div class="flex items-start gap-2 p-3 hover:bg-sky-50 border-b border-slate-50 notif-item" data-id="' + n.id + '" data-task="' + taskId + '">' +
        '<span class="text-lg">📋</span>' +
        '<div class="flex-1 min-w-0">' +
          '<div class="text-sm font-semibold text-slate-900">تم تكليفك بمهمة: <a href="' + detailUrl + '" class="text-sky-600 hover:underline" onclick="fetch(\'' + notifBase + '/' + n.id + '/read\', {method:\'POST\',headers:{\'X-Requested-With\':\'XMLHttpRequest\',\'X-CSRF-TOKEN\':\'' + csrfToken + '\'}}).then(function(){ fetchNotifCount(); });">' + ((n.data && n.data.title) || '') + '</a></div>' +
          '<div class="text-xs text-slate-400">' + n.created_at + '</div>' +
        '</div>' +
        '<button class="text-sky-600 text-xs font-bold mark-notif-read" onclick="event.stopPropagation(); event.preventDefault(); markNotifRead(this, ' + n.id + ')" data-id="' + n.id + '">✓</button>' +
      '</div>';
    }).join('');
  } catch(e) { console.warn('fetchNotifList failed', e); }
}

async function markNotifRead(btn, id) {
  try {
    await fetch(notifBase + '/' + id + '/read', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } });
    var item = btn.closest('.notif-item');
    if (item) item.remove();
    fetchNotifCount();
    var list = document.getElementById('notifList');
    var markBtn = document.getElementById('markAllNotif');
    if (list && list.querySelectorAll('.notif-item').length === 0) {
      list.innerHTML = '<div class="p-4 text-center text-sm text-slate-400">لا توجد إشعارات جديدة</div>';
      if (markBtn) markBtn.style.display = 'none';
    }
  } catch(e) { console.warn('mark-read failed', e); }
}

document.getElementById('markAllNotif')?.addEventListener('click', async function() {
  try {
    await fetch(notifBase + '/read-all', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken } });
    document.getElementById('notifList').innerHTML = '<div class="p-4 text-center text-sm text-slate-400">لا توجد إشعارات جديدة</div>';
    this.style.display = 'none';
    document.getElementById('notifBadge').style.display = 'none';
  } catch(e) { console.warn('markAllNotif failed', e); }
});

// Poll for new notifications every 30s
fetchNotifCount();
setInterval(fetchNotifCount, 30000);
</script>
@stack('scripts')
</body>
</html>
