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
 * Fetches a paginated page of records from the API.
 * Automatically attaches the bearer token if the user is logged in as admin.
 *
 * Returns the full Laravel paginator object:
 *   { data: [...], current_page, last_page, total, per_page, from, to }
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

/**
 * 3. Pagination Controls Renderer
 * Renders Previous / page-info / Next controls into a given container element.
 *
 * @param {HTMLElement} container    - The element to render pagination into
 * @param {number}      currentPage  - The active page number
 * @param {number}      lastPage     - The total number of pages
 * @param {number}      total        - The total number of records across all pages
 * @param {Function}    onPageChange - Callback(newPage) invoked when user navigates
 */
function renderPagination(container, currentPage, lastPage, total, onPageChange) {
    // Hide the controls entirely when everything fits on one page
    if (lastPage <= 1) {
        container.innerHTML = '';
        return;
    }

    const prevDisabled = currentPage <= 1;
    const nextDisabled = currentPage >= lastPage;

    // Write the button shell — no inline onclick, handlers are attached below
    container.innerHTML = `
        <div class="pagination">
            <button class="page-btn" id="prevPageBtn" ${prevDisabled ? 'disabled' : ''}>&#8592; Prev</button>
            <span class="page-info">
                Page <strong>${currentPage}</strong> of <strong>${lastPage}</strong>
                <span class="page-total">(${total} total)</span>
            </span>
            <button class="page-btn" id="nextPageBtn" ${nextDisabled ? 'disabled' : ''}>Next &#8594;</button>
        </div>
    `;

    // Attach click handlers via addEventListener so the callback is never serialized to a string
    if (!prevDisabled) {
        document.getElementById('prevPageBtn')
            .addEventListener('click', () => onPageChange(currentPage - 1));
    }
    if (!nextDisabled) {
        document.getElementById('nextPageBtn')
            .addEventListener('click', () => onPageChange(currentPage + 1));
    }
}