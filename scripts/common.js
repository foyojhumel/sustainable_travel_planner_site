// Shared across multiple pages

// Helper: Escape HTML special characters to prevent XSS
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Helper: Show suggestions dropdown
function initSearchSuggestions(searchInputId, suggestionsDropdownId, buttonId = null) {
    const searchInput = document.getElementById(searchInputId);
    const suggestionsDropdown = document.getElementById(suggestionsDropdownId);
    const searchButton = buttonId ? document.getElementById(buttonId) : null;

    if (!searchInput || !suggestionsDropdown) return;

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        if (query.length < 2) {
            suggestionsDropdown.classList.add('hidden');
            return;
        }
        debounceTimer = setTimeout(() => fetchSuggestions(query, suggestionsDropdown), 300);
    });

    async function fetchSuggestions(query, dropdown) {
        try {
            const response = await fetch(`php/searchSuggestions.php?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            if (!suggestions.length) {
                dropdown.classList.add('hidden');
                return;
            }
            dropdown.innerHTML = suggestions.map(s => `
                <div class="px-4 py-3 hover:bg-surface-container-high cursor-pointer border-b border-outline-variant/20" data-province-id="${s.province_id}">
                    ${escapeHtml(s.label)}
                </div>
            `).join('');
            dropdown.classList.remove('hidden');

            document.querySelectorAll(`#${dropdown.id} > div`).forEach(el => {
                el.addEventListener('click', () => {
                    const provinceId = el.dataset.provinceId;
                    goToResultPage(provinceId);
                });
            });
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    }

    function goToResultPage(provinceId) {
        window.location.href = `pages/result.html?province_id=${provinceId}`;
    }

    function performSearch() {
        const query = searchInput.value.trim();
        if (!query) return;
        fetch(`php/searchSuggestions.php?q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(suggestions => {
                if (suggestions.length) {
                    goToResultPage(suggestions[0].province_id);
                } else {
                    alert('No province found for your "' + query + '" search. Please try a different keyword.');
                }
            });
    }

    if (searchButton) {
        searchButton.addEventListener('click', performSearch);
    }
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
            suggestionsDropdown.classList.add('hidden');
        }
    });
}