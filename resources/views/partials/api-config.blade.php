<script>
    (function() {
        const hostname = window.location.hostname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';

        // Prioritize local URL if on localhost, otherwise use the deployed Render URL
        window.API_BASE_URL = isLocal ?
            'http://127.0.0.1:8000/api' :
            'https://pccivalph.onrender.com/api';

        console.log("🔌 API URL:", window.API_BASE_URL);
    })();
</script>