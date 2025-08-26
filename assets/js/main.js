document.addEventListener('DOMContentLoaded', function() {
    
    
    // --- NEW: Real-Time Clock Logic ---
    const clockElement = document.getElementById('realtime-clock');

    const updateClock = () => {
        const now = new Date();
        // Options to format the date and time for India
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true, // Use AM/PM
            timeZone: 'Asia/Kolkata'
        };
        clockElement.textContent = now.toLocaleString('en-IN', options);
    };

    // Update the clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
    // --- End of Clock Logic ---

    
    // DOM Elements
    const noticeContainer = document.getElementById('notice-container');
    const categoryFiltersContainer = document.getElementById('category-filters');
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    const paginationContainer = document.getElementById('pagination-container');

    // State to hold the current filters, now including page number
    let currentFilters = {
        category: 'all',
        search: '',
        sort: 'latest',
        page: 1
    };

    // --- Main Function to Fetch and Render ---
    const fetchAndRenderNotices = () => {
        const params = new URLSearchParams(currentFilters).toString();
        showLoadingSpinner();

        fetch(`api/get_notices.php?${params}`)
            .then(response => response.json())
            .then(data => {
                renderNotices(data.notices);
                renderPagination(data.pagination);
                // Only render categories on the very first load
                if (currentFilters.page === 1 && currentFilters.category === 'all' && currentFilters.search === '') {
                   renderCategoryButtons(data.categories);
                }
            })
            .catch(error => {
                console.error('Error fetching notices:', error);
                noticeContainer.innerHTML = '<p class="text-danger text-center">Could not load notices.</p>';
            });
    };

    const showLoadingSpinner = () => {
        noticeContainer.innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;
        paginationContainer.innerHTML = '';
    };
    
    // --- Render Functions ---
    const renderNotices = (notices) => {
        noticeContainer.innerHTML = '';
        if (notices.length === 0) {
            noticeContainer.innerHTML = '<p class="text-center text-muted">No notices match your criteria.</p>';
            return;
        }
        notices.forEach(notice => {
            const noticeDate = new Date(notice.created_at).toLocaleString('en-IN', { dateStyle: 'long', timeStyle: 'short' });
            const noticeCol = document.createElement('div');
            noticeCol.className = 'col-lg-12';
            let attachmentHTML = notice.attachment_path ? `<a href="${notice.attachment_path}" target="_blank" class="attachment-link"><i class="bi bi-paperclip"></i> View Attachment</a>` : '';
            noticeCol.innerHTML = `
                <div class="card notice-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">${notice.title}</h5>
                            <span class="badge bg-primary text-white">${notice.category_name}</span>
                        </div>
                        <p class="card-text meta-info">Posted on: ${noticeDate}</p>
                        <p class="card-text">${notice.content.replace(/\n/g, '<br>')}</p>
                        ${attachmentHTML}
                    </div>
                </div>`;
            noticeContainer.appendChild(noticeCol);
        });
    };

    const renderCategoryButtons = (categories) => {
        categoryFiltersContainer.innerHTML = '<button class="btn btn-outline-primary active" data-category="all">All</button>';
        categories.forEach(category => {
            const button = document.createElement('button');
            button.className = 'btn btn-outline-primary';
            button.dataset.category = category.id;
            button.textContent = category.name;
            categoryFiltersContainer.appendChild(button);
        });
    };

    // --- NEW: Function to Render Pagination Links ---
    const renderPagination = (pagination) => {
        paginationContainer.innerHTML = '';
        if (pagination.total_pages <= 1) return;

        for (let i = 1; i <= pagination.total_pages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.dataset.page = i;
            li.appendChild(a);
            paginationContainer.appendChild(li);
        }
    };

    // --- Event Listeners ---
    const resetToFirstPageAndFetch = () => {
        currentFilters.page = 1;
        fetchAndRenderNotices();
    };

    categoryFiltersContainer.addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') {
            currentFilters.category = e.target.dataset.category;
            categoryFiltersContainer.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            resetToFirstPageAndFetch();
        }
    });

    searchInput.addEventListener('input', () => {
        currentFilters.search = searchInput.value;
        resetToFirstPageAndFetch();
    });

    sortSelect.addEventListener('change', () => {
        currentFilters.sort = sortSelect.value;
        resetToFirstPageAndFetch();
    });
    
    // NEW: Event listener for pagination clicks
    paginationContainer.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            currentFilters.page = parseInt(e.target.dataset.page, 10);
            fetchAndRenderNotices();
        }
    });

    // --- Initial Load ---
    fetchAndRenderNotices();
});


// --- Dark Mode Logic ---
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Function to set the theme
const setTheme = (theme) => {
    if (theme === 'dark') {
        body.classList.add('dark-mode');
        darkModeToggle.checked = true;
        localStorage.setItem('theme', 'dark');
    } else {
        body.classList.remove('dark-mode');
        darkModeToggle.checked = false;
        localStorage.setItem('theme', 'light');
    }
};

// Event listener for the toggle
darkModeToggle.addEventListener('change', () => {
    if (darkModeToggle.checked) {
        setTheme('dark');
    } else {
        setTheme('light');
    }
});

// Check for saved theme in localStorage on page load
const savedTheme = localStorage.getItem('theme') || 'light';
setTheme(savedTheme);