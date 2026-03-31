<?php include '../app/views/layouts/header.php'; ?>
<?php
// load UserModel to fetch current user's avatar for composer
require_once '../app/models/usermodel.php';
$currentAvatar = null;
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['user_id']) && isset($db)) {
    $um = new UserModel($db);
    $me = $um->findById($_SESSION['user_id']);
    if (!empty($me) && !empty($me['avatar'])) $currentAvatar = $me['avatar'];
}
?>

<div class="max-w-2xl mx-auto mt-8 px-4">
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!empty($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>
    
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 transition-all hover:shadow-md">
        <div class="flex items-center space-x-4 mb-4">
            <?php if (!empty($currentAvatar)): ?>
                <img src="assets/uploads/avatars/<?php echo htmlspecialchars($currentAvatar); ?>" alt="avatar" class="h-10 w-10 rounded-full object-cover shadow-inner">
            <?php else: ?>
                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-inner">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            <h2 class="text-lg font-semibold text-gray-700">What's on your mind, <?php echo htmlspecialchars($_SESSION['username']); ?>?</h2>
        </div>
        <form action="?url=posts/store" method="POST" enctype="multipart/form-data">
            <textarea 
                name="content" 
                rows="3" 
                class="w-full p-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all resize-none" 
                placeholder="Share an update with your friends..." 
                required></textarea>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3 items-center">
                <!-- Hidden file inputs (allow multiple) -->
                <input id="post-image-input" type="file" name="images[]" accept="image/*" class="hidden" multiple>
                <input id="post-video-input" type="file" name="videos[]" accept="video/*" class="hidden" multiple>

                <div class="col-span-4 mt-2">
                    <div id="post-preview" class="flex flex-wrap gap-3"></div>
                </div>

                <div class="col-span-1 flex items-center">
                    <button id="post-image-btn" type="button" class="p-2 rounded hover:bg-gray-100" title="Upload image">
                        <!-- image SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M8 7l4 4 4-4M4 7h16" />
                        </svg>
                    </button>
                </div>

                <div class="col-span-1 flex items-center">
                    <button id="post-video-btn" type="button" class="p-2 rounded hover:bg-gray-100" title="Upload video">
                        <!-- video SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14v-4zM4 6h8v12H4z" />
                        </svg>
                    </button>
                </div>

                <div class="col-span-1 flex space-x-0">
                    <select name="currency" class="p-2 border border-gray-200 rounded-l focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white w-2/5">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="PHP">PHP</option>
                    </select>
                    <input type="number" step="0.01" min="0" name="price" placeholder="Price" class="p-2 border border-gray-200 border-l-0 rounded-r focus:ring-2 focus:ring-blue-500 focus:outline-none w-3/5" />
                </div>

                <div class="col-span-1 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-transform active:scale-95 shadow-md">
                        Post Update
                    </button>
                </div>
            </div>
        </form>
        </div>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">Recent Activity</h3>
        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Global Feed</span>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-20 bg-white rounded-xl border-2 border-dashed border-gray-200">
            <p class="text-gray-400 italic">No posts yet. Be the first to say something!</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card p-6 mb-6 bg-white border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <?php if (!empty($post['avatar'])): ?>
                            <a href="?url=profile&id=<?php echo $post['user_id']; ?>">
                                <img src="assets/uploads/avatars/<?php echo htmlspecialchars($post['avatar']); ?>" alt="avatar" class="h-12 w-12 rounded-full object-cover shadow-sm mr-4">
                            </a>
                        <?php else: ?>
                            <a href="?url=profile&id=<?php echo $post['user_id']; ?>">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm mr-4">
                                    <?php echo strtoupper(substr($post['full_name'], 0, 1)); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        <div>
                            <div class="font-bold text-gray-900 leading-tight">
                                <a href="?url=profile&id=<?php echo $post['user_id']; ?>" class="hover:underline"><?php echo htmlspecialchars($post['full_name']); ?></a>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                <span class="font-medium text-blue-500">@<?php echo htmlspecialchars($post['username']); ?></span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span><?php echo date('M j, Y \a\t g:i a', strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="C5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </button>
                </div>

                <div class="text-gray-800 text-lg leading-relaxed mb-4 px-1">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <?php if (!empty($post['shared_post_id'])): 
                    $sp = $post['shared_post'] ?? null;
                    if ($sp):
                ?>
                    <div class="mt-3 border border-gray-300 rounded-xl overflow-hidden bg-gray-50 mb-4">
                        <!-- Media rendering for nested card -->
                        <?php
                            if (!empty($sp['media']) && is_array($sp['media'])):
                                echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-b border-gray-300 bg-black">';
                                foreach ($sp['media'] as $m):
                                    if ($m['media_type'] === 'image'):
                                        echo '<div><img src="assets/uploads/posts/' . htmlspecialchars($m['filename']) . '" class="w-full h-full object-cover max-h-96" /></div>';
                                    elseif ($m['media_type'] === 'video'):
                                        echo '<div><video controls class="w-full h-full object-cover max-h-96"><source src="assets/uploads/posts/' . htmlspecialchars($m['filename']) . '" type="video/mp4"></video></div>';
                                    endif;
                                endforeach;
                                echo '</div>';
                            else:
                                if (!empty($sp['image'])):
                                    echo '<div class="bg-black"><img src="assets/uploads/posts/' . htmlspecialchars($sp['image']) . '" class="w-full object-contain max-h-[500px] border-b border-gray-300 mx-auto" /></div>';
                                endif;
                                if (!empty($sp['video'])):
                                    echo '<video controls class="w-full max-h-96 bg-black border-b border-gray-300"><source src="assets/uploads/posts/' . htmlspecialchars($sp['video']) . '" type="video/mp4"></video>';
                                endif;
                            endif;
                        ?>

                        <!-- Nested Card Footer -->
                        <div class="p-4 bg-gray-100 dark:bg-[#242526] text-black">
                            <div class="font-bold text-gray-900 leading-tight">
                                <a href="?url=profile&id=<?php echo $sp['user_id']; ?>" class="hover:underline"><?php echo htmlspecialchars($sp['full_name']); ?></a>
                                <?php if (!empty($sp['content'])) { echo '<span class="font-normal text-gray-500"> wrote:</span>'; } ?>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">
                                <?php echo date('M j \a\t g:i A', strtotime($sp['created_at'])); ?> • <span title="Public">🌎</span>
                            </div>
                            
                            <?php if (!empty($sp['content'])): ?>
                                <div class="text-gray-800 text-sm leading-relaxed mb-2">
                                    <?php echo nl2br(htmlspecialchars($sp['content'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($sp['price'])): ?>
                                <div class="mt-2">
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-0.5 rounded text-sm font-semibold">Price: <?php echo htmlspecialchars($sp['currency'] ?? 'USD'); ?> <?php echo htmlspecialchars(number_format($sp['price'], 2)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-3 border border-gray-300 rounded-xl p-4 bg-gray-50 text-gray-500 text-center italic mb-4">
                        This content isn't available right now.
                    </div>
                <?php endif; endif; ?>

                <?php
                    // Render media from post_media (new) if present
                    if (!empty($post['media']) && is_array($post['media'])):
                        echo '<div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">';
                        foreach ($post['media'] as $m):
                            if ($m['media_type'] === 'image'):
                ?>
                                <div>
                                    <img src="assets/uploads/posts/<?php echo htmlspecialchars($m['filename']); ?>" alt="post image" class="w-full h-full object-cover rounded-lg aspect-square" />
                                </div>
                <?php
                            elseif ($m['media_type'] === 'video'):
                ?>
                                <div>
                                    <video controls class="w-full h-full object-cover rounded-lg aspect-square bg-black">
                                        <source src="assets/uploads/posts/<?php echo htmlspecialchars($m['filename']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                <?php
                            endif;
                        endforeach;
                        echo '</div>';
                    else:
                        // fallback to legacy single columns
                        if (!empty($post['image'])): ?>
                            <div class="mb-4">
                                <img src="assets/uploads/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="post image" class="w-full rounded-lg" />
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($post['video'])): ?>
                            <div class="mb-4">
                                <video controls class="w-full rounded-lg">
                                    <source src="assets/uploads/posts/<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php endif;
                    endif;
                ?>

                <?php if (!empty($post['price'])): ?>
                    <div class="mb-3">
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded font-semibold">Price: <?php echo htmlspecialchars($post['currency'] ?? 'USD'); ?> <?php echo htmlspecialchars(number_format($post['price'], 2)); ?></span>
                    </div>
                <?php endif; ?>

                <div class="border-t border-gray-100 pt-3 mt-4 text-sm">
                    <div class="flex items-center justify-between text-gray-500 font-semibold mb-3">
                        <div class="flex items-center space-x-4">
                            <form action="?url=likes/toggle" method="POST" style="display:inline">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                    <span class="group-hover:text-blue-600">👍 Like</span>
                                </button>
                            </form>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                <form action="index.php" method="GET" style="display:inline">
                                    <input type="hidden" name="url" value="posts/edit">
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                        <span class="group-hover:text-blue-600">✏️ Edit</span>
                                    </button>
                                </form>

                                <form action="?url=posts/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?');">
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                        <span class="group-hover:text-red-600">🗑️ Delete</span>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form action="?url=comments/store" method="POST" style="display:inline">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <input type="hidden" name="comment" value=""> <!-- placeholder for JS-enabled quick comment -->
                                <button type="button" onclick="document.getElementById('comment-box-<?php echo $post['id']; ?>').classList.toggle('hidden')" class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                    <span class="group-hover:text-blue-600">💬 Comment</span>
                                </button>
                            </form>


                        </div>
                        <div class="text-gray-400">Likes: <?php
                            // show like count if model exists
                            if (class_exists('LikeModel')) {
                                $lm = new LikeModel($db);
                                echo $lm->countLikes($post['id']);
                            } else { echo '0'; }
                        ?></div>
                    </div>

                    <div id="comment-box-<?php echo $post['id']; ?>" class="hidden">
                        <form action="?url=comments/store" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="comment" rows="2" class="w-full p-2 border rounded mb-2" placeholder="Write a comment..."></textarea>
                            <div class="text-right">
                                <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Comment</button>
                            </div>
                        </form>
                        <?php if (class_exists('CommentModel')): 
                            $cm = new CommentModel($db);
                            $comments = $cm->getCommentsByPost($post['id']);
                            if (!empty($comments)): ?>
                                <div class="mt-3 space-y-3">
                                    <?php foreach ($comments as $c): ?>
                                        <div class="bg-gray-50 p-3 rounded flex justify-between items-start">
                                            <div>
                                                <div class="font-semibold"><a href="?url=profile&id=<?php echo $c['user_id']; ?>" class="hover:underline"><?php echo htmlspecialchars($c['full_name']); ?></a></div>
                                                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($c['content']); ?></div>
                                            </div>
                                            <div class="text-sm">
                                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                                                    <a href="?url=comments/edit&id=<?php echo $c['id']; ?>" class="text-blue-600 mr-2">Edit</a>
                                                    <form action="?url=comments/delete" method="POST" style="display:inline" onsubmit="return confirm('Delete this comment?');">
                                                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                        <button type="submit" class="text-red-600">Delete</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; 
                        endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php include '../app/views/layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var imgBtn = document.getElementById('post-image-btn');
    var vidBtn = document.getElementById('post-video-btn');
    var imgInput = document.getElementById('post-image-input');
    var vidInput = document.getElementById('post-video-input');
    var preview = document.getElementById('post-preview');

    var currentImages = new DataTransfer();
    var currentVideos = new DataTransfer();

    if(imgBtn && imgInput) imgBtn.addEventListener('click', function(){ imgInput.click(); });
    if(vidBtn && vidInput) vidBtn.addEventListener('click', function(){ vidInput.click(); });

    function createPreview(file, kind, index) {
        if (!file) return null;
        var wrapper = document.createElement('div');
        wrapper.className = 'relative inline-block mr-3 mb-3 align-top';

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'absolute right-1 top-1 bg-black bg-opacity-60 text-white rounded-full p-1 shadow hover:bg-opacity-80 transition';
        removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 18L18 6"></path><path d="M6 6l12 12"></path></svg>';
        removeBtn.title = 'Remove ' + kind;
        removeBtn.addEventListener('click', function(e){
            e.preventDefault();
            if (kind === 'image') {
                var newDt = new DataTransfer();
                for(var i=0; i<currentImages.files.length; i++){
                    if(i !== index) newDt.items.add(currentImages.files[i]);
                }
                currentImages = newDt;
                imgInput.files = currentImages.files;
            } else {
                var newDt = new DataTransfer();
                for(var i=0; i<currentVideos.files.length; i++){
                    if(i !== index) newDt.items.add(currentVideos.files[i]);
                }
                currentVideos = newDt;
                vidInput.files = currentVideos.files;
            }
            renderPreviews();
        });

        if (kind === 'image') {
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-32 h-32 rounded-lg object-cover border border-gray-200 shadow-sm';
            img.onload = function(){ URL.revokeObjectURL(this.src); };
            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            return wrapper;
        }

        var video = document.createElement('video');
        video.controls = true;
        video.className = 'w-48 h-32 rounded-lg object-cover bg-black border border-gray-200 shadow-sm';
        var src = document.createElement('source');
        src.src = URL.createObjectURL(file);
        src.type = file.type || 'video/mp4';
        video.appendChild(src);
        video.onloadeddata = function(){ URL.revokeObjectURL(src.src); };
        wrapper.appendChild(video);
        wrapper.appendChild(removeBtn);
        return wrapper;
    }

    function renderPreviews() {
        if (!preview) return;
        preview.innerHTML = '';
        for (var i = 0; i < currentImages.files.length; i++) {
            var el = createPreview(currentImages.files[i], 'image', i);
            if (el) preview.appendChild(el);
        }
        for (var j = 0; j < currentVideos.files.length; j++) {
            var el = createPreview(currentVideos.files[j], 'video', j);
            if (el) preview.appendChild(el);
        }
    }

    if (imgInput) {
        imgInput.addEventListener('change', function(){
            for(var i=0; i<this.files.length; i++){
                var exists = false;
                for(var j=0; j<currentImages.files.length; j++){
                    if(currentImages.files[j].name === this.files[i].name && currentImages.files[j].size === this.files[i].size) exists = true;
                }
                if(!exists) currentImages.items.add(this.files[i]);
            }
            this.files = currentImages.files;
            renderPreviews();
        });
    }
    if (vidInput) {
        vidInput.addEventListener('change', function(){
            for(var i=0; i<this.files.length; i++){
                var exists = false;
                for(var j=0; j<currentVideos.files.length; j++){
                    if(currentVideos.files[j].name === this.files[i].name && currentVideos.files[j].size === this.files[i].size) exists = true;
                }
                if(!exists) currentVideos.items.add(this.files[i]);
            }
            this.files = currentVideos.files;
            renderPreviews();
        });
    }
});
</script>