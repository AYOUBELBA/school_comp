// public/assets/js/chat.js

class ChatClient {
    constructor() {
        this.ws = null;
        this.userId = null;
        this.currentChatId = null;
        this.connected = false;
        this.typingTimeout = null;
        this.messageCallbacks = [];
        
        this.init();
    }
    
    init() {
        this.connect();
        this.setupEventListeners();
    }
    
    connect() {
        const wsUrl = `ws://${window.location.hostname}:8080`;
        this.ws = new WebSocket(wsUrl);
        
        this.ws.onopen = () => {
            console.log('WebSocket connected');
            this.connected = true;
            this.authenticate();
        };
        
        this.ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            } catch (e) {
                console.error('Error parsing message:', e);
            }
        };
        
        this.ws.onclose = () => {
            console.log('WebSocket disconnected');
            this.connected = false;
            setTimeout(() => this.connect(), 3000);
        };
        
        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };
    }
    
    authenticate() {
        const token = this.getAuthToken();
        if (token) {
            this.send({
                type: 'authenticate',
                user_id: this.userId,
                token: token
            });
        }
    }
    
    sendMessage(chatId, receiverId, message) {
        if (!this.connected) {
            console.error('WebSocket not connected');
            return false;
        }
        
        this.send({
            type: 'message',
            chat_id: chatId,
            receiver_id: receiverId,
            message: message
        });
        
        return true;
    }
    
    send(data) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
            return true;
        }
        return false;
    }
    
    handleMessage(data) {
        switch (data.type) {
            case 'authenticated':
                this.userId = data.user_id;
                this.onAuthenticated();
                break;
                
            case 'message':
                this.displayMessage(data);
                this.notifyCallbacks('message', data);
                break;
                
            case 'typing':
                this.handleTyping(data);
                break;
                
            case 'read':
                this.markMessagesAsRead(data);
                break;
                
            case 'unread_count':
                this.updateUnreadCount(data.count);
                break;
                
            case 'error':
                console.error('Server error:', data.message);
                break;
        }
    }
    
    displayMessage(data) {
        if (data.chat_id !== this.currentChatId) {
            // Message for another chat, update notification
            this.updateUnreadCount(1);
            return;
        }
        
        const messagesContainer = document.getElementById('messages-container');
        if (!messagesContainer) return;
        
        const isOwnMessage = data.sender_id === this.userId;
        const messageHtml = `
            <div class="message ${isOwnMessage ? 'message-sent' : 'message-received'}">
                <div class="message-content">${this.escapeHtml(data.message)}</div>
                <div class="message-time">${this.formatTime(data.created_at)}</div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Mark as read if this is the current chat
        if (!isOwnMessage) {
            this.send({
                type: 'read',
                chat_id: data.chat_id,
                sender_id: data.sender_id
            });
        }
    }
    
    handleTyping(data) {
        const typingIndicator = document.getElementById('typing-indicator');
        if (!typingIndicator) return;
        
        if (data.is_typing && data.sender_id !== this.userId) {
            typingIndicator.style.display = 'block';
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                typingIndicator.style.display = 'none';
            }, 3000);
        } else {
            typingIndicator.style.display = 'none';
        }
    }
    
    markMessagesAsRead(data) {
        // Update UI to show messages as read
        const messages = document.querySelectorAll('.message-received');
        messages.forEach(msg => {
            // Mark as read logic
        });
    }
    
    updateUnreadCount(count) {
        const badge = document.getElementById('unread-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
    
    sendTyping(chatId, receiverId, isTyping) {
        if (!this.connected) return;
        
        this.send({
            type: 'typing',
            chat_id: chatId,
            receiver_id: receiverId,
            is_typing: isTyping
        });
    }
    
    startTyping(chatId, receiverId) {
        this.sendTyping(chatId, receiverId, true);
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.stopTyping(chatId, receiverId);
        }, 2000);
    }
    
    stopTyping(chatId, receiverId) {
        this.sendTyping(chatId, receiverId, false);
    }
    
    onAuthenticated() {
        console.log('User authenticated:', this.userId);
        // Load initial chats and messages
        this.loadChats();
        this.loadUnreadCount();
    }
    
    loadChats() {
        fetch('/api/chats')
            .then(response => response.json())
            .then(data => {
                this.renderChats(data);
            })
            .catch(error => {
                console.error('Error loading chats:', error);
            });
    }
    
    loadUnreadCount() {
        fetch('/api/unread-count')
            .then(response => response.json())
            .then(data => {
                this.updateUnreadCount(data.count);
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
    }
    
    renderChats(chats) {
        const container = document.getElementById('chat-list');
        if (!container) return;
        
        container.innerHTML = chats.map(chat => `
            <div class="chat-item" data-chat-id="${chat.id}" onclick="chat.selectChat('${chat.id}')">
                <img src="${chat.avatar || '/assets/images/default-avatar.png'}" alt="${chat.other_party_name}" class="chat-avatar">
                <div class="chat-info">
                    <div class="chat-name">${chat.other_party_name}</div>
                    <div class="chat-last-message">${this.truncate(chat.last_message, 50)}</div>
                </div>
                <div class="chat-time">${this.formatTime(chat.last_message_time)}</div>
                ${chat.unread_count > 0 ? `<span class="chat-unread">${chat.unread_count}</span>` : ''}
            </div>
        `).join('');
    }
    
    selectChat(chatId) {
        this.currentChatId = chatId;
        
        // Load messages for this chat
        fetch(`/api/chats/${chatId}/messages`)
            .then(response => response.json())
            .then(data => {
                this.renderMessages(data);
                this.showChatWindow();
            })
            .catch(error => {
                console.error('Error loading messages:', error);
            });
    }
    
    renderMessages(messages) {
        const container = document.getElementById('messages-container');
        if (!container) return;
        
        container.innerHTML = messages.map(message => {
            const isOwnMessage = message.sender_id === this.userId;
            return `
                <div class="message ${isOwnMessage ? 'message-sent' : 'message-received'}">
                    <div class="message-content">${this.escapeHtml(message.message)}</div>
                    <div class="message-time">${this.formatTime(message.created_at)}</div>
                </div>
            `;
        }).join('');
        
        container.scrollTop = container.scrollHeight;
    }
    
    setupEventListeners() {
        // Message input
        const input = document.getElementById('message-input');
        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendCurrentMessage();
                }
            });
            
            input.addEventListener('input', () => {
                if (this.currentChatId) {
                    this.startTyping(this.currentChatId, this.getReceiverId());
                }
            });
        }
        
        // Send button
        const sendBtn = document.getElementById('send-message-btn');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => {
                this.sendCurrentMessage();
            });
        }
    }
    
    sendCurrentMessage() {
        const input = document.getElementById('message-input');
        if (!input || !input.value.trim() || !this.currentChatId) return;
        
        const message = input.value.trim();
        const receiverId = this.getReceiverId();
        
        if (this.sendMessage(this.currentChatId, receiverId, message)) {
            input.value = '';
            this.stopTyping(this.currentChatId, receiverId);
        }
    }
    
    getReceiverId() {
        const chatItem = document.querySelector(`.chat-item[data-chat-id="${this.currentChatId}"]`);
        return chatItem ? chatItem.dataset.receiverId : null;
    }
    
    // Utility methods
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatTime(timestamp) {
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
    
    truncate(text, length) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
    
    getAuthToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || null;
    }
    
    notifyCallbacks(event, data) {
        this.messageCallbacks.forEach(callback => {
            if (callback.event === event) {
                callback.fn(data);
            }
        });
    }
    
    on(event, callback) {
        this.messageCallbacks.push({ event, fn: callback });
    }
    
    showChatWindow() {
        document.getElementById('chat-window')?.classList.add('active');
        document.getElementById('chat-list-container')?.classList.remove('active');
    }
}

// Initialize chat client
const chat = new ChatClient();