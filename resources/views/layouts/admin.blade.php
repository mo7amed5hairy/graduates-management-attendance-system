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
    <div class="flex items-center gap-3 mb-6 px-2 flex-shrink-0">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xl">👥</div>
      <div>
        <div class="font-extrabold text-slate-900">نظام المستخدمين</div>
        <div class="text-xs text-slate-500">لوحة التحكم</div>
      </div>
    </div>
    <div class="side-nav-wrap">
      <nav>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('home') }}" class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('home') ? 'active' : '' }}">
          <span>🏠</span><span>الرئيسية</span>
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.graduates.index') }}" class="{{ request()->routeIs('admin.graduates.*') ? 'active' : '' }}">
          <span>🎓</span><span>الخريجين</span>
        </a>
        <div class="nav-group">
          @php
            $dataRoutes = ['admin.governorates.*', 'admin.institution-types.*', 'admin.university-types.*', 'admin.institutions.*', 'admin.departments.*'];
            $dataActive = request()->routeIs($dataRoutes);
          @endphp
          <a href="#" onclick="toggleNavGroup(this); return false;" class="nav-group-toggle{{ $dataActive ? ' open' : '' }}">
            <span>⚙️</span><span>المحافظات والجامعات والأقسام</span><span class="nav-arrow">{{ $dataActive ? '▲' : '▼' }}</span>
          </a>
          <div class="nav-sub" id="navDataGroup" style="display: {{ $dataActive ? 'block' : 'none' }};">
            <a href="{{ route('admin.governorates.index') }}" class="{{ request()->routeIs('admin.governorates.*') ? 'active' : '' }}">
              <span>🏛️</span><span>المحافظات</span>
            </a>
            <a href="{{ route('admin.institution-types.index') }}" class="{{ request()->routeIs('admin.institution-types.*') ? 'active' : '' }}">
              <span>🏫</span><span>أنواع المؤسسات</span>
            </a>
            <a href="{{ route('admin.university-types.index') }}" class="{{ request()->routeIs('admin.university-types.*') ? 'active' : '' }}">
              <span>📚</span><span>أنواع الجامعات</span>
            </a>
            <a href="{{ route('admin.institutions.index') }}" class="{{ request()->routeIs('admin.institutions.*') ? 'active' : '' }}">
              <span>🎓</span><span>المؤسسات التعليمية</span>
            </a>
            <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
              <span>📖</span><span>الأقسام والتخصصات</span>
            </a>
          </div>
        </div>
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
        <div class="nav-group">
          @php
            $qualRoutes = ['admin.qualifications.*', 'admin.qualification-faculties.*'];
            $qualActive = request()->routeIs($qualRoutes);
          @endphp
          <a href="#" onclick="toggleNavGroup(this); return false;" class="nav-group-toggle{{ $qualActive ? ' open' : '' }}">
            <span>📚</span><span>المؤهلات الدراسية</span><span class="nav-arrow">{{ $qualActive ? '▲' : '▼' }}</span>
          </a>
          <div class="nav-sub" id="navQualGroup" style="display: {{ $qualActive ? 'block' : 'none' }};">
            <a href="{{ route('admin.qualifications.index') }}" class="{{ request()->routeIs('admin.qualifications.*') ? 'active' : '' }}">
              <span>🎓</span><span>المؤهلات</span>
            </a>
            <a href="{{ route('admin.qualification-faculties.index') }}" class="{{ request()->routeIs('admin.qualification-faculties.*') ? 'active' : '' }}">
              <span>🏛️</span><span>الكليات</span>
            </a>
          </div>
        </div>
        <a href="{{ route('admin.statistics.index') }}" class="{{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}">
          <span>📊</span><span>الإحصائيات</span>
        </a>
        <a href="{{ route('admin.profile-change-requests.index') }}" class="{{ request()->routeIs('admin.profile-change-requests.*') ? 'active' : '' }}">
          <span>📝</span><span>طلبات التعديل</span>
        </a>
        <a href="{{ route('admin.tasks.index') }}" class="{{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
          <span>📋</span><span>المهام</span>
        </a>
        <a href="{{ route('admin.import.index') }}" class="{{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
          <span>📥</span><span>استيراد</span>
        </a>
        @if(auth()->user()->email === 'admin@admin.com')
        <a href="{{ route('admin.sub-admins.index') }}" class="{{ request()->routeIs('admin.sub-admins.*') ? 'active' : '' }}">
          <span>🔐</span><span>المشرفين والصلاحيات</span>
        </a>
        @endif
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
    </div>
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

    <footer class="app-footer">
      <span>الحقوق البرمجية محفوظة &copy; {{ date('Y') }}</span>
    </footer>
  </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
// Collapsible nav group
function toggleNavGroup(btn) {
  var sub = btn.nextElementSibling;
  var arrow = btn.querySelector('.nav-arrow');
  if (sub.style.display === 'none' || sub.style.display === '') {
    sub.style.display = 'block';
    arrow.textContent = '▲';
    btn.classList.add('open');
  } else {
    sub.style.display = 'none';
    arrow.textContent = '▼';
    btn.classList.remove('open');
  }
}

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
    var graduatesUrl = '{{ route('admin.graduates.index') }}';
    list.innerHTML = data.data.map(function(n) {
      var icon, text, linkUrl;
      switch (n.type) {
        case 'new_registration':
          icon = '&#x1F464;';
          text = 'تسجيل خريج جديد: ' + (n.data && n.data.user_name || '');
          linkUrl = graduatesUrl;
          break;
        case 'event_invitation':
          icon = '&#x1F4C5;';
          text = (n.data && n.data.message) || 'دعوة لفعالية';
          linkUrl = '{{ route('events.index') }}';
          break;
        case 'approval':
          icon = '&#x2705;';
          text = (n.data && n.data.message) || 'تم اعتماد حسابك';
          linkUrl = '{{ route('profile.show') }}';
          break;
        case 'rejection':
          icon = '&#x274C;';
          text = 'تم رفض حسابك';
          linkUrl = '{{ route('profile.show') }}';
          break;
        case 'profile_change_request':
          icon = '&#x1F4DD;';
          text = (n.data && n.data.message) || 'طلب تعديل بيانات';
          linkUrl = '{{ route('admin.profile-change-requests.index') }}';
          break;
        case 'profile_change_approved':
          icon = '&#x2705;';
          text = (n.data && n.data.message) || 'تمت الموافقة على طلبك';
          linkUrl = '{{ route('profile.show') }}';
          break;
        case 'profile_change_rejected':
          icon = '&#x274C;';
          text = (n.data && n.data.message) || 'تم رفض طلبك';
          linkUrl = '{{ route('profile.show') }}';
          break;
        default:
          icon = '&#x1F4CB;';
          var taskId = (n.data && n.data.task_id) || 0;
          text = (n.data && n.data.title) || 'إشعار';
          linkUrl = '{{ route('tasks.index') }}' + '/' + taskId;
      }
      var markReadUrl = notifBase + '/' + n.id + '/read';
      return '<div class="flex items-start gap-2 p-3 hover:bg-sky-50 border-b border-slate-50 notif-item" data-id="' + n.id + '">' +
        '<span class="text-lg">' + icon + '</span>' +
        '<div class="flex-1 min-w-0">' +
          '<div class="text-sm font-semibold text-slate-900"><a href="' + linkUrl + '" class="text-sky-600 hover:underline" onclick="event.preventDefault(); var self=this; fetch(\'' + markReadUrl + '\',{method:\'POST\',headers:{\'X-Requested-With\':\'XMLHttpRequest\',\'X-CSRF-TOKEN\':\'' + csrfToken + '\'},keepalive:true}).then(function(){ window.location.href=self.href; }).catch(function(){ window.location.href=self.href; });">' + text + '</a></div>' +
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

// Check notifications on page load only (no auto-polling)
fetchNotifCount();
</script>
@stack('scripts')
</body>
</html>
