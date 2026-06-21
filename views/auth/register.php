<?php
// views/auth/register.php
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Create Account</h1>
        
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
                <?php unset($_SESSION['flash']['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/register/store" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>I want to:</label>
                <div class="role-selection">
                    <label class="role-option">
                        <input type="radio" name="role" value="buyer" checked>
                        <span class="role-label">
                            <strong>🛍️ Buy</strong>
                            <small>Browse and purchase items</small>
                        </span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="seller">
                        <span class="role-label">
                            <strong>📦 Sell</strong>
                            <small>List and sell your items</small>
                        </span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        
        <p class="auth-links">
            Already have an account? <a href="/login">Login here</a>
        </p>
    </div>
</div>

<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 20px;
}

.auth-box {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    max-width: 450px;
    width: 100%;
}

.auth-box h1 {
    margin-bottom: 24px;
    text-align: center;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #555;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #0654ba;
}

.role-selection {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 8px;
}

.role-option {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.role-option:hover {
    border-color: #0654ba;
    background: #f8f9fa;
}

.role-option input[type="radio"] {
    margin-right: 8px;
}

.role-option input[type="radio"]:checked + .role-label {
    color: #0654ba;
}

.role-option:has(input:checked) {
    border-color: #0654ba;
    background: #e8f0fe;
}

.role-label {
    display: flex;
    flex-direction: column;
}

.role-label strong {
    font-size: 16px;
}

.role-label small {
    font-size: 12px;
    color: #666;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #0654ba;
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: #004b9a;
}

.btn-block {
    width: 100%;
}

.auth-links {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

.auth-links a {
    color: #0654ba;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .role-selection {
        grid-template-columns: 1fr;
    }
}
</style>