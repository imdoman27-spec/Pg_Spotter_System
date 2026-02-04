document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotMessages = document.getElementById('chatbot-messages');

    // Toggle chatbot window
    chatbotToggle.addEventListener('click', function() {
        if (chatbotWindow.style.display === 'none') {
            chatbotWindow.style.display = 'flex';
            chatbotInput.focus();
        } else {
            chatbotWindow.style.display = 'none';
        }
    });

    // Close chatbot
    chatbotClose.addEventListener('click', function() {
        chatbotWindow.style.display = 'none';
    });

    // Send message function with optional message parameter
    function sendMessage(message = null) {
        const msg = message || chatbotInput.value.trim();
        if (msg === '') return;

        // Add user message to chat
        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'chatbot-message user-message';
        userMessageDiv.innerHTML = `<p>${escapeHtml(msg)}</p>`;
        chatbotMessages.appendChild(userMessageDiv);

        if (!message) {
            chatbotInput.value = '';
        }

        // Send query to API
        fetch('chatbot_api.php?q=' + encodeURIComponent(msg))
            .then(response => response.json())
            .then(data => {
                if (data && data.message) {
                    const botMessageDiv = document.createElement('div');
                    botMessageDiv.className = 'chatbot-message bot-message';
                    botMessageDiv.innerHTML = `<p>${escapeHtml(data.message).replace(/\n/g, '<br>')}</p>`;
                    chatbotMessages.appendChild(botMessageDiv);
                } else {
                    throw new Error('Invalid response format');
                }

                // Scroll to bottom
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'chatbot-message bot-message';
                errorDiv.innerHTML = '<p>Sorry, I encountered an error. Please try again.</p>';
                chatbotMessages.appendChild(errorDiv);
            });

        // Scroll to bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Send message on button click
    chatbotSend.addEventListener('click', () => sendMessage());

    // Send message on Enter key
    chatbotInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });

    // Event delegation for quick menu buttons
    chatbotMessages.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-menu-btn')) {
            const buttonText = e.target.textContent.trim();
            sendMessage(buttonText);
        }
    });

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});