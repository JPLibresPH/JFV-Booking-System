<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book a Session — Photographer Booking</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

<!-- ── Site Header ──────────────────────────────────────────────────────── -->
<header class="site-header">
  <p class="header-eyebrow">Photography Studio · Booking System</p>
  <h1 class="header-title">Reserve Your <em>Moment</em></h1>
  <p class="header-subtitle">
    Choose your preferred session date and fill in your details below.
    We'll confirm your booking within 24 hours.
  </p>
  <div class="header-divider"></div>
</header>

<!-- ── Main Content ─────────────────────────────────────────────────────── -->
<main class="page-wrap">

  <!-- ── Section: Calendar ──────────────────────────────────────────────── -->
  <section aria-labelledby="cal-section-title" style="margin-bottom: 0;">
    <p class="section-label">Step 1</p>
    <h2 class="section-title" id="cal-section-title">Pick Your Date</h2>
    <p class="section-desc">
      Available shoot days are highlighted in gold. Click any available date to select it.
    </p>

    <!-- Availability note -->
    <div class="avail-note">
      <span class="avail-note-icon">📅</span>
      <p class="avail-note-text">
        <strong>Available days: Sunday, Monday &amp; Saturday.</strong>
        Tuesday to Friday are reserved for post-processing and editing.
        If you need a weekday session (Tue–Fri), please reach out to the photographer directly —
        <strong>weekday bookings are negotiable with an additional fee.</strong>
      </p>
    </div>

    <!-- Calendar card -->
    <div class="calendar-card">
      <!-- Calendar header -->
      <div class="cal-header">
        <button class="cal-nav-btn" id="cal-prev" aria-label="Previous month">&#8592;</button>
        <span class="cal-month-year" id="cal-month-year"></span>
        <button class="cal-nav-btn" id="cal-next" aria-label="Next month">&#8594;</button>
      </div>

      <!-- Day-name row + day cells -->
      <div class="cal-grid" id="cal-grid">
        <!-- Day names -->
        <?php
          $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
          $available = [0, 1, 6]; // Sun=0, Mon=1, Sat=6
          foreach ($days as $i => $day):
            $cls = in_array($i, $available) ? 'cal-day-name available-header' : 'cal-day-name';
        ?>
        <div class="<?= $cls ?>"><?= $day ?></div>
        <?php endforeach; ?>
        <!-- Day cells rendered by JS -->
      </div>

      <!-- Legend -->
      <div class="cal-legend">
        <div class="legend-item">
          <span class="legend-dot available"></span>
          Available (Sun / Mon / Sat)
        </div>
        <div class="legend-item">
          <span class="legend-dot selected"></span>
          Selected date
        </div>
        <div class="legend-item">
          <span class="legend-dot unavailable"></span>
          Editing days (Tue – Fri)
        </div>
      </div>
    </div>

    <!-- Selected date pill -->
    <div class="selected-date-display" id="selected-date-display">
      <span class="date-icon">✦</span>
      <span>Selected: <strong id="selected-date-text"></strong></span>
    </div>
  </section>

  <!-- spacer -->
  <div style="height: 48px;"></div>

  <!-- ── Section: Booking Form ──────────────────────────────────────────── -->
  <section id="booking-form-section" aria-labelledby="form-section-title">
    <p class="section-label">Step 2</p>
    <h2 class="section-title" id="form-section-title">Your Details</h2>
    <p class="section-desc">
      Fill in the form below and we'll send you a confirmation. Fields marked
      <span style="color:var(--gold);">✦</span> are required.
    </p>

    <div class="form-card">
      <!-- Form card header -->
      <div class="form-card-header">
        <p style="font-family:var(--font-display);font-size:20px;font-weight:400;color:var(--text-primary);">
          Booking Request Form
        </p>
        <p style="font-size:12px;color:var(--text-muted);margin-top:4px;">
          All information is kept private and used solely for your booking.
        </p>
      </div>

      <!-- The form -->
      <form id="booking-form" novalidate>
        <div class="form-body">

          <!-- Row 1: Couple name + Contact -->
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="couple_name">
                Couple Name <span class="required">✦</span>
              </label>
              <input
                class="form-input"
                type="text"
                id="couple_name"
                name="couple_name"
                placeholder="e.g. Jahred and Frederick"
                maxlength="150"
                autocomplete="off"
              />
              <span id="err-couple_name" class="field-error"></span>
              <span class="form-hint">Letters and "and" only — no special characters.</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="contact">
                Contact Number <span class="required">✦</span>
              </label>
              <input
                class="form-input"
                type="tel"
                id="contact"
                name="contact"
                placeholder="e.g. +63 912 345 6789"
                maxlength="20"
                autocomplete="tel"
              />
              <span id="err-contact" class="field-error"></span>
            </div>
          </div>

          <!-- Row 2: Email + Location -->
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="email">
                Email Address <span class="required">✦</span>
              </label>
              <input
                class="form-input"
                type="email"
                id="email"
                name="email"
                placeholder="yourname@email.com"
                autocomplete="email"
              />
              <span id="err-email" class="field-error"></span>
            </div>

            <div class="form-group">
              <label class="form-label" for="location">
                Location of Shoot <span class="required">✦</span>
              </label>
              <input
                class="form-input"
                type="text"
                id="location"
                name="location"
                placeholder="e.g. Rizal Park, Manila"
                maxlength="255"
              />
              <span id="err-location" class="field-error"></span>
            </div>
          </div>

          <!-- Row 3: Package -->
          <div class="form-row single">
            <div class="form-group">
              <label class="form-label" for="package">
                Package <span class="required">✦</span>
              </label>

              <!-- Visual chips -->
              <div class="package-hint">
                <div class="pkg-chip" data-package="Pilot">
                  <span class="pkg-icon">✈</span>
                  Pilot
                </div>
                <div class="pkg-chip" data-package="Mainstream">
                  <span class="pkg-icon">🎬</span>
                  Mainstream
                </div>
                <div class="pkg-chip" data-package="Blockbuster">
                  <span class="pkg-icon">⭐</span>
                  Blockbuster
                </div>
                <div class="pkg-chip" data-package="Travel">
                  <span class="pkg-icon">🌍</span>
                  Travel
                </div>
              </div>

              <select class="form-select" id="package" name="package" style="margin-top:10px;">
                <option value="" disabled selected>Select a package…</option>
                <option value="Pilot">Pilot</option>
                <option value="Mainstream">Mainstream</option>
                <option value="Blockbuster">Blockbuster</option>
                <option value="Travel">Travel</option>
              </select>
              <span id="err-package" class="field-error"></span>
            </div>
          </div>

          <!-- Row 4: Story / Notes -->
          <div class="form-row single">
            <div class="form-group">
              <label class="form-label" for="story_notes">
                Your Story &amp; Notes
              </label>
              <textarea
                class="form-textarea"
                id="story_notes"
                name="story_notes"
                placeholder="Share a brief story about how you met, your vision for the shoot, any special requests, preferred mood or style, props you'd like to bring — anything that helps us capture your moment perfectly."
                maxlength="2000"
              ></textarea>
              <span class="form-hint">
                Optional but encouraged — the more we know, the better we can prepare. (Max 2000 characters)
              </span>
            </div>
          </div>

          <!-- Row 5: Session Date (from calendar) -->
          <div class="form-row single">
            <div class="form-group">
              <label class="form-label" for="date-display">
                Session Date <span class="required">✦</span>
              </label>
              <div class="date-field-wrap">
                <input
                  class="form-input"
                  type="text"
                  id="date-display"
                  placeholder="← Select a date from the calendar above"
                  readonly
                  style="cursor:default;"
                />
                <!-- Hidden field that actually holds the YYYY-MM-DD value -->
                <input type="hidden" id="booking-date" name="date" />
              </div>
              <span id="err-date-display" class="field-error"></span>
              <span class="form-hint">
                Date is set automatically when you click a day on the calendar.
                Need a weekday? Contact us for negotiated availability.
              </span>
            </div>
          </div>

        </div><!-- /form-body -->

        <!-- Form footer -->
        <div class="form-footer">
          <p class="form-footer-note">
            By submitting this form you agree to be contacted regarding your
            booking. We'll respond within 24 hours to confirm availability.
          </p>
          <button class="btn-submit" type="submit" id="btn-submit">
            <span class="btn-spinner"></span>
            <span class="btn-text">✦ &nbsp;Confirm Booking</span>
          </button>
        </div>

      </form>
    </div><!-- /form-card -->
  </section>

</main>

<!-- ── Toast notification ────────────────────────────────────────────────── -->
<div class="toast" id="toast" role="alert" aria-live="polite">
  <span class="toast-icon" id="toast-icon">✅</span>
  <span id="toast-message">Booking confirmed!</span>
</div>

<!-- ── Site Footer ───────────────────────────────────────────────────────── -->
<footer class="site-footer">
  <span>✦</span> &nbsp;Photographer Booking System &nbsp;<span>✦</span>
  &nbsp;·&nbsp; All rights reserved.
</footer>

<script src="js/booking.js"></script>


Hi jahred
Zapppbrooo
</body>
</html>
<!--  -->