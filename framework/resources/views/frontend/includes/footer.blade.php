<div>
   <footer>
      <div class="container">
         <div class="row">
            <div class="col-md-12">
               <div class="footer-menu" style="text-align: center;">
                  <ul style="display: flex; justify-content: center; align-items: center; gap: 30px; margin: 0; padding: 0; list-style: none;">
                     <li><a href="{{ route('frontend.contact') }}" title="Contact">@lang('frontend.contact')</a></li>
                     <li><a href="{{ route('frontend.about') }}" title="About us">@lang('frontend.about')</a></li>
                     @if (!Auth::guest() && (Auth::user()->user_type == 'C' || Auth::user()->user_type == 'D'))
                     <li><a href="{{ route('frontend.booking_history', Auth::user()->id) }}" title="Booking history">@lang('frontend.booking_history')</a></li>
                     @endif
                  </ul>
               </div>
            </div>
            <div class="col-md-12">
               <div class="copyright">
                  <p>{!! Hyvikk::get('web_footer') !!}</p>
               </div>
            </div>
         </div>
      </div>
   </footer>
</div>