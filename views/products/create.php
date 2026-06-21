<?php
// views/products/create.php

$categories = $categories ?? [];
$errors = $_SESSION['flash']['errors'] ?? [];
$old = $_SESSION['flash']['old'] ?? [];
unset($_SESSION['flash']['errors']);
unset($_SESSION['flash']['old']);
?>

<div class="product-form-container">
    <h1>Create New Listing</h1>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
    <?php endif; ?>
    
    <form action="/product/store" method="POST" enctype="multipart/form-data" class="product-form">
        <div class="form-group">
            <label for="title">Product Title *</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?php echo htmlspecialchars($old['title'] ?? ''); ?>" required>
            <?php if (isset($errors['title'])): ?>
                <span class="error-text"><?php echo $errors['title'][0]; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo (isset($old['category_id']) && $old['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category_id'])): ?>
                <span class="error-text"><?php echo $errors['category_id'][0]; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" class="form-control" rows="6" required><?php 
                echo htmlspecialchars($old['description'] ?? ''); 
            ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <span class="error-text"><?php echo $errors['description'][0]; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" id="price" name="price" class="form-control" 
                       value="<?php echo htmlspecialchars($old['price'] ?? ''); ?>" 
                       step="0.01" min="0.01" required>
                <?php if (isset($errors['price'])): ?>
                    <span class="error-text"><?php echo $errors['price'][0]; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="condition">Condition *</label>
                <select id="condition" name="condition" class="form-control" required>
                    <option value="new" <?php echo (isset($old['condition']) && $old['condition'] == 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="used" <?php echo (isset($old['condition']) && $old['condition'] == 'used') ? 'selected' : ''; ?>>Used</option>
                    <option value="refurbished" <?php echo (isset($old['condition']) && $old['condition'] == 'refurbished') ? 'selected' : ''; ?>>Refurbished</option>
                    <option value="damaged" <?php echo (isset($old['condition']) && $old['condition'] == 'damaged') ? 'selected' : ''; ?>>Damaged</option>
                </select>
                <?php if (isset($errors['condition'])): ?>
                    <span class="error-text"><?php echo $errors['condition'][0]; ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="location">Location (City)</label>
            <input type="text" id="location" name="location" class="form-control" 
                   value="<?php echo htmlspecialchars($old['location'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_negotiable" value="1" 
                       <?php echo (isset($old['is_negotiable']) && $old['is_negotiable']) ? 'checked' : 'checked'; ?>>
                Allow Negotiation (Buyers can make offers)
            </label>
        </div>
        
        <div class="form-group">
            <label for="images">Product Images (Max 5 images)</label>
            <input type="file" id="images" name="images[]" class="form-control" 
                   accept="image/*" multiple>
            <small class="help-text">You can select multiple images. First image will be the primary.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Listing</button>
            <a href="/dashboard/seller" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.product-form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 0;
}

.product-form-container h1 {
    margin-bottom: 24px;
}

.product-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

.form-control.error {
    border-color: #ea4335;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: normal;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.help-text {
    display: block;
    margin-top: 4px;
    color: #666;
    font-size: 13px;
}

.error-text {
    color: #ea4335;
    font-size: 13px;
    display: block;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #0654ba;
    color: white;
}

.btn-primary:hover {
    background: #004b9a;
}

.btn-secondary {
    background: #f0f0f0;
    color: #333;
}

.btn-secondary:hover {
    background: #e0e0e0;
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
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>