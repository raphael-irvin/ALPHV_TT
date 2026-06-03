// shared.js

/**
 * 1. Centralized Timestamp Formatter
 * Takes a record object and returns a beautifully formatted date/time string.
 */
function formatRecordTime(record) {
    const dateObj = new Date(record.updated_at || record.created_at);
    return dateObj.toLocaleTimeString('en-GB') + ' ' + dateObj.toISOString().split('T')[0];
}

/**
 * 2. Centralized Fetch Engine
 * Fetches data from the API. Automatically attaches the VIP token if the user is an Admin.
 * The "page = 1" parameter future-proofs this for our pagination!
 */
function getRecordsFromAPI(page = 1) {
    const headers = { 'Accept': 'application/json' };
    
    // If a token exists (Admin is logged in), attach it to the request
    const token = localStorage.getItem('auth_token');
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    return fetch(`http://127.0.0.1:8000/api/records?page=${page}`, { headers })
        .then(response => {
            if (!response.ok) throw new Error("API Request Failed or Unauthorized");
            return response.json();
        });
}