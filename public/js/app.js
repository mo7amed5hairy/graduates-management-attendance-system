// Global AJAX setup
const App = {
  init() {
    this.setupAjaxForms();
    this.setupModals();
    this.setupDeleteButtons();
    this.setupPointsForm();
    this.setupUserSearch();
    this.setupFilterForms();
    this.setupImagePreview();
    this.setupSidebar();
  },

  // Show toast notification
  toast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.display = 'block';
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .3s';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  },

  // Show/hide global loader
  showLoader() {
    document.querySelector('.loader-overlay')?.classList.add('active');
  },
  hideLoader() {
    document.querySelector('.loader-overlay')?.classList.remove('active');
  },

  // Set button loading state
  setButtonLoading(btn, loading = true, text = null) {
    if (!btn) return;
    if (loading) {
      btn.dataset.originalText = btn.innerHTML;
      btn.disabled = true;
      btn.classList.add('btn-loading');
    } else {
      btn.disabled = false;
      btn.classList.remove('btn-loading');
      if (text) btn.innerHTML = text;
    }
  },

  // Handle fetch response
  async handleResponse(response) {
    const data = await response.json();
    if (!response.ok) {
      if (response.status === 422 && data.errors) {
        const messages = Object.values(data.errors).flat().join('\n');
        throw new Error(messages);
      }
      throw new Error(data.message || 'حدث خطأ غير متوقع');
    }
    return data;
  },

  // Submit form via AJAX
  async submitForm(form, options = {}) {
    const btn = form.querySelector('[type="submit"]');
    const method = options.method || form.method || 'POST';
    const action = options.action || form.action;
    const hasFile = form.querySelector('input[type="file"]');
    let progressEl, progressFill, progressText, progressAnim;

    if (hasFile) {
      progressEl = form.querySelector('.progress-bar-wrap');
      if (!progressEl) {
        progressEl = document.createElement('div');
        progressEl.className = 'progress-bar-wrap';
        progressEl.style.cssText = 'margin-top:.75rem;display:none';
        progressEl.innerHTML = '<div class="progress-bar-fill" style="width:0%"></div><span class="progress-text">0%</span>';
        const btnContainer = form.querySelector('.flex.gap-2, .flex-wrap') || form;
        btnContainer.parentNode.insertBefore(progressEl, btnContainer.nextSibling);
      }
      progressFill = progressEl.querySelector('.progress-bar-fill');
      progressText = progressEl.querySelector('.progress-text');
      progressEl.style.display = 'block';
      progressFill.style.width = '0%';
      progressText.textContent = '0%';

      // Simulated smooth progress up to 90%
      let pct = 0;
      progressAnim = setInterval(() => {
        if (pct < 90) {
          pct += Math.random() * 15 + 2;
          if (pct > 90) pct = 90;
          progressFill.style.width = pct + '%';
          progressText.textContent = Math.round(pct) + '%';
        }
      }, 400);
    }

    this.setButtonLoading(btn, true);
    progressText && (progressText.textContent = '...');

    try {
      const formData = new FormData(form);
      if (options.method === 'PUT' || options.method === 'DELETE') {
        formData.append('_method', options.method);
      }

      const response = await fetch(action, {
        method: method.toUpperCase(),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: formData
      });

      const data = await this.handleResponse(response);

      // Complete progress bar
      if (progressAnim) clearInterval(progressAnim);
      if (progressFill) {
        progressFill.style.width = '100%';
        if (progressText) progressText.textContent = '✅ تم';
      }

      this.toast(data.message, 'success');

      if (data.redirect) {
        setTimeout(() => window.location.href = data.redirect, 500);
        return data;
      }

      if (options.onSuccess) {
        options.onSuccess(data);
      } else if (form.closest('.modal-overlay')) {
        setTimeout(() => {
          if (progressEl) progressEl.style.display = 'none';
          window.location.reload();
        }, 1000);
      }

      return data;
    } catch (error) {
      this.toast(error.message, 'error');
      if (progressEl) progressEl.style.display = 'none';
    } finally {
      this.setButtonLoading(btn, false);
      if (progressAnim) clearInterval(progressAnim);
    }
  },

  // Setup AJAX forms
  setupAjaxForms() {
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        await this.submitForm(form);
      });
    });
  },

  // Setup modal functionality
  setupModals() {
    document.addEventListener('click', (e) => {
      const modalTrigger = e.target.closest('[data-modal]');
      if (modalTrigger) {
        const modalId = modalTrigger.dataset.modal;
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
      }

      if (e.target.closest('.modal-overlay') && !e.target.closest('.modal-content')) {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
      }
    });

    document.querySelectorAll('[data-modal-close]').forEach(el => {
      el.addEventListener('click', () => {
        el.closest('.modal-overlay')?.classList.remove('active');
      });
    });
  },

  // Setup delete buttons
  setupDeleteButtons() {
    document.querySelectorAll('[data-delete]').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const url = btn.dataset.delete;
        const name = btn.dataset.name || 'هذا العنصر';

        if (!confirm(`هل أنت متأكد من حذف ${name}؟`)) return;

        App.setButtonLoading(btn, true);

        try {
          const response = await fetch(url, {
            method: 'DELETE',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
          });

          const data = await App.handleResponse(response);
          App.toast(data.message, 'success');
          btn.closest('tr')?.remove();
          if (data.redirect) setTimeout(() => window.location.href = data.redirect, 500);
        } catch (error) {
          App.toast(error.message, 'error');
        } finally {
          App.setButtonLoading(btn, false);
        }
      });
    });
  },

  // Setup points form
  setupPointsForm() {
    const form = document.getElementById('pointsForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const data = await App.submitForm(form, {
        onSuccess: (response) => {
          document.getElementById('currentPoints').textContent = response.data.user_points;
          form.reset();
          // Reload the page to show updated transaction list
          setTimeout(() => window.location.reload(), 1000);
        }
      });
    });

    // User select change to show current points
    const userSelect = form.querySelector('[name="user_id"]');
    if (userSelect) {
      userSelect.addEventListener('change', async () => {
        const userId = userSelect.value;
        if (!userId) return;

        try {
          const response = await fetch(`/admin/points/user/${userId}/points`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          const data = await response.json();
          document.getElementById('currentPoints').textContent = data.data.points;
        } catch (e) {
          // ignore
        }
      });
    }
  },

  // Setup user search
  setupUserSearch() {
    const searchInput = document.getElementById('userSearch');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase();
      document.querySelectorAll('table.data tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  },

  // Setup filter forms
  setupFilterForms() {
    document.querySelectorAll('[data-filter]').forEach(select => {
      select.addEventListener('change', () => {
        select.closest('form')?.submit();
      });
    });
  },

  // Setup image preview
  setupImagePreview() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
      input.addEventListener('change', () => {
        const preview = document.getElementById(input.dataset.preview);
        if (preview && input.files?.[0]) {
          const reader = new FileReader();
          reader.onload = (e) => { preview.src = e.target.result; };
          reader.readAsDataURL(input.files[0]);
        }
      });
    });
  },

  setupSidebar() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!toggle || !sidebar || !overlay) return;

    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => App.init());
