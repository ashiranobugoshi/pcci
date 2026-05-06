// This checks your .env for VITE_API_BASE_URL. 
// If it isn't set, it safely falls back to your local environment.
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api';