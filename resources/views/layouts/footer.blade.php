<footer class="container-fluid p-3 {{ (isset($fixed_footer) && $fixed_footer) ? "fixed-bottom" : "" }}">
    <span>© {{ date("Y") }}-{{ date("Y", strtotime("+1 year")) }} {{ config('app.company') }}</span>
</footer>
<script src="{{ asset('plugins/jquery/jquery-3.3.1.slim.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/side_nav.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>