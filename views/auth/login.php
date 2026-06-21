<?php
// views/auth/login.php
?>

<div class="auth-page">
    <div class="auth-container-modern">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">♻️</div>
                <h1>Welcome Back</h1>
                <p>Sign in to continue your sustainable journey</p>
            </div>
            
            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
                    <?php unset($_SESSION['flash']['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['flash']['success']); ?>
                    <?php unset($_SESSION['flash']['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/authenticate" method="POST" class="auth-form">
                <div class="form-group-modern">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>
                
                <div class="form-group-modern">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn-auth-primary">Sign In</button>
            </form>
            
            <div class="auth-footer">
                Don't have an account? <a href="/register">Sign up</a>
            </div>
            
            <div class="demo-accounts">
                <p><strong>Demo Accounts:</strong></p>
                <div class="demo-grid">
                    <div><strong>Admin:</strong> admin@marketplace.com<br><span>admin123</span></div>
                    <div><strong>Seller:</strong> seller@marketplace.com<br><span>seller123</span></div>
                    <div><strong>Buyer:</strong> buyer@marketplace.com<br><span>buyer123</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Auth Page Styles */
.auth-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-xl) var(--space-md);
    background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
}

.auth-container-modern {
    width: 100%;
    max-width: 440px;
    animation: fadeInUp 0.6s ease-out;
}

.auth-card {
    background: var(--white);
    border-radius: var(--border-radius-xl);
    padding: var(--space-2xl);
    box-shadow: var(--shadow-xl);
}

.auth-header {
    text-align: center;
    margin-bottom: var(--space-xl);
}

.auth-icon {
    font-size: 48px;
    margin-bottom: var(--space-sm);
}

.auth-header h1 {
    font-size: var(--font-size-2xl);
    font-weight: 800;
    margin-bottom: var(--space-xs);
    color: var(--gray-800);
}

.auth-header p {
    color: var(--gray-500);
    font-size: var(--font-size-sm);
}

.alert {
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--border-radius-md);
    margin-bottom: var(--space-lg);
    font-size: var(--font-size-sm);
}

.alert-error {
    background: #FED7D7;
    color: #742A2A;
    border: 1px solid #FECACA;
}

.alert-success {
    background: #C6F6D5;
    color: #22543D;
    border: 1px solid #9AE6B4;
}

.auth-form .form-group-modern {
    margin-bottom: var(--space-lg);
}

.auth-form .form-group-modern label {
    display: block;
    margin-bottom: var(--space-xs);
    font-weight: 600;
    color: var(--gray-700);
    font-size: var(--font-size-sm);
}

.auth-form .form-group-modern input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-md);
    transition: all 0.3s;
    background: var(--gray-50);
    font-family: var(--font-family);
}

.auth-form .form-group-modern input:focus {
    outline: none;
    border-color: var(--primary);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(44, 122, 123, 0.1);
}

.auth-form .form-group-modern input::placeholder {
    color: var(--gray-400);
}

.btn-auth-primary {
    width: 100%;
    padding: 14px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-md);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-family: var(--font-family);
}

.btn-auth-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.auth-footer {
    text-align: center;
    margin-top: var(--space-lg);
    color: var(--gray-500);
    font-size: var(--font-size-sm);
}

.auth-footer a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.demo-accounts {
    margin-top: var(--space-xl);
    padding: var(--space-md) var(--space-lg);
    background: var(--gray-50);
    border-radius: var(--border-radius-md);
    font-size: var(--font-size-sm);
}

.demo-accounts p {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--space-sm);
}

.demo-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: var(--space-sm);
    font-size: var(--font-size-xs);
}

.demo-grid div span {
    color: var(--gray-500);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .auth-card {
        padding: var(--space-lg);
    }
    
    .demo-grid {
        grid-template-columns: 1fr;
        gap: var(--space-xs);
    }
}
</style>