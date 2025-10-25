  @extends('frontend.layouts.app')

  @section('title')
  <title>PCOFlow | Contact</title>
@endsection

  @section('css')
      <style>
          body { background: #fff; }
          .main-section-background { background: transparent !important; min-height: auto !important; }

          /* Match header layout from landing page */
          .header { padding: 15px 0; position: relative; z-index: 10; }
          .navbar { padding: 0; }
          .navbar-brand img { width: 112px; height: auto; }
          .main-menubar { display: flex; align-items: center; justify-content: center; width: 100%; position: relative; }
          .navbar-brand { position: absolute; left: 0; }
          .navbar-nav { display: flex; align-items: center; margin: 0; flex-direction: row; position: absolute; left: 50%; transform: translateX(-50%); }
          .navbar-collapse { flex-grow: 0; }
          .navbar-nav .nav-item { margin: 0 15px; }
          .auth-buttons { display: flex; align-items: center; gap: 12px; margin-left: auto; }

          /* Mobile Login Button Styling (match home page) */
          .mobile-login-btn {
              background: linear-gradient(to right, #80D7DF, #BDEFCC) !important;
              color: white !important;
              border: none !important;
              padding: 8px 20px !important;
              border-radius: 25px !important;
              font-weight: 600 !important;
              font-size: 14px !important;
              box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
              transition: all 0.3s ease !important;
              white-space: nowrap !important;
          }
          .mobile-login-btn:hover {
              background: linear-gradient(to right, #BDEFCC, #80D7DF) !important;
              transform: translateY(-2px) !important;
              box-shadow: 0 6px 18px rgba(0,0,0,0.2) !important;
          }
          .login-btn-res { display: flex !important; align-items: center !important; margin-right: 15px !important; }

          /* Contact page header background bar */
          .contact-header-bar { position: absolute; top: 0; left: 0; right: 0; height: 170px; background: #032127; z-index: 5; padding-bottom: 30px; }

          /* Contact page layout */
          .contact-wrapper { padding-top: 32px; padding-bottom: 48px; }
          .contact-container { max-width: 1140px; margin: 0 auto; padding: 0 16px; }
          .contact-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
          @media (min-width: 992px) { .contact-grid { grid-template-columns: 1fr 1fr; gap: 32px; } }

          .contact-title { font-size: 40px; line-height: 1.2; font-weight: 800; color: #111; margin: 8px 0 10px 0; }
          .contact-sub { font-size: 14px; color: #6b7280; max-width: 560px; }
          .assist-title { font-size: 36px; line-height: 1.2; font-weight: 800; color: #111; margin: 24px 0 8px 0; }
          .assist-copy { font-size: 14px; color: #6b7280; max-width: 560px; }

          .contact-list { margin-top: 16px; margin-bottom: 16px; }
          .contact-label { font-weight: 700; color: #111; }
          .contact-text { color: #111; }

          .img-placeholder { width: 100%; aspect-ratio: 16/9; background: #e5e7eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-weight: 600; margin-top: 16px; }

          .contact-card { background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); padding: 24px; position: sticky; top: 96px; }
          .card-title { font-size: 28px; font-weight: 800; color: #111; margin-bottom: 16px; }
          .form-label-plain { font-size: 12px; color: #6b7280; margin-bottom: 6px; display: inline-block; }
          .input, .select, .textarea { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; background: #fff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.03); }
          .textarea { min-height: 140px; resize: vertical; }
          .submit-btn { width: 100%; height: 36px; border-radius: 8px; background: #000; color: #fff; border: none; font-weight: 600; }
          .submit-btn:hover { background: #111; }
          .field { margin-bottom: 12px; }

          /* Responsive (match home page mobile overrides) */
          @media (max-width: 768px) {
              .navbar-brand img { width: 140px !important; height: auto !important; }
              .main-menubar { justify-content: space-between; padding: 0 12px; }
              .res-collapse { margin-left: auto; display: flex; align-items: center; gap: 8px; }
              .navbar { padding: 0 0 8px 0; }
              .header { padding-top: 70px !important; }

              /* Mobile auth buttons style override (thin white outline) */
              .mobile-login-btn,
              .res-collapse .btn {
                  background: transparent !important;
                  color: #EAF3F4 !important;
                  border: 1px solid rgba(234,243,244,0.25) !important;
                  padding: 8px 14px !important;
                  border-radius: 999px !important;
                  font-weight: 600 !important;
                  font-size: 13px !important;
                  height: 36px !important;
                  display: flex !important;
                  align-items: center !important;
              }
              .mobile-login-btn { padding: 6px 16px !important; font-size: 13px !important; margin-right: 0 !important; }
              .login-btn-res { margin-right: 6px !important; }
          }
      </style>
  @endsection

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      var collapseEl = document.getElementById('navbarsExample09');
      if (!collapseEl) return;
      var closeBtn = collapseEl.querySelector('.mobile-menu-close');
      var togglers = document.querySelectorAll('[data-bs-target="#navbarsExample09"]');

      function closeMenu() {
          try {
              if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                  var instance = bootstrap.Collapse.getInstance(collapseEl);
                  if (!instance) { instance = new bootstrap.Collapse(collapseEl, { toggle: false }); }
                  instance.hide();
              }
          } catch(e) {}
          collapseEl.classList.remove('show');
          collapseEl.style.display = 'none';
          // No body scroll locking for dropdown
          // sync toggler button state
          togglers.forEach(function(btn){
              btn.classList.add('collapsed');
              btn.setAttribute('aria-expanded', 'false');
          });
      }
      var links = collapseEl.querySelectorAll('.nav-link');
      links.forEach(function(link) {
          link.addEventListener('click', function() {
              closeMenu();
          });
      });

      // Fallback toggle when Bootstrap JS isn't loaded
      togglers.forEach(function(btn){
          btn.addEventListener('click', function(e){
              if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) return; // let Bootstrap handle
              e.preventDefault();
              if (collapseEl.classList.contains('show')) {
                  closeMenu();
              } else {
                  collapseEl.classList.add('show');
                  collapseEl.style.display = 'block';
                  // No body scroll locking for dropdown
                  btn.classList.remove('collapsed');
                  btn.setAttribute('aria-expanded', 'true');
              }
          });
      });

      if (closeBtn) {
          closeBtn.addEventListener('click', closeMenu);
      }

      // Expose a global fallback so inline onclick can call it
      window.closeMobileMenu = closeMenu;

      // Also close on Escape key
      document.addEventListener('keydown', function(ev){
          if (ev.key === 'Escape' && collapseEl.classList.contains('show')) {
              closeMenu();
          }
      });
  });
  </script>
  @endpush

  @section('content')
      <div class="contact-header-bar" aria-hidden="true"></div>
      <section class="contact-wrapper">
        <div class="contact-container">
          <div class="contact-grid">
            <div>
              <div style="display:flex; align-items:center; gap:8px; color:#0f172a; font-weight:700;">
                <span style="display:inline-flex; width:18px; height:18px; border-radius:9999px; background:#e5e7eb; align-items:center; justify-content:center;">★</span>
                <span>Contact Us</span>
              </div>
              <h1 class="contact-title">Get in Touch with Us</h1>
              <p class="contact-sub">Whether you have questions, need assistance, or want to explore how PCO Flow can elevate your business, we’re here to help.</p>

              <h2 class="assist-title">Need Technical<br>Assistance?</h2>
              <p class="assist-copy">Our support team is available 24/7 to assist with your questions and troubleshoot any issues.</p>

              <div class="contact-list">
                <div style="margin-bottom:12px;">
                  <div class="contact-label">Email:</div>
                  <div class="contact-text">support@pcoflow.com</div>
                </div>
                <div>
                  <div class="contact-label">Whatsapp:</div>
                  <div class="contact-text">+44 7566 261618</div>
                </div>
              </div>

              <div class="img-placeholder">
                <img src="{{ asset('assets/ss11.png') }}" alt="Contact Us" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
              </div>
            </div>

            <div>
              <div class="contact-card">
                <div class="card-title">Send us a message</div>
                <form action="{{ route('user.enquiry') }}" method="POST">
                  {{ csrf_field() }}
                  <div class="field">
                    <label class="form-label-plain">Name</label>
                    <input class="input" type="text" name="name" required>
                  </div>
                  <div class="field">
                    <label class="form-label-plain">Email</label>
                    <input class="input" type="email" name="email" required>
                  </div>
                  <div class="field">
                    <label class="form-label-plain">Company Name</label>
                    <input class="input" type="text" name="company" autocomplete="organization">
                  </div>
                  <div class="field">
                    <label class="form-label-plain">Phone Number</label>
                    <input class="input" type="tel" name="phone" autocomplete="tel">
                  </div>
                  <div class="field">
                    <label class="form-label-plain">Fleet Size</label>
                    <select class="select" name="fleet_size">
                      <option value="" selected disabled>Select</option>
                      <option value="1-10">1-10</option>
                      <option value="11-50">11-50</option>
                      <option value="51-200">51-200</option>
                      <option value="200+">200+</option>
                    </select>
                  </div>
                  <div class="field">
                    <label class="form-label-plain">Message</label>
                    <textarea class="textarea" name="message" required></textarea>
                  </div>
                  <button type="submit" class="submit-btn">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
  @endsection
