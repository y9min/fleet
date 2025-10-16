@if (!Auth::guest() && (Auth::user()->user_type == 'D' ))
<footer class="footer pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Footer content removed -->
            </div>
        </div>
    </div>
</footer>
@else
<footer class="dark-footer res-footer footer pb-2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Footer content removed -->
            </div>
        </div>
    </div>
</footer>
@endif
