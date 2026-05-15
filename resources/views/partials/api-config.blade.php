<script>
    (function() {
        const hostname = window.location.hostname;
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';
        
        // Prioritize local URL if on localhost, otherwise use the deployed Render URL
        window.API_BASE_URL = isLocal 
            ? 'http://192.168.55.107:8000/api' 
            : 'https://pccivalph.onrender.com/api';

        console.log("🔌 API Mode:", isLocal ? "LOCAL" : "PRODUCTION");
        console.log("🌐 URL:", window.API_BASE_URL);
    })();
</script>