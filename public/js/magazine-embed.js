(function() {
    // Configuration options
    const config = {
        apiUrl: window.location.origin + '/api/magazine-users',
        containerId: 'magazine-subscription-form',
        primaryColor: '#3b82f6',
        successColor: '#10b981',
        errorColor: '#ef4444',
        borderRadius: '8px',
        fontFamily: 'system-ui, -apple-system, sans-serif'
    };

    // Override config with window.MagazineEmbedConfig if provided
    if (window.MagazineEmbedConfig) {
        Object.assign(config, window.MagazineEmbedConfig);
    }

    // Create form HTML
    const createFormHTML = () => {
        return `
            <div id="${config.containerId}" style="
                max-width: 400px;
                margin: 20px auto;
                padding: 24px;
                border: 1px solid #e5e7eb;
                border-radius: ${config.borderRadius};
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                background-color: white;
                font-family: ${config.fontFamily};
            ">
                <h3 style="
                    margin: 0 0 16px 0;
                    font-size: 18px;
                    font-weight: 600;
                    color: #1f2937;
                    text-align: center;
                ">Subscribe to Our Magazine</h3>
                
                <form id="magazine-form" style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="magazine-name" style="
                            display: block;
                            margin-bottom: 4px;
                            font-size: 14px;
                            font-weight: 500;
                            color: #374151;
                        ">Name</label>
                        <input 
                            type="text" 
                            id="magazine-name" 
                            name="name" 
                            required 
                            style="
                                width: 100%;
                                padding: 8px 12px;
                                border: 1px solid #d1d5db;
                                border-radius: 6px;
                                font-size: 14px;
                                box-sizing: border-box;
                                transition: border-color 0.2s ease;
                            "
                            placeholder="Enter your name"
                        >
                    </div>
                    
                    <div>
                        <label for="magazine-email" style="
                            display: block;
                            margin-bottom: 4px;
                            font-size: 14px;
                            font-weight: 500;
                            color: #374151;
                        ">Email</label>
                        <input 
                            type="email" 
                            id="magazine-email" 
                            name="email" 
                            required 
                            style="
                                width: 100%;
                                padding: 8px 12px;
                                border: 1px solid #d1d5db;
                                border-radius: 6px;
                                font-size: 14px;
                                box-sizing: border-box;
                                transition: border-color 0.2s ease;
                            "
                            placeholder="Enter your email"
                        >
                    </div>
                    
                    <button 
                        type="submit" 
                        id="magazine-submit"
                        style="
                            width: 100%;
                            padding: 12px 16px;
                            background-color: ${config.primaryColor};
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: background-color 0.2s ease;
                        "
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'"
                    >
                        Subscribe Now
                    </button>
                </form>
                
                <div id="magazine-message" style="
                    margin-top: 16px;
                    padding: 12px;
                    border-radius: 6px;
                    font-size: 14px;
                    display: none;
                "></div>
            </div>
        `;
    };

    // Show message function
    const showMessage = (message, type = 'success') => {
        const messageDiv = document.getElementById('magazine-message');
        messageDiv.style.display = 'block';
        messageDiv.textContent = message;
        
        if (type === 'success') {
            messageDiv.style.backgroundColor = '#dcfce7';
            messageDiv.style.color = '#166534';
            messageDiv.style.border = '1px solid #bbf7d0';
        } else {
            messageDiv.style.backgroundColor = '#fef2f2';
            messageDiv.style.color = '#991b1b';
            messageDiv.style.border = '1px solid #fecaca';
        }
    };

    // Hide message function
    const hideMessage = () => {
        const messageDiv = document.getElementById('magazine-message');
        messageDiv.style.display = 'none';
    };

    // Handle form submission
    const handleSubmit = async (event) => {
        event.preventDefault();
        
        const submitButton = document.getElementById('magazine-submit');
        const nameInput = document.getElementById('magazine-name');
        const emailInput = document.getElementById('magazine-email');
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Subscribing...';
        submitButton.style.opacity = '0.6';
        
        hideMessage();
        
        try {
            const response = await fetch(config.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: nameInput.value,
                    email: emailInput.value
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showMessage(data.message || 'Successfully subscribed!', 'success');
                nameInput.value = '';
                emailInput.value = '';
            } else {
                let errorMessage = 'An error occurred. Please try again.';
                if (data.message) {
                    errorMessage = data.message;
                } else if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join(', ');
                }
                showMessage(errorMessage, 'error');
            }
        } catch (error) {
            showMessage('Network error. Please check your connection and try again.', 'error');
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = 'Subscribe Now';
            submitButton.style.opacity = '1';
        }
    };

    // Initialize the form
    const init = () => {
        // Find target element
        let targetElement = document.getElementById(config.containerId);
        if (!targetElement) {
            // If no target element found, create one and append to body
            targetElement = document.createElement('div');
            document.body.appendChild(targetElement);
        }
        
        // Insert form HTML
        targetElement.innerHTML = createFormHTML();
        
        // Add event listener
        document.getElementById('magazine-form').addEventListener('submit', handleSubmit);
        
        // Add focus styles
        const inputs = document.querySelectorAll('#magazine-name, #magazine-email');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.borderColor = config.primaryColor;
                input.style.outline = 'none';
                input.style.boxShadow = `0 0 0 3px ${config.primaryColor}20`;
            });
            
            input.addEventListener('blur', () => {
                input.style.borderColor = '#d1d5db';
                input.style.boxShadow = 'none';
            });
        });
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();