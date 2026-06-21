<?php
// views/products/edit.php

$product = $product ?? [];
$categories = $categories ?? [];
$images = $images ?? [];
$errors = $_SESSION['flash']['errors'] ?? [];
$old = $_SESSION['flash']['old'] ?? [];
unset($_SESSION['flash']['errors']);
unset($_SESSION['flash']['old']);
?>

<div class="product-form-container">
    <h1>Edit Listing</h1>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
    <?php endif; ?>
    
    <form action="/product/update/<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data" class="product-form">
        <div class="form-group">
            <label for="title">Product Title *</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" 
                            <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" class="form-control" rows="6" required><?php 
                echo htmlspecialchars($product['description'] ?? ''); 
            ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" id="price" name="price" class="form-control" 
                       value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" 
                       step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="condition">Condition *</label>
                <select id="condition" name="condition" class="form-control" required>
                    <option value="new" <?php echo ($product['condition'] == 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="used" <?php echo ($product['condition'] == 'used') ? 'selected' : ''; ?>>Used</option>
                    <option value="refurbished" <?php echo ($product['condition'] == 'refurbished') ? 'selected' : ''; ?>>Refurbished</option>
                    <option value="damaged" <?php echo ($product['condition'] == 'damaged') ? 'selected' : ''; ?>>Damaged</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="location">Location (City)</label>
            <input type="text" id="location" name="location" class="form-control" 
                   value="<?php echo htmlspecialchars($product['location'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_negotiable" value="1" 
                       <?php echo ($product['is_negotiable']) ? 'checked' : ''; ?>>
                Allow Negotiation (Buyers can make offers)
            </label>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="active" <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                <option value="sold" <?php echo ($product['status'] == 'sold') ? 'selected' : ''; ?>>Sold</option>
            </select>
        </div>
        
        <!-- Current Images -->
        <?php if (!empty($images)): ?>
            <div class="form-group">
                <label>Current Images</label>
                <div class="current-images">
                    <?php foreach ($images as $image): ?>
                        <div class="image-item">
                            <img src="/uploads/products/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="Product image">
                            <span class="image-label"><?php echo $image['is_primary'] ? 'Primary' : ''; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="images">Add New Images (Max 5 total)</label>
            <input type="file" id="images" name="images[]" class="form-control" 
                   accept="image/*" multiple>
            <small class="help-text">You can select multiple images. First image will be the primary.</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Listing</button>
            <a href="/product/<?php echo $product['id']; ?>" class="btn btn-secondary">Cancel</a>
            <a href="/product/delete/<?php echo $product['id']; ?>" class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
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

.current-images {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.image-item {
    position: relative;
    width: 100px;
    height: 100px;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(6, 84, 186, 0.8);
    color: white;
    font-size: 11px;
    text-align: center;
    padding: 2px;
}

.help-text {
    display: block;
    margin-top: 4px;
    color: #666;
    font-size: 13px;
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

.btn-danger {
    background: #ea4335;
    color: white;
}

.btn-danger:hover {
    background: #d33426;
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