<div>
   <footer style="background:#FFFFFF; color:#2B3A3E; padding:28px 0 20px 0; border-top:1px solid #E6ECEE;">
      <div class="container">
         <div class="row align-items-start" style="padding-bottom:16px;">
            <div class="col-lg-6 d-flex align-items-center" style="gap:12px;">
               <img src="{{ asset('assets/images/footer_logo.png') }}" alt="pco flow" style="height:22px; width:auto; object-fit:contain;" onerror="this.src='{{ asset('assets/images/pco-flow-logo.png') }}'">
               <span style="font-size:11px; color:#7A8C91;">Empower your business with PCO Flow</span>
            </div>
            <div class="col-lg-6">
               <div class="row" style="font-size:12px; color:#6A7C80;">
                  <div class="col-4">
                     <div style="font-weight:700; color:#2B3A3E; margin-bottom:8px;">Features</div>
                     <div>Core features</div>
                     <div>Pro experience</div>
                     <div>Integrations</div>
                  </div>
                  <div class="col-4">
                     <div style="font-weight:700; color:#2B3A3E; margin-bottom:8px;">Learn more</div>
                     <div>Blog</div>
                     <div>Case studies</div>
                     <div>Customer stories</div>
                     <div>Best practices</div>
                  </div>
                  <div class="col-4">
                     <div style="font-weight:700; color:#2B3A3E; margin-bottom:8px;">Support</div>
                     <div><a href="{{ route('frontend.contact') }}" style="color:#6A7C80; text-decoration:none;">Contact</a></div>
                     <div>Support</div>
                     <div>Legal</div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12 d-flex justify-content-between" style="font-size:12px; color:#98A7AC;">
               <p style="margin:0;">&copy; {{ date('Y') }} PCO Flow.</p>
               <p style="margin:0;"></p>
            </div>
         </div>
      </div>
   </footer>
</div>