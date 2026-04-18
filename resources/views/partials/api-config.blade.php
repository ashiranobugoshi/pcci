<script>
    window.API_BASE_URL = @json(app()->environment('local') ? url('/api') : config('services.pcci_api.base_url', 'https://pcci-laravel-api.onrender.com/api'));
</script>