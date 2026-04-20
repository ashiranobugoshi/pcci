<script>
    window.API_BASE_URL = @json(rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/'));

    window.PCCI_ENV_DEBUG = {
        app_name: @json(config('app.name')),
        app_env: @json(config('app.env')),
        app_debug: @json((bool) config('app.debug')),
        app_url: @json(config('app.url')),
        pcci_api_base_url: @json(rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/')),
    };

    if (window.PCCI_ENV_DEBUG.app_debug) {
        console.log('[PCCI ENV DEBUG]', window.PCCI_ENV_DEBUG);
    }
</script>