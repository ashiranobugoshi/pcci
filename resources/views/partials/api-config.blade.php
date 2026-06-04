<script>
    (function() {
        const hostname = window.location.hostname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';

        window.API_BASE_URL = isLocal ?
            'http://127.0.0.1:8000/api' :
            '/api';
        window.PCCI_API_BASE_URL = window.API_BASE_URL;
        console.log("🔌 API URL:", window.API_BASE_URL);
    })();
</script>