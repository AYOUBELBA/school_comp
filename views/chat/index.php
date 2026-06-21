<?php
// views/chat/index.php

$chats = $chats ?? [];
$userId = $userId ?? 0;
$selectedChatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
?>

<div class="chat-container-modern">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
    <h2>💬 Messages</h2>
    <span class="chat-count" id="totalUnread">0</span>
</div>
        
        <div class="chat-list">
            <?php if (empty($chats)): ?>
                <div class="empty-chats">
                    <div class="empty-icon">💬</div>
                    <p>No conversations yet</p>
                    <small>Browse products and start chatting with sellers</small>
                    <a href="/products" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <?php foreach ($chats as $chat): ?>
                    <a href="/chat?chat_id=<?php echo $chat['id']; ?>" 
                       class="chat-item <?php echo $selectedChatId == $chat['id'] ? 'active' : ''; ?>">
                        <div class="chat-avatar">
                            <?php echo strtoupper(substr($chat['other_party_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                <?php echo htmlspecialchars($chat['other_party_name'] ?? 'Unknown'); ?>
                                <?php if ($chat['unread_count'] > 0): ?>
                                    <span class="unread-badge"><?php echo $chat['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="chat-product"><?php echo htmlspecialchars($chat['product_title'] ?? 'Product'); ?></div>
                            <div class="chat-last-message">
                                <?php echo htmlspecialchars($chat['last_message'] ?? 'No messages yet'); ?>
                            </div>
                        </div>
                        <div class="chat-time">
                            <?php if (!empty($chat['last_message_time'])): ?>
                                <?php echo date('M d', strtotime($chat['last_message_time'])); ?>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="chat-window">
        <?php if ($selectedChatId && !empty($chats)): ?>
            <?php 
            $currentChat = null;
            foreach ($chats as $c) {
                if ($c['id'] == $selectedChatId) {
                    $currentChat = $c;
                    break;
                }
            }
            ?>
            <?php if ($currentChat): ?>
                <div class="chat-window-header">
                    <div class="chat-window-user">
                        <div class="chat-avatar">
                            <?php echo strtoupper(substr($currentChat['other_party_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div>
                            <div class="chat-window-name">
                                <?php echo htmlspecialchars($currentChat['other_party_name'] ?? 'Unknown'); ?>
                            </div>
                            <div class="chat-window-product">
                                Selling: <?php echo htmlspecialchars($currentChat['product_title'] ?? 'Product'); ?>
                            </div>
                        </div>
                    </div>
                    <a href="/product/<?php echo $currentChat['product_id']; ?>" class="view-product-btn">
                        View Product
                    </a>
                </div>
                
                <div class="messages-container" id="messagesContainer">
                    <!-- Messages will be loaded here via AJAX -->
                    <div class="loading-messages">Loading messages...</div>
                </div>
                
                <div class="chat-input-area">
                    <textarea id="messageInput" rows="2" placeholder="Type a message..." 
                              onkeypress="if(event.key==='Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }"></textarea>
                    <button onclick="sendMessage()" class="send-btn">Send</button>
                </div>
            <?php else: ?>
                <div class="chat-placeholder">
                    <div class="placeholder-icon">💬</div>
                    <h3>Select a conversation</h3>
                    <p>Choose a chat from the sidebar to start messaging</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="chat-placeholder">
                <div class="placeholder-icon">💬</div>
                <h3>Your Messages</h3>
                <p>Select a conversation from the sidebar to start messaging</p>
                <?php if (empty($chats)): ?>
                    <a href="/products" class="btn btn-primary">Find Products to Chat About</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const chatId = <?php echo $selectedChatId; ?>;
const userId = <?php echo $userId; ?>;

// Load messages when chat is selected
if (chatId) {
    loadMessages(chatId);
}

function loadMessages(chatId) {
    fetch('/chat/messages/' + chatId)
        .then(response => response.json())
        .then(data => {
            if (data.messages) {
                renderMessages(data.messages);
                // Update unread count after loading messages
                updateUnreadCount();
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}
function updateUnreadCount() {
    fetch('/chat/unread-count')
        .then(response => response.json())
        .then(data => {
            // Update badge in parent window if exists
            if (window.opener) {
                // If opened in new window, update parent
            }
            // Update any badge in current window
            const badge = document.querySelector('.nav-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error updating unread count:', error);
        });
}
function renderMessages(messages) {
    const container = document.getElementById('messagesContainer');
    if (!container) return;
    
    if (messages.length === 0) {
        container.innerHTML = '<div class="no-messages">No messages yet. Start the conversation!</div>';
        return;
    }
    
    container.innerHTML = messages.map(msg => {
        const isOwn = msg.sender_id == userId;
        return `
            <div class="message ${isOwn ? 'message-sent' : 'message-received'}">
                <div class="message-content">${escapeHtml(msg.message)}</div>
                <div class="message-time">${formatTime(msg.created_at)}</div>
            </div>
        `;
    }).join('');
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !chatId) return;
    
    input.disabled = true;
    
    fetch('/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'chat_id=' + chatId + '&message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            // Add message to UI
            const container = document.getElementById('messagesContainer');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'message message-sent';
            msgDiv.innerHTML = `
                <div class="message-content">${escapeHtml(message)}</div>
                <div class="message-time">Just now</div>
            `;
            container.appendChild(msgDiv);
            container.scrollTop = container.scrollHeight;
        } else {
            alert('Failed to send message: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    })
    .finally(() => {
        input.disabled = false;
        input.focus();
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    }
    
    return date.toLocaleDateString();
}

// Auto-refresh messages every 5 seconds
let refreshInterval = null;

if (chatId) {
    refreshInterval = setInterval(() => {
        loadMessages(chatId);
    }, 5000);
}

// Clean up interval when leaving page
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
// Update unread count periodically
setInterval(() => {
    updateUnreadCount();
}, 5000);

// Also update when tab becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateUnreadCount();
        if (chatId) {
            loadMessages(chatId);
        }
    }
});
</script>

<style>
.chat-container-modern {
    display: grid;
    grid-template-columns: 340px 1fr;
    height: calc(100vh - 180px);
    background: var(--white);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    margin: var(--space-lg) 0;
}

.chat-sidebar {
    border-right: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
    background: var(--gray-50);
}

.chat-sidebar-header {
    padding: var(--space-lg);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--white);
}

.chat-sidebar-header h2 {
    font-size: var(--font-size-lg);
    font-weight: 700;
    margin: 0;
}

.chat-count {
    background: var(--primary);
    color: white;
    padding: 2px 10px;
    border-radius: var(--border-radius-full);
    font-size: var(--font-size-xs);
    font-weight: 600;
}

.chat-list {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-sm);
}

.chat-item {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    border-radius: var(--border-radius-md);
    text-decoration: none;
    color: var(--gray-700);
    transition: all 0.3s;
    cursor: pointer;
    margin-bottom: 2px;
}

.chat-item:hover {
    background: var(--gray-100);
}

.chat-item.active {
    background: #E6FFFA;
    border-left: 4px solid var(--primary);
}

.chat-avatar {
    width: 48px;
    height: 48px;
    border-radius: var(--border-radius-full);
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: var(--font-size-md);
    flex-shrink: 0;
}

.chat-info {
    flex: 1;
    min-width: 0;
}

.chat-name {
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.unread-badge {
    background: var(--danger);
    color: white;
    padding: 1px 8px;
    border-radius: var(--border-radius-full);
    font-size: var(--font-size-xs);
    font-weight: 700;
}

.chat-product {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
}

.chat-last-message {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-time {
    font-size: var(--font-size-xs);
    color: var(--gray-400);
    flex-shrink: 0;
}

.chat-window {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--white);
}

.chat-window-header {
    padding: var(--space-md) var(--space-lg);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--white);
}

.chat-window-user {
    display: flex;
    align-items: center;
    gap: var(--space-md);
}

.chat-window-name {
    font-weight: 600;
    font-size: var(--font-size-md);
}

.chat-window-product {
    font-size: var(--font-size-xs);
    color: var(--gray-500);
}

.view-product-btn {
    padding: 6px 16px;
    background: var(--primary);
    color: white;
    border-radius: var(--border-radius-md);
    text-decoration: none;
    font-size: var(--font-size-xs);
    font-weight: 600;
    transition: all 0.3s;
}

.view-product-btn:hover {
    background: var(--primary-dark);
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-lg);
    background: var(--gray-50);
}

.message {
    max-width: 70%;
    margin-bottom: var(--space-md);
    clear: both;
    animation: fadeInUp 0.3s ease;
}

.message-sent {
    float: right;
    background: var(--primary);
    color: white;
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 var(--border-radius-lg);
    padding: 10px 16px;
}

.message-received {
    float: left;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius-lg) var(--border-radius-lg) var(--border-radius-lg) 0;
    padding: 10px 16px;
}

.message-content {
    font-size: var(--font-size-sm);
    line-height: 1.5;
    word-wrap: break-word;
}

.message-time {
    font-size: var(--font-size-xs);
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
}

.message-received .message-time {
    color: var(--gray-500);
}

.chat-input-area {
    padding: var(--space-md) var(--space-lg);
    border-top: 1px solid var(--gray-200);
    display: flex;
    gap: var(--space-sm);
    background: var(--white);
}

.chat-input-area textarea {
    flex: 1;
    padding: 10px 16px;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius-md);
    resize: none;
    font-family: var(--font-family);
    font-size: var(--font-size-sm);
    transition: all 0.3s;
    background: var(--gray-50);
    min-height: 40px;
    max-height: 120px;
}

.chat-input-area textarea:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(44, 122, 123, 0.1);
}

.send-btn {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-family: var(--font-family);
    align-self: flex-end;
    height: 44px;
}

.send-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.chat-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--gray-500);
    padding: var(--space-2xl);
    text-align: center;
}

.placeholder-icon {
    font-size: 64px;
    margin-bottom: var(--space-md);
}

.chat-placeholder h3 {
    color: var(--gray-700);
    margin-bottom: var(--space-sm);
}

.chat-placeholder p {
    margin-bottom: var(--space-lg);
}

.empty-chats {
    text-align: center;
    padding: var(--space-xl) var(--space-md);
}

.empty-icon {
    font-size: 48px;
    margin-bottom: var(--space-md);
}

.empty-chats p {
    color: var(--gray-500);
    margin-bottom: var(--space-xs);
}

.empty-chats small {
    color: var(--gray-400);
    display: block;
    margin-bottom: var(--space-md);
}

.loading-messages, .no-messages {
    text-align: center;
    color: var(--gray-500);
    padding: var(--space-xl);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .chat-container-modern {
        grid-template-columns: 1fr;
        height: calc(100vh - 140px);
        border-radius: var(--border-radius-lg);
    }
    
    .chat-sidebar {
        display: <?php echo $selectedChatId ? 'none' : 'block'; ?>;
    }
    
    .chat-window {
        display: <?php echo $selectedChatId ? 'flex' : 'none'; ?>;
    }
}
</style>