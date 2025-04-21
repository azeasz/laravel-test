const BASE_URL = 'http://localhost:8000/api';

export const apiFetch = async (endpoint, options = {}) => {
    const url = `${BASE_URL}${endpoint}`;
    const response = await fetch(url, options);
    return response;
};