<script>
    (function() {
        const hostname = window.location.hostname;
<<<<<<< HEAD
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '192.168.55.107';
        window.API_BASE_URL = isLocal ? 'http://127.0.0.1:8000/api' : 'https://pccivalph.onrender.com/api';
=======
        const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';
        
        // Prioritize local URL if on localhost, otherwise use the deployed Render URL
        window.API_BASE_URL = isLocal 
            ? 'http://192.168.55.107:8000/api' 
            : 'https://pccivalph.onrender.com/api';
>>>>>>> 89bb8ef0376c1d22657d97cde9d41a31013bc9d4

        console.log("🔌 API URL:", window.API_BASE_URL);
    })();
</script>