const getBaseUrl = () => {
    const hostname = window.location.hostname;
    
    // Check if we are running locally
    if (hostname === 'localhost' || hostname === '127.0.0.1') {
        return 'http://127.0.0.1:8000/api';
    }
    
    // Default to Production Render URL
    return 'https://pccivalph.onrender.com/api';
};

window.API_BASE_URL = getBaseUrl();
console.log("🚀 API connected to:", window.API_BASE_URL);