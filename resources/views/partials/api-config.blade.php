<script>
    window.API_BASE_URL = @json(rtrim(config('services.pcci_api.base_url', 'https://pccivalph.onrender.com/api'), '/'));
    window.PCCI_API_BASE_URL = window.API_BASE_URL;

    // Backward-compatible global for scripts using `${PCCI_API_BASE_URL}` directly.
    if (typeof PCCI_API_BASE_URL === 'undefined') {
        var PCCI_API_BASE_URL = window.API_BASE_URL;
    }

    window.PCCI_ENV_DEBUG = {
        app_name: @json(config('app.name')),
        app_env: @json(config('app.env')),
        app_debug: @json((bool) config('app.debug')),
        app_url: @json(config('app.url')),
        pcci_api_base_url: @json(rtrim(config('services.pcci_api.base_url', 'https://pccivalph.onrender.com/api'), '/')),
    };

    if (window.PCCI_ENV_DEBUG.app_debug) {
        console.log('[PCCI ENV DEBUG]', window.PCCI_ENV_DEBUG);
    }
</script>