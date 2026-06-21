<?php
// views/auth/register.php
?>

<div class="auth-page">
    <div class="auth-container-modern">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🌱</div>
                <h1>Join ReCircle</h1>
                <p>Start your sustainable journey today</p>
            </div>
            
            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
                    <?php unset($_SESSION['flash']['error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/register/store" method="POST" class="auth-form">
                <div class="form-group-modern">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="John Doe" 
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group-modern">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="johndoe"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group-modern">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group-modern">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Min 6 characters">
                </div>
                
                <div class="form-group-modern">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <div class="form-group-modern">
                    <label>I want to:</label>
                    <div class="role-selector">
                        <label class="role-option-modern">
                            <input type="radio" name="role" value="buyer" checked>
                            <div class="role-content">
                                <span class="role-icon">🛍️</span>
                                <strong>Buy</strong>
                                <small>Browse & purchase items</small>
                            </div>
                        </label>
                        <label class="role-option-modern">
                            <input type="radio" name="role" value="seller">
                            <div class="role-content">
                                <span class="role-icon">📦</span>
                                <strong>Sell</strong>
                                <small>List & sell your items</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-auth-primary">Create Account</button>
            </form>
            
            <div class="auth-footer">
                Already have an account? <a href="/login">Sign in</a>
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
    max-width: 480px;
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

/* Role Selector */
.role-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-sm);
    margin-top: var(--space-xs);
}

.role-option-modern {
    display: block;
    padding: var(--space-md);
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius-md);
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.role-option-modern:hover {
    border-color: var(--primary);
    background: var(--gray-50);
}

.role-option-modern input[type="radio"] {
    display: none;
}

.role-option-modern:has(input:checked) {
    border-color: var(--primary);
    background: #E6FFFA;
}

.role-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.role-icon {
    font-size: 24px;
}

.role-content strong {
    font-size: var(--font-size-sm);
    color: var(--gray-700);
}

.role-content small {
    font-size: var(--font-size-xs);
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
    
    .role-selector {
        grid-template-columns: 1fr;
    }
}
</style>