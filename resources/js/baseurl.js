const isLocalHost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);

export const API_BASE_URL = isLocalHost
	? `${window.location.origin}/api`
	: (import.meta.env.VITE_API_BASE_URL || 'https://pcciv-api.onrender.com/api').replace(/\/$/, '');
