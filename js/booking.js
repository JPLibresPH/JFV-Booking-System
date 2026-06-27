/* ── Photographer Booking System — JS ──────────────────────────────────── */

(function () {
  'use strict';

  /* ── State ────────────────────────────────────────────────────────────── */
  const state = {
    today:        new Date(),
    viewYear:     new Date().getFullYear(),
    viewMonth:    new Date().getMonth(), // 0-indexed
    selectedDate: null, // 'YYYY-MM-DD' string
  };

  /* ── DOM refs ─────────────────────────────────────────────────────────── */
  const calGrid         = document.getElementById('cal-grid');
  const calMonthYear    = document.getElementById('cal-month-year');
  const btnPrev         = document.getElementById('cal-prev');
  const btnNext         = document.getElementById('cal-next');
  const selectedDisplay = document.getElementById('selected-date-display');
  const selectedText    = document.getElementById('selected-date-text');
  const hiddenDate      = document.getElementById('booking-date');
  const dateInput       = document.getElementById('date-display');
  const form            = document.getElementById('booking-form');
  const btnSubmit       = document.getElementById('btn-submit');
  const pkgSelect       = document.getElementById('package');
  const pkgChips        = document.querySelectorAll('.pkg-chip');

  /* ── Available days: 0=Sun, 1=Mon, 6=Sat ─────────────────────────────── */
  const AVAILABLE_DAYS = new Set([0, 1, 6]);

  const MONTH_NAMES = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December',
  ];

  const DAY_SHORT = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

  /* ── Helpers ──────────────────────────────────────────────────────────── */
  function pad(n) { return String(n).padStart(2, '0'); }

  function toYMD(y, m, d) {
    return `${y}-${pad(m + 1)}-${pad(d)}`;
  }

  function formatDisplay(ymd) {
    const [y, m, d] = ymd.split('-').map(Number);
    const dt = new Date(y, m - 1, d);
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    return `${days[dt.getDay()]}, ${MONTH_NAMES[dt.getMonth()]} ${d}, ${y}`;
  }

  function isBefore(y, m, d) {
    const t = state.today;
    return new Date(y, m, d) < new Date(t.getFullYear(), t.getMonth(), t.getDate());
  }

  function isToday(y, m, d) {
    const t = state.today;
    return y === t.getFullYear() && m === t.getMonth() && d === t.getDate();
  }

  /* ── Render calendar ──────────────────────────────────────────────────── */
  function renderCalendar() {
    const { viewYear: yr, viewMonth: mo } = state;
    calMonthYear.textContent = `${MONTH_NAMES[mo]} ${yr}`;

    // Disable prev button if we're already on the current month
    const todayMo = state.today.getMonth();
    const todayYr = state.today.getFullYear();
    btnPrev.disabled = yr === todayYr && mo === todayMo;
    btnPrev.style.opacity = btnPrev.disabled ? '0.3' : '1';

    // Clear grid (keep the 7 day-name headers)
    const headers = [...calGrid.querySelectorAll('.cal-day-name')];
    calGrid.innerHTML = '';
    headers.forEach(h => calGrid.appendChild(h));

    // First day of month (0=Sun)
    const firstDay = new Date(yr, mo, 1).getDay();
    // Days in month
    const daysInMonth = new Date(yr, mo + 1, 0).getDate();

    // Leading empty cells
    for (let i = 0; i < firstDay; i++) {
      const cell = document.createElement('div');
      cell.className = 'cal-cell empty';
      calGrid.appendChild(cell);
    }

    // Day cells
    for (let d = 1; d <= daysInMonth; d++) {
      const cell = document.createElement('div');
      const dow = new Date(yr, mo, d).getDay(); // 0=Sun..6=Sat
      const ymd = toYMD(yr, mo, d);
      const past = isBefore(yr, mo, d);
      const today = isToday(yr, mo, d);
      const avail = AVAILABLE_DAYS.has(dow);

      let cls = 'cal-cell';
      if (today) cls += ' today';

      if (past) {
        cls += ' past';
      } else if (avail) {
        cls += ' available';
        if (state.selectedDate === ymd) cls += ' selected';
      } else {
        cls += ' unavailable';
      }

      cell.className = cls;
      cell.textContent = d;
      cell.dataset.date = ymd;

      if (avail && !past) {
        cell.addEventListener('click', () => selectDate(ymd));
      }

      calGrid.appendChild(cell);
    }
  }

  /* ── Select a date ────────────────────────────────────────────────────── */
  function selectDate(ymd) {
    state.selectedDate = ymd;
    hiddenDate.value = ymd;
    dateInput.value = formatDisplay(ymd);

    const display = formatDisplay(ymd);
    selectedText.textContent = display;
    selectedDisplay.classList.add('visible');

    // Scroll to form smoothly
    document.getElementById('booking-form-section').scrollIntoView({ behavior: 'smooth', block: 'start' });

    renderCalendar(); // re-render to update selected style
    clearFieldError('date-display');
  }

  /* ── Navigation ───────────────────────────────────────────────────────── */
  btnPrev.addEventListener('click', () => {
    if (state.viewMonth === 0) { state.viewMonth = 11; state.viewYear--; }
    else state.viewMonth--;
    renderCalendar();
  });

  btnNext.addEventListener('click', () => {
    if (state.viewMonth === 11) { state.viewMonth = 0; state.viewYear++; }
    else state.viewMonth++;
    renderCalendar();
  });

  /* ── Package chips ─────────────────────────────────────────────────────── */
  pkgChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const val = chip.dataset.package;
      pkgSelect.value = val;
      pkgChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      clearFieldError('package');
    });
  });

  pkgSelect.addEventListener('change', () => {
    const val = pkgSelect.value;
    pkgChips.forEach(c => {
      c.classList.toggle('active', c.dataset.package === val);
    });
    clearFieldError('package');
  });

  /* ── Inline validation helpers ────────────────────────────────────────── */
  function showFieldError(fieldId, msg) {
    const field = document.getElementById(fieldId);
    const errEl = document.getElementById('err-' + fieldId);
    if (field) field.classList.add('error');
    if (errEl) { errEl.textContent = msg; errEl.classList.add('visible'); }
  }

  function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errEl = document.getElementById('err-' + fieldId);
    if (field) field.classList.remove('error');
    if (errEl) errEl.classList.remove('visible');
  }

  function clearAllErrors() {
    document.querySelectorAll('.form-input, .form-select, .form-textarea')
      .forEach(el => el.classList.remove('error'));
    document.querySelectorAll('.field-error')
      .forEach(el => el.classList.remove('visible'));
  }

  /* Live validation on blur */
  ['couple_name','contact','email','location','package'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('blur', () => validateField(id, el.value));
  });

  function validateField(id, value) {
    value = value.trim();
    switch (id) {
      case 'couple_name':
        if (!value) { showFieldError(id, 'Couple name is required.'); return false; }
        if (!/^[A-Za-z\s&]+$/.test(value)) { showFieldError(id, 'Letters and "and" only — no special characters.'); return false; }
        clearFieldError(id); return true;
      case 'contact':
        if (!value) { showFieldError(id, 'Contact number is required.'); return false; }
        if (!/^[0-9+\-\s()]{7,20}$/.test(value)) { showFieldError(id, 'Enter a valid phone number.'); return false; }
        clearFieldError(id); return true;
      case 'email':
        if (!value) { showFieldError(id, 'Email address is required.'); return false; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { showFieldError(id, 'Enter a valid email address.'); return false; }
        clearFieldError(id); return true;
      case 'location':
        if (!value) { showFieldError(id, 'Location of shoot is required.'); return false; }
        clearFieldError(id); return true;
      case 'package':
        if (!value) { showFieldError(id, 'Please select a package.'); return false; }
        clearFieldError(id); return true;
    }
    return true;
  }

  /* ── Toast ─────────────────────────────────────────────────────────────── */
  const toast     = document.getElementById('toast');
  const toastIcon = document.getElementById('toast-icon');
  const toastMsg  = document.getElementById('toast-message');
  let toastTimer;

  function showToast(msg, type = 'success') {
    clearTimeout(toastTimer);
    toastMsg.textContent = msg;
    toastIcon.textContent = type === 'success' ? '✅' : '❌';
    toast.className = `toast ${type}`;
    requestAnimationFrame(() => toast.classList.add('show'));
    toastTimer = setTimeout(() => toast.classList.remove('show'), 5000);
  }

  /* ── Form submission ──────────────────────────────────────────────────── */
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAllErrors();

    // Client-side validation
    const fields = ['couple_name', 'contact', 'email', 'location', 'package'];
    let valid = fields.every(id => {
      const el = document.getElementById(id);
      return validateField(id, el ? el.value : '');
    });

    if (!state.selectedDate) {
      showFieldError('date-display', 'Please select a date from the calendar above.');
      valid = false;
    }

    if (!valid) {
      showToast('Please fix the highlighted fields.', 'error');
      // Scroll to first error
      const firstErr = form.querySelector('.error');
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    // Set loading state
    btnSubmit.disabled = true;
    btnSubmit.classList.add('loading');

    try {
      const formData = new FormData(form);
      const res = await fetch('php/submit.php', {
        method: 'POST',
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        showToast(`🎉 ${data.message} (Booking #${data.couple_id})`, 'success');
        form.reset();
        pkgChips.forEach(c => c.classList.remove('active'));
        state.selectedDate = null;
        hiddenDate.value = '';
        dateInput.value = '';
        selectedDisplay.classList.remove('visible');
        renderCalendar();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        showToast(data.message || 'Something went wrong. Please try again.', 'error');
      }
    } catch (err) {
      showToast('Network error. Please check your connection and try again.', 'error');
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.classList.remove('loading');
    }
  });

  /* ── Init ─────────────────────────────────────────────────────────────── */
  renderCalendar();

})();
