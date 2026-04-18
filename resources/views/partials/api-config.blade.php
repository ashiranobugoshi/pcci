<script>
    window.API_BASE_URL = {{ json_encode(
        in_array(request()->getHost(), ['localhost', '127.0.0.1', '::1'], true)
            ? rtrim(url('/api'), '/')
            : rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/')
    ) }};
</script>