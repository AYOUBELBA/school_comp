<?php
// views/auth/login.php
?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Login to MarketPlace</h1>
        
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
                <?php unset($_SESSION['flash']['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/authenticate" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="auth-links">
            Don't have an account? <a href="/register">Register here</a>
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
    max-width: 400px;
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

.demo-credentials {
    margin-top: 20px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 14px;
}

.demo-credentials p {
    margin: 4px 0;
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
</style>