<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-xl border border-gray-200">
    <h2 class="text-xl font-bold mb-4">Edit Post</h2>

    <form action="?url=posts/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($post['id']); ?>">
        <label class="block mb-2 font-medium">Content</label>
        <textarea name="content" rows="4" class="w-full p-2 mb-4 border rounded"><?php echo htmlspecialchars($post['content']); ?></textarea>

        <label class="block mb-2 font-medium">Replace Image</label>
        <?php if (!empty($post['image'])): ?>
            <div class="mb-2 relative inline-block" id="current-image-wrap">
                <img src="assets/uploads/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="current image" class="w-48 rounded-lg mb-2" id="current-image-preview">
                <button type="button" id="remove-image-btn" class="absolute top-0 right-0 mt-1 mr-1 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-2 shadow text-white" aria-label="Remove image" title="Remove image">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 18L18 6"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="text-sm text-gray-600 mb-3">Current image</div>
                <input type="hidden" name="remove_image" id="remove-image-input" value="0">
            </div>
        <?php endif; ?>
        <div class="mb-4 flex items-center space-x-3">
            <input id="edit-image-input" type="file" name="image" accept="image/*" class="hidden">
            <button id="edit-image-btn" type="button" class="p-2 rounded hover:bg-gray-100 flex items-center" title="Choose image">
                <!-- image icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M8 7l4 4 4-4M4 7h16" />
                </svg>
            </button>
            <span id="edit-image-filename" class="text-sm text-gray-600">No file chosen</span>
        </div>

        <label class="block mb-2 font-medium">Replace Video</label>
        <?php if (!empty($post['video'])): ?>
            <div class="mb-2 relative inline-block" id="current-video-wrap">
                <video controls class="w-64 rounded-lg mb-2" id="current-video-preview"><source src="assets/uploads/posts/<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">Your browser does not support video.</video>
                <button type="button" id="remove-video-btn" class="absolute top-0 right-0 mt-1 mr-1 bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-2 shadow text-white" aria-label="Remove video" title="Remove video">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 18L18 6"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="text-sm text-gray-600 mb-3">Current video</div>
                <input type="hidden" name="remove_video" id="remove-video-input" value="0">
            </div>
        <?php endif; ?>
        <div class="mb-4 flex items-center space-x-3">
            <input id="edit-video-input" type="file" name="video" accept="video/*" class="hidden">
            <button id="edit-video-btn" type="button" class="p-2 rounded hover:bg-gray-100 flex items-center" title="Choose video">
                <!-- video icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14v-4zM4 6h8v12H4z" />
                </svg>
            </button>
            <span id="edit-video-filename" class="text-sm text-gray-600">No file chosen</span>
        </div>

        <label class="block mb-2 font-medium">Price</label>
        <div class="flex space-x-0 mb-4">
            <select name="currency" class="p-2 border border-gray-200 rounded-l focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white w-1/4">
                <option value="USD" <?php echo (isset($post['currency']) && $post['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                <option value="EUR" <?php echo (isset($post['currency']) && $post['currency'] === 'EUR') ? 'selected' : ''; ?>>EUR</option>
                <option value="PHP" <?php echo (isset($post['currency']) && $post['currency'] === 'PHP') ? 'selected' : ''; ?>>PHP</option>
            </select>
            <input type="number" step="0.01" min="0" name="price" value="<?php echo htmlspecialchars($post['price'] ?? ''); ?>" class="w-3/4 p-2 border border-gray-200 border-l-0 rounded-r focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex justify-end">
            <a href="?url=newsfeed" class="mr-3 text-gray-600">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>
</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var imgBtn = document.getElementById('edit-image-btn');
    var vidBtn = document.getElementById('edit-video-btn');
    var imgInput = document.getElementById('edit-image-input');
    var vidInput = document.getElementById('edit-video-input');
    var imgName = document.getElementById('edit-image-filename');
    var vidName = document.getElementById('edit-video-filename');
    if(imgBtn && imgInput) imgBtn.addEventListener('click', function(){ imgInput.click(); });
    if(vidBtn && vidInput) vidBtn.addEventListener('click', function(){ vidInput.click(); });
    if(imgInput) imgInput.addEventListener('change', function(){ imgName.textContent = imgInput.files && imgInput.files[0] ? imgInput.files[0].name : 'No file chosen'; });
    if(vidInput) vidInput.addEventListener('change', function(){ vidName.textContent = vidInput.files && vidInput.files[0] ? vidInput.files[0].name : 'No file chosen'; });

    // Remove overlay buttons
    var removeImageBtn = document.getElementById('remove-image-btn');
    var removeVideoBtn = document.getElementById('remove-video-btn');
    var removeImageInput = document.getElementById('remove-image-input');
    var removeVideoInput = document.getElementById('remove-video-input');
    var currentImageWrap = document.getElementById('current-image-wrap');
    var currentVideoWrap = document.getElementById('current-video-wrap');
    if(removeImageBtn && removeImageInput) {
        removeImageBtn.addEventListener('click', function(){
            removeImageInput.value = '1';
            if(currentImageWrap) currentImageWrap.style.display = 'none';
            // clear any selected file
            if(imgInput) { imgInput.value = ''; imgName.textContent = 'No file chosen'; }
        });
    }
    if(removeVideoBtn && removeVideoInput) {
        removeVideoBtn.addEventListener('click', function(){
            removeVideoInput.value = '1';
            if(currentVideoWrap) currentVideoWrap.style.display = 'none';
            if(vidInput) { vidInput.value = ''; vidName.textContent = 'No file chosen'; }
        });
    }

    // If user selects a new file, cancel the remove flag
    if(imgInput) imgInput.addEventListener('change', function(){ if(removeImageInput) removeImageInput.value = '0'; });
    if(vidInput) vidInput.addEventListener('change', function(){ if(removeVideoInput) removeVideoInput.value = '0'; });
});
</script>
