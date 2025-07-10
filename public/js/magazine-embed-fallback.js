/**
 * Fallback script for magazine embed widget
 * This script provides error handling when the main embed script fails to load
 */
(function() {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkEmbed);
    } else {
        checkEmbed();
    }

    function checkEmbed() {
        // Check if the main embed script has loaded after a short delay
        setTimeout(function() {
            var container = document.getElementById('magazine-subscription-form');
            if (container && container.innerHTML.trim() === '') {
                // Container exists but is empty - likely a loading issue
                showError(container, 'Unable to load subscription form. Please try refreshing the page.');
            }
        }, 2000); // Wait 2 seconds for the main script to load
    }

    function showError(container, message) {
        container.innerHTML = 
            '<div style="padding: 20px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #dc2626; text-align: center; font-family: system-ui, -apple-system, sans-serif;">' +
                '<h4 style="margin: 0 0 8px 0; font-size: 16px;">Loading Error</h4>' +
                '<p style="margin: 0; font-size: 14px;">' + message + '</p>' +
            '</div>';
    }
})();