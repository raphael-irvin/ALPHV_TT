// shared.js

// ── API Base URL ──────────────────────────────────────────────────
// For local development:  'http://127.0.0.1:8000'
// For production:         '' (empty string = same-origin, since Nginx proxies /api)
const API_BASE_URL = '';
// ──────────────────────────────────────────────────────────────────

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
 * Fetches a paginated, sorted page of records from the API.
 * Automatically attaches the bearer token if the user is logged in as admin.
 *
 * @param {number} page    - Page number (default: 1)
 * @param {string} sortBy  - Column to sort by: name | shape | color | updated_at
 * @param {string} sortDir - Sort direction: asc | desc
 *
 * Returns the full Laravel paginator object:
 *   { data: [...], current_page, last_page, total, per_page, from, to }
 */
function getRecordsFromAPI(page = 1, sortBy = 'updated_at', sortDir = 'desc') {
    const headers = { 'Accept': 'application/json' };

    // If a token exists (Admin is logged in), attach it to the request
    const token = localStorage.getItem('auth_token');
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const url = `${API_BASE_URL}/api/records?page=${page}&sort_by=${sortBy}&sort_dir=${sortDir}`;

    return fetch(url, { headers })
        .then(response => {
            if (!response.ok) throw new Error("API Request Failed or Unauthorized");
            return response.json();
        });
}

/**
 * 3. Sortable Header Renderer
 * Writes clickable <th> elements into a <thead><tr> element.
 * Shows a ↑ or ↓ indicator on the currently active column, and ↕ on inactive sortable columns.
 *
 * @param {HTMLElement} theadRow - The <tr> inside <thead> to populate
 * @param {Array}       columns  - Array of { label, key, style? }.
 *                                 key=null means the column is not sortable.
 * @param {string}      sortBy   - Currently active sort column key
 * @param {string}      sortDir  - Currently active sort direction ('asc' | 'desc')
 * @param {Function}    onSort   - Callback(newSortBy, newSortDir) when a header is clicked
 */
function renderSortHeaders(theadRow, columns, sortBy, sortDir, onSort) {
    theadRow.innerHTML = '';

    columns.forEach(col => {
        const th = document.createElement('th');

        if (!col.key) {
            // Non-sortable column (e.g. Shape & Color, Actions)
            th.textContent = col.label;
            if (col.style) th.setAttribute('style', col.style);
        } else {
            const isActive  = sortBy === col.key;
            const nextDir   = isActive && sortDir === 'asc' ? 'desc' : 'asc';
            const indicator = isActive ? (sortDir === 'asc' ? ' ↑' : ' ↓') : ' ↕';

            th.innerHTML = `${col.label}<span class="sort-indicator">${indicator}</span>`;
            th.className = 'sortable' + (isActive ? ' sort-active' : '');
            th.addEventListener('click', () => onSort(col.key, nextDir));
        }

        theadRow.appendChild(th);
    });
}

/**
 * 4. Pagination Controls Renderer
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