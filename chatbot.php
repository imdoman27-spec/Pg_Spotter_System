<?php
// Get user type from session (not used for initial menu, only for follow-ups)
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';
$is_logged_in = isset($_SESSION['user_id']);
?>

<div id="chatbot-container" class="chatbot-container" data-user-type="<?php echo htmlspecialchars($user_type); ?>" data-logged-in="<?php echo $is_logged_in ? 'true' : 'false'; ?>">
    <div id="chatbot-toggle" class="chatbot-toggle" title="Open Chat">
        <i class="fas fa-comments"></i>
        <span class="chatbot-badge">1</span>
    </div>

    <div id="chatbot-window" class="chatbot-window" style="display: none;">
        <!-- Page Slider - Draggable Scroller -->
        <div class="page-slider-track" id="page-slider-track">
            <div class="page-slider-thumb" id="page-slider-thumb"></div>
        </div>
        
        <div class="chatbot-header">
            <h3>PG Spotter Assistant</h3>
            <button id="chatbot-close" class="chatbot-close">×</button>
        </div>
        
        <div class="chatbot-content-wrapper">
            <!-- Main Chat Area -->
            <div class="chatbot-main">
                <div id="chatbot-messages" class="chatbot-messages">
                    <div class="chatbot-message bot-message">
                        <p>Hello! 👋 Welcome to PG Spotter. What are you looking for?</p>
                    </div>
                    
                    <!-- Type Selection Menu -->
                    <div class="chatbot-menu">
                        <h4 class="menu-title">Select your query type:</h4>
                        <div class="chatbot-quick-replies">
                            <button class="quick-reply-btn type-selector" data-type="tenant">🔍 I'm Looking for a PG</button>
                            <button class="quick-reply-btn type-selector" data-type="owner">🏠 I Want to List My PG</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chatbot-input-area">
            <button id="chatbot-new-chat" class="chatbot-new-chat" title="Start New Chat">
                <i class="fas fa-redo"></i>
            </button>
            <input 
                type="text" 
                id="chatbot-input" 
                class="chatbot-input" 
                placeholder="Type your question..." 
                autocomplete="off"
            >
            <button id="chatbot-send" class="chatbot-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

@media (max-width: 480px) {
    .chatbot-container {
        bottom: 70px;
        right: 10px;
    }

    .chatbot-toggle {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }

    .chatbot-window {
        position: fixed;
        width: calc(100vw - 20px);
        max-height: 60vh;
        bottom: 130px;
        right: 10px;
        left: 10px;
    }
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f7a01d 0%, #3952a3 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(247, 160, 29, 0.4);
    transition: all 0.3s ease;
    position: relative;
    animation: float 3s ease-in-out infinite;
    user-select: none;
}

.chatbot-toggle:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 20px rgba(247, 160, 29, 0.6);
    animation: none;
}

.chatbot-toggle.dragging {
    cursor: grabbing !important;
    animation: none !important;
    box-shadow: 0 8px 25px rgba(247, 160, 29, 0.6);
}

/* Show grab cursor when window is open */
.chatbot-window[style*="display: flex"] ~ .chatbot-toggle {
    cursor: grab;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) translateX(0px);
    }
    25% {
        transform: translateY(-15px) translateX(5px);
    }
    50% {
        transform: translateY(-10px) translateX(-8px);
    }
    75% {
        transform: translateY(-5px) translateX(8px);
    }
}

.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff6b6b;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.chatbot-window {
    position: absolute;
    top: auto;
    bottom: 80px;
    right: 0;
    width: 480px;
    height: auto;
    max-height: 75vh;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease-out;
    z-index: 10000;
    min-height: 350px;
    transform-origin: bottom right;
}

/* Scroll Indicator */
.scroll-indicator {
    position: absolute;
    right: 0;
    top: 57px;
    bottom: 58px;
    width: 4px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 2px;
    z-index: 10;
}

.scroll-indicator-thumb {
    position: absolute;
    right: 0;
    top: 0;
    width: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
    transition: all 0.3s ease;
    opacity: 1;
}

.scroll-indicator-thumb:hover {
    width: 6px;
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: grab;
    user-select: none;
    transition: cursor 0.2s ease;
    gap: 12px;
}

.chatbot-header h3 {
    margin: 5px;
    font-size: 14px;
    font-weight: 600;
}

.chatbot-new-chat {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 8px 10px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.chatbot-new-chat:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 12px rgba(102, 126, 234, 0.4);
}

.chatbot-new-chat i {
    font-size: 12px;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
    flex-shrink: 0;
}

.chatbot-close:hover {
    transform: scale(1.1);
}

/* Content Wrapper */
.chatbot-content-wrapper {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
    min-height: 0;
    position: relative;
}

/* Page Slider - Draggable Scroller on Right */
.page-slider-track {
    position: absolute;
    right: 0;
    top: 57px;
    bottom: 58px;
    width: 12px;
    background: rgba(102, 126, 234, 0.08);
    border-radius: 6px;
    z-index: 20;
    cursor: grab;
}

.page-slider-track:active {
    cursor: grabbing;
}

.page-slider-thumb {
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 6px;
    cursor: grab;
    transition: background 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    min-height: 30px;
}

.page-slider-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    box-shadow: 0 2px 12px rgba(102, 126, 234, 0.5);
}

.page-slider-thumb:active {
    cursor: grabbing;
}

/* Main Chat Area */
.chatbot-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}

.chatbot-messages {
    flex: 1 1 auto;
    overflow-y: auto !important;
    overflow-x: hidden;
    padding: 8px;
    padding-right: 24px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 0px;
    min-height: 180px;
    max-height: none;
}

.chatbot-message {
    display: flex;
    flex-direction: column;
    margin-bottom: 8px;
    animation: fadeIn 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bot-message {
    justify-content: flex-start;
    align-items: flex-start;
}

.user-message {
    justify-content: flex-end;
    align-items: flex-end;
}

.chatbot-message p {
    margin: 0 0 8px 0;
    padding: 4px 14px;
    border-radius: 12px;
    font-size: 12px;
    line-height: 1.4;
    max-width: 100%;
    word-wrap: break-word;
    box-sizing: border-box;
}

.bot-message p {
    background: #e9ecef;
    color: #333;
    border-radius: 12px 12px 12px 0;
}

.user-message p {
    background: #667eea;
    color: white;
    border-radius: 12px 12px 0 12px;
}

.chatbot-input-area {
    padding: 12px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 8px;
    background: white;
    border-radius: 0 0 12px 12px;
    align-items: center;
}

.chatbot-input {
    flex: 1;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 12px;
    outline: none;
    transition: border-color 0.2s ease;
}

.chatbot-input:focus {
    border-color: #667eea;
}

.chatbot-send {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 6px;
    width: 40px;
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.chatbot-send:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.chatbot-send:active {
    transform: scale(0.95);
}

/* Scrollbar styling */
.chatbot-messages::-webkit-scrollbar {
    width: 6px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 3px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}

/* Quick Reply Buttons */
.chatbot-menu {
    background: white;
    border-radius: 8px;
    margin-top: 12px;
    overflow: hidden;
}

.menu-title {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 10px 12px;
    margin: 0;
    font-size: 11px;
    font-weight: 700;
    color: #495057;
    border-bottom: 1px solid #dee2e6;
}

.chatbot-quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    padding: 12px;
    background: white;
}

.quick-reply-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
    white-space: nowrap;
    font-weight: 500;
}

.quick-reply-btn.faq-btn {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 2px 6px rgba(79, 172, 254, 0.3);
}

.quick-reply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.quick-reply-btn.faq-btn:hover {
    box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4);
}

.quick-reply-btn:active {
    transform: scale(0.95);
}

/* Back to Main Menu button */
.back-to-menu-container {
    display: flex;
    justify-content: center;
    padding: 10px 20px;
    margin: 5px 0 10px 0;
    width: 100%;
    box-sizing: border-box;
}

.back-to-menu-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.back-to-menu-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.back-to-menu-btn:active {
    transform: translateY(0);
}

/* Action buttons in chatbot messages */
.chatbot-action-btn-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    margin-bottom: 10px;
    justify-content: flex-start;
    width: 100%;
    box-sizing: border-box;
}

.chatbot-action-btn {
    display: inline-block;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 20px;
    padding: 9px 18px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
    white-space: nowrap;
    overflow: visible;
    flex-shrink: 0;
}

.chatbot-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    color: white;
}

.chatbot-action-btn:active {
    transform: translateY(0);
}

/* Tablet responsive */
@media (max-width: 768px) {
    .chatbot-window {
        width: calc(100vw - 30px);
        height: auto;
        max-height: 70vh;
        bottom: 80px;
        right: 15px;
        left: 15px;
    }
    
    .slide-content {
        padding: 16px;
    }
    
    .slide-icon {
        font-size: 30px;
    }
    
    .slide-content h4 {
        font-size: 14px;
    }
    
    .slide-content p {
        font-size: 12px;
    }
    
    .chatbot-messages {
        overflow-y: auto !important;
        min-height: 200px;
        padding-right: 20px;
    }
}

/* Mobile responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 20px);
        height: auto;
        max-height: 65vh;
        bottom: 70px;
        right: 10px;
        left: 10px;
    }
    
    .chatbot-slider-container {
        padding: 12px;
    }
    
    .slide {
        padding: 12px;
    }
    
    .slide-content {
        padding: 14px;
    }
    
    .slide-icon {
        font-size: 28px;
    }
    
    .slide-content h4 {
        font-size: 13px;
    }
    
    .slide-content p {
        font-size: 11px;
    }
    
    .slider-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    
    .chatbot-messages {
        overflow-y: auto !important;
        min-height: 180px;
        padding-right: 18px;
    }

    .chatbot-message p {
        max-width: 100%;
    }
    .chatbot-action-btn-container {
        margin-top: 10px;
    }
    
    .chatbot-action-btn {
        padding: 8px 14px;
        font-size: 11px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotHeader = document.querySelector('.chatbot-header');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotNewChat = document.getElementById('chatbot-new-chat');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const sliderTrack = document.getElementById('slider-track');
    const prevSlideBtn = document.getElementById('prev-slide');
    const nextSlideBtn = document.getElementById('next-slide');
    const sliderDotsContainer = document.getElementById('slider-dots');
    const pageSliderThumb = document.getElementById('page-slider-thumb');
    const pageSliderTrack = document.getElementById('page-slider-track');

    // Dragging variables
    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;
    let containerX = 0;
    let containerY = 0;
    let isWindowOpen = false;
    
    // Page slider dragging variables
    let isSliderDragging = false;
    let sliderOffsetY = 0;
    
    // Slider variables
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;

    // Update page slider
    function updatePageSlider() {
        if (!chatbotMessages) return;
        
        const scrollHeight = chatbotMessages.scrollHeight;
        const clientHeight = chatbotMessages.clientHeight;
        const scrollTop = chatbotMessages.scrollTop;
        const trackHeight = pageSliderTrack.offsetHeight;
        
        // Always show the thumb
        pageSliderThumb.style.display = 'block';
        
        // Calculate thumb height and position
        const thumbHeight = Math.max((clientHeight / scrollHeight) * trackHeight, 30); // Minimum 30px height
        const maxScroll = scrollHeight - clientHeight;
        const thumbTop = maxScroll > 0 ? (scrollTop / maxScroll) * (trackHeight - thumbHeight) : 0;
        
        pageSliderThumb.style.height = thumbHeight + 'px';
        pageSliderThumb.style.top = thumbTop + 'px';
    }

    // Page slider drag handling
    pageSliderThumb.addEventListener('mousedown', function(e) {
        isSliderDragging = true;
        sliderOffsetY = e.clientY - pageSliderThumb.getBoundingClientRect().top;
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isSliderDragging || !chatbotMessages) return;
        
        const trackRect = pageSliderTrack.getBoundingClientRect();
        const trackHeight = pageSliderTrack.offsetHeight;
        const thumbHeight = pageSliderThumb.offsetHeight;
        const maxTrackOffset = trackHeight - thumbHeight;
        
        let thumbTop = e.clientY - trackRect.top - sliderOffsetY;
        thumbTop = Math.max(0, Math.min(thumbTop, maxTrackOffset));
        
        const scrollHeight = chatbotMessages.scrollHeight;
        const clientHeight = chatbotMessages.clientHeight;
        const maxScroll = scrollHeight - clientHeight;
        
        const scrollTop = (thumbTop / maxTrackOffset) * maxScroll;
        chatbotMessages.scrollTop = scrollTop;
    });

    document.addEventListener('mouseup', function() {
        isSliderDragging = false;
    });

    // Listen to scroll events
    if (chatbotMessages) {
        chatbotMessages.addEventListener('scroll', updatePageSlider);
        
        // Update on content change
        const observer = new MutationObserver(updatePageSlider);
        observer.observe(chatbotMessages, { childList: true, subtree: true });
    }

    // Update on window resize
    window.addEventListener('resize', updatePageSlider);

    // Load saved position from localStorage
    function loadPosition() {
        const savedPosition = localStorage.getItem('chatbot-position');
        if (savedPosition) {
            const pos = JSON.parse(savedPosition);
            chatbotContainer.style.bottom = 'auto';
            chatbotContainer.style.right = 'auto';
            chatbotContainer.style.top = pos.top + 'px';
            chatbotContainer.style.left = pos.left + 'px';
            containerX = pos.left;
            containerY = pos.top;
        }
    }

    // Save position to localStorage
    function savePosition() {
        const position = {
            left: containerX,
            top: containerY
        };
        localStorage.setItem('chatbot-position', JSON.stringify(position));
    }

    // Make toggle button draggable when window is open
    chatbotToggle.addEventListener('mousedown', function(e) {
        if (!isWindowOpen) return; // Only drag when window is open
        
        isDragging = true;
        chatbotToggle.classList.add('dragging');
        
        const rect = chatbotContainer.getBoundingClientRect();
        containerX = rect.left;
        containerY = rect.top;
        
        offsetX = e.clientX - containerX;
        offsetY = e.clientY - containerY;
        
        e.preventDefault();
        e.stopPropagation();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        containerX = e.clientX - offsetX;
        containerY = e.clientY - offsetY;
        
        // Keep within viewport bounds
        containerX = Math.max(0, Math.min(containerX, window.innerWidth - chatbotContainer.offsetWidth));
        containerY = Math.max(0, Math.min(containerY, window.innerHeight - chatbotContainer.offsetHeight));
        
        chatbotContainer.style.left = containerX + 'px';
        chatbotContainer.style.top = containerY + 'px';
        chatbotContainer.style.bottom = 'auto';
        chatbotContainer.style.right = 'auto';
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            chatbotToggle.classList.remove('dragging');
            savePosition();
        }
    });

    // Load saved position on page load
    loadPosition();

    // Toggle chatbot window
    chatbotToggle.addEventListener('click', function(e) {
        if (isDragging) return; // Don't toggle if dragging
        
        if (chatbotWindow.style.display === 'none') {
            chatbotWindow.style.display = 'flex';
            isWindowOpen = true;
            chatbotInput.focus();
            setTimeout(updatePageSlider, 100); // Update page slider after opening
        } else {
            chatbotWindow.style.display = 'none';
            isWindowOpen = false;
        }
    });

    // Close chatbot
    chatbotClose.addEventListener('click', function() {
        chatbotWindow.style.display = 'none';
        isWindowOpen = false;
    });

    // Start new chat
    chatbotNewChat.addEventListener('click', function() {
        // Clear all messages
        chatbotMessages.innerHTML = '';
        
        // Add welcome message
        const welcomeDiv = document.createElement('div');
        welcomeDiv.className = 'chatbot-message bot-message';
        welcomeDiv.innerHTML = '<p>Hello! 👋 I\'m here to help you find your perfect PG. Choose an option below or type your question.</p>';
        chatbotMessages.appendChild(welcomeDiv);
        
        // Add menu
        addMenu();
        
        // Scroll to top
        chatbotMessages.scrollTop = 0;
    });

    // Handle quick reply button clicks
    document.addEventListener('click', function(e) {
        // Handle type selector for all users
        if (e.target.classList.contains('type-selector')) {
            const selectedType = e.target.getAttribute('data-type');
            const typeLabel = selectedType === 'owner' ? 'PG Owner' : 'Tenant Looking for PG';
            
            // Add user selection message
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'chatbot-message user-message';
            userMessageDiv.innerHTML = `<p>${e.target.textContent}</p>`;
            chatbotMessages.appendChild(userMessageDiv);
            
            // Remove type selector menu
            const typeMenu = chatbotMessages.querySelector('.chatbot-menu');
            if (typeMenu) {
                typeMenu.remove();
            }
            
            // Add bot response
            const botResponseDiv = document.createElement('div');
            botResponseDiv.className = 'chatbot-message bot-message';
            botResponseDiv.innerHTML = `<p>Great! I'll show you ${typeLabel} related FAQs and help. 😊</p>`;
            chatbotMessages.appendChild(botResponseDiv);
            
            // Store selected type
            chatbotContainer.setAttribute('data-current-type', selectedType);
            
            // Add relevant menu after 500ms
            setTimeout(() => {
                addTypeSpecificMenu(selectedType);
            }, 500);
            
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            return;
        }
        
        if (e.target.classList.contains('quick-reply-btn')) {
            const query = e.target.getAttribute('data-query');
            sendMessageWithText(query);
        }
        
        // Handle "Back to Main Menu" button
        if (e.target.classList.contains('back-to-menu-btn')) {
            // Remove all messages except initial ones
            const allMessages = chatbotMessages.querySelectorAll('.chatbot-message, .back-to-menu-container');
            allMessages.forEach(msg => msg.remove());
            
            // Add welcome message
            const welcomeDiv = document.createElement('div');
            welcomeDiv.className = 'chatbot-message bot-message';
            welcomeDiv.innerHTML = '<p>Hello! 👋 Welcome to PG Spotter. What are you looking for?</p>';
            chatbotMessages.appendChild(welcomeDiv);
            
            // Re-add type selector menu
            addMenu();
            
            // Clear selected type
            chatbotContainer.removeAttribute('data-current-type');
            
            // Scroll to top
            chatbotMessages.scrollTop = 0;
        }
    });

    // Send message function
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message === '') return;
        sendMessageWithText(message);
        chatbotInput.value = '';
    }

    // Send message with specific text
    function sendMessageWithText(message) {
        // Add user message to chat
        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'chatbot-message user-message';
        userMessageDiv.innerHTML = `<p>${escapeHtml(message)}</p>`;
        chatbotMessages.appendChild(userMessageDiv);

        // Remove any existing menu
        const existingMenu = chatbotMessages.querySelector('.chatbot-menu');
        if (existingMenu) {
            existingMenu.remove();
        }

        // Scroll to bottom
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        // Send query to API
        fetch('<?php echo BASE_URL; ?>chatbot_api.php?q=' + encodeURIComponent(message))
            .then(response => response.json())
            .then(data => {
                const botMessageDiv = document.createElement('div');
                botMessageDiv.className = 'chatbot-message bot-message';
                
                // Check if message contains action buttons
                if (data.message.includes('<div class=\'chatbot-action-btn-container\'>')) {
                    // Split message into text and buttons
                    const parts = data.message.split('<div class=\'chatbot-action-btn-container\'>');
                    const textPart = escapeHtml(parts[0]).replace(/\n/g, '<br>');
                    const buttonPart = '<div class=\'chatbot-action-btn-container\'>' + parts[1];
                    botMessageDiv.innerHTML = `<p>${textPart}</p>${buttonPart}`;
                } else {
                    // Normal message without buttons
                    botMessageDiv.innerHTML = `<p>${escapeHtml(data.message).replace(/\n/g, '<br>')}</p>`;
                }
                
                chatbotMessages.appendChild(botMessageDiv);

                // Add "Back to Main Menu" button
                const backButtonDiv = document.createElement('div');
                backButtonDiv.className = 'back-to-menu-container';
                backButtonDiv.innerHTML = `<button class="back-to-menu-btn">🏠 Back to Main Menu</button>`;
                chatbotMessages.appendChild(backButtonDiv);

                // Scroll to bottom
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'chatbot-message bot-message';
                errorDiv.innerHTML = '<p>Sorry, I encountered an error. Please try again.</p>';
                chatbotMessages.appendChild(errorDiv);

                // Add "Back to Main Menu" button even on error
                const backButtonDiv = document.createElement('div');
                backButtonDiv.className = 'back-to-menu-container';
                backButtonDiv.innerHTML = `<button class="back-to-menu-btn">🏠 Back to Main Menu</button>`;
                chatbotMessages.appendChild(backButtonDiv);
            });
    }

    // Function to add initial type selector menu
    function addMenu() {
        // Remove existing menu first
        const existingMenu = chatbotMessages.querySelector('.chatbot-menu');
        if (existingMenu) {
            existingMenu.remove();
        }

        const menuDiv = document.createElement('div');
        menuDiv.className = 'chatbot-menu';
        menuDiv.innerHTML = `
            <h4 class="menu-title">Select your query type:</h4>
            <div class="chatbot-quick-replies">
                <button class="quick-reply-btn type-selector" data-type="tenant">🔍 I'm Looking for a PG</button>
                <button class="quick-reply-btn type-selector" data-type="owner">🏠 I Want to List My PG</button>
            </div>
        `;
        
        chatbotMessages.appendChild(menuDiv);
    }

    // Function to add type-specific menu
    function addTypeSpecificMenu(selectedType) {
        const menuDiv = document.createElement('div');
        menuDiv.className = 'chatbot-menu';
        
        if (selectedType === 'owner') {
            menuDiv.innerHTML = `
                <h4 class="menu-title">📋 Quick Menu</h4>
                <div class="chatbot-quick-replies">
                    <button class="quick-reply-btn" data-query="How to list a PG?">🏠 List My PG</button>
                    <button class="quick-reply-btn" data-query="How to manage listings?">📋 Manage Listings</button>
                    <button class="quick-reply-btn" data-query="How to handle inquiries?">📨 Handle Inquiries</button>
                    <button class="quick-reply-btn" data-query="Contact support">💬 Contact Us</button>
                </div>
                
                <h4 class="menu-title">❓ FAQs</h4>
                <div class="chatbot-quick-replies">
                    <button class="quick-reply-btn faq-btn" data-query="How to list my PG?">🏠 List My PG</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to edit my listing?">✏️ Edit Listing</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to delete my listing?">🗑️ Delete Listing</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to manage inquiries?">📨 Manage Inquiries</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to reply to tenants?">💬 Reply to Inquiries</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to add photos?">📷 Add Photos</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to set pricing?">💰 Set Pricing</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to view my insights?">📊 View Insights</button>
                </div>
            `;
        } else {
            menuDiv.innerHTML = `
                <h4 class="menu-title">📋 Quick Menu</h4>
                <div class="chatbot-quick-replies">
                    <button class="quick-reply-btn" data-query="How to search for PGs?">🔍 Search PGs</button>
                    <button class="quick-reply-btn" data-query="How to book a PG?">📝 How to Book</button>
                    <button class="quick-reply-btn" data-query="How to create an account?">👤 Sign Up</button>
                    <button class="quick-reply-btn" data-query="Contact support">💬 Contact Us</button>
                </div>
                
                <h4 class="menu-title">❓ FAQs</h4>
                <div class="chatbot-quick-replies">
                    <button class="quick-reply-btn faq-btn" data-query="Tell me about pricing">💰 Pricing & Rent</button>
                    <button class="quick-reply-btn faq-btn" data-query="What amenities are available?">🏠 Amenities</button>
                    <button class="quick-reply-btn faq-btn" data-query="Tell me about security deposit">🔒 Security Deposit</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to save favorites?">⭐ Save Favorites</button>
                    <button class="quick-reply-btn faq-btn" data-query="Tell me about reviews">📝 Reviews & Ratings</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to contact owner?">📧 Contact Owner</button>
                    <button class="quick-reply-btn faq-btn" data-query="Tell me about location and maps">📍 Location & Maps</button>
                    <button class="quick-reply-btn faq-btn" data-query="How to view photos?">📷 View Photos</button>
                </div>
            `;
        }
        
        chatbotMessages.appendChild(menuDiv);
    }

    // Send message on button click
    chatbotSend.addEventListener('click', sendMessage);

    // Send message on Enter key
    chatbotInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            sendMessage();
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
</script>
