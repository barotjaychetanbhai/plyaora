document.addEventListener("DOMContentLoaded", () => {
    // Initial load
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    params.delete('page');
    const additionalParams = params.toString();
    loadPage(page, additionalParams);
});

function loadPage(page, additionalParams = '') {
    const appContainer = document.getElementById('app');

    // Skeleton Loading State
    appContainer.innerHTML = `
        <div style="padding: 100px; text-align: center; color: var(--brown-500);">
            <h2>Loading...</h2>
        </div>
    `;

    // Construct URL
    let cleanParams = typeof additionalParams === 'object' ? new URLSearchParams(additionalParams).toString() : additionalParams;
    const url = `pages/${page}.php${cleanParams ? '?' + cleanParams : ''}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            appContainer.innerHTML = html;

            // Execute scripts in the loaded HTML
            const scripts = appContainer.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.innerHTML = script.innerHTML;
                }
                document.body.appendChild(newScript);
                script.remove();
            });

            // Update URL without reloading
            let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=' + page;
            if(additionalParams) {
                newUrl += '&' + additionalParams;
            }
            window.history.pushState({ path: newUrl }, '', newUrl);

            // Re-initialize any necessary plugins/animations here if needed
            // e.g. initializeGSAP()
        })
        .catch(error => {
            console.error('Error loading page:', error);
            appContainer.innerHTML = `
                <div style="padding: 100px; text-align: center; color: red;">
                    <h2>Error loading page. Please try again later.</h2>
                </div>
            `;
        });
}

// Handle browser back/forward buttons
window.addEventListener('popstate', (event) => {
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 'home';
    // We parse out extra params manually or just reload the whole query string
    const query = window.location.search.substring(1);
    const paramsObj = new URLSearchParams(query);
    paramsObj.delete('page');
    const additionalParams = paramsObj.toString();

    // Avoid double pushState by using a flag or just a modified load function
    fetch(`pages/${page}.php${additionalParams ? '?' + additionalParams : ''}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('app').innerHTML = html;
        });
});