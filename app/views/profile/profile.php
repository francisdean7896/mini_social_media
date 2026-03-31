<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-3xl mx-auto mt-8">
    <?php if (session_status() !== PHP_SESSION_ACTIVE)
        session_start(); ?>
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-800 rounded">
            <?php echo htmlspecialchars($_SESSION['flash']);
            unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center space-x-6">
            <div>
                <?php if (!empty($user['avatar'])): ?>
                    <img src="assets/uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="avatar"
                        class="h-24 w-24 rounded-full object-cover shadow" />
                <?php else: ?>
                    <div
                        class="h-24 w-24 rounded-full avatar-gradient flex items-center justify-center text-white text-3xl font-bold">
                        <?php echo isset($user['full_name']) ? strtoupper(substr($user['full_name'], 0, 1)) : 'U'; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-extrabold text-gray-900">
                        <?php echo htmlspecialchars($user['full_name'] ?? 'Unknown'); ?></h1>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                        <a href="?url=profile/edit"
                            class="ml-3 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit Profile</a>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-500">@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></p>
                <p class="mt-2 text-gray-600">
                    <?php echo htmlspecialchars($user['bio'] ?? 'Member profile — basic info and recent posts.'); ?></p>
                <div class="mt-4 text-sm text-gray-700">
                    <?php if (!empty($user['street']) || !empty($user['barangay']) || !empty($user['city']) || !empty($user['province']) || !empty($user['country']) || !empty($user['postal_code'])): ?>
                        <div class="font-semibold">Address</div>
                        <div>
                            <?php
                            $parts = [];
                            if (!empty($user['street']))
                                $parts[] = htmlspecialchars($user['street']);
                            if (!empty($user['barangay']))
                                $parts[] = htmlspecialchars($user['barangay']);
                            if (!empty($user['city']))
                                $parts[] = htmlspecialchars($user['city']);
                            if (!empty($user['province']))
                                $parts[] = htmlspecialchars($user['province']);
                            if (!empty($user['country']))
                                $parts[] = htmlspecialchars($user['country']);
                            if (!empty($user['postal_code']))
                                $parts[] = htmlspecialchars($user['postal_code']);
                            echo implode(', ', $parts);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($user['phone'])): ?>
                        <div class="mt-2"><span class="font-semibold">Phone:</span>
                            <?php echo htmlspecialchars($user['phone']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($user['birthdate'])): ?>
                        <div class="mt-1"><span class="font-semibold">Birthdate:</span>
                            <?php echo date('F j, Y', strtotime($user['birthdate'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-bold">Recent Posts</h2>
        <a href="?url=newsfeed" class="text-blue-600 hover:underline">Back to Feed</a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
            <p class="text-gray-400">No posts yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card p-6 mb-6 bg-white border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <?php if (!empty($post['avatar'])): ?>
                            <a href="?url=profile&id=<?php echo $post['user_id']; ?>">
                                <img src="assets/uploads/avatars/<?php echo htmlspecialchars($post['avatar']); ?>" alt="avatar"
                                    class="h-12 w-12 rounded-full object-cover shadow-sm mr-4">
                            </a>
                        <?php else: ?>
                            <a href="?url=profile&id=<?php echo $post['user_id']; ?>">
                                <div
                                    class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm mr-4">
                                    <?php echo strtoupper(substr($post['full_name'], 0, 1)); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        <div>
                            <div class="font-bold text-gray-900 leading-tight">
                                <a href="?url=profile&id=<?php echo $post['user_id']; ?>"
                                    class="hover:underline"><?php echo htmlspecialchars($post['full_name']); ?></a>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                <span
                                    class="font-medium text-blue-500">@<?php echo htmlspecialchars($post['username']); ?></span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span><?php echo date('M j, Y \a\t g:i a', strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-gray-800 text-lg leading-relaxed mb-4 px-1">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <?php
                // Render media from post_media (new) if present
                if (!empty($post['media']) && is_array($post['media'])):
                    foreach ($post['media'] as $m):
                        if ($m['media_type'] === 'image'):
                            ?>
                            <div class="mb-4">
                                <img src="assets/uploads/posts/<?php echo htmlspecialchars($m['filename']); ?>" alt="post image"
                                    class="w-full rounded-lg" />
                            </div>
                            <?php
                        elseif ($m['media_type'] === 'video'):
                            ?>
                            <div class="mb-4">
                                <video controls class="w-full rounded-lg">
                                    <source src="assets/uploads/posts/<?php echo htmlspecialchars($m['filename']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <?php
                        endif;
                    endforeach;
                else:
                    // fallback to legacy single columns
                    if (!empty($post['image'])): ?>
                        <div class="mb-4">
                            <img src="assets/uploads/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="post image"
                                class="w-full rounded-lg" />
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
                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded font-semibold">Price:
                            <?php echo htmlspecialchars($post['currency'] ?? 'USD'); ?>
                            <?php echo htmlspecialchars(number_format($post['price'], 2)); ?></span>
                    </div>
                <?php endif; ?>

                <div class="border-t border-gray-100 pt-3 mt-4 text-sm">
                    <div class="flex items-center justify-between text-gray-500 font-semibold mb-3">
                        <div class="flex items-center space-x-4">
                            <form action="?url=likes/toggle" method="POST" style="display:inline">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit"
                                    class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                    <span class="group-hover:text-blue-600">👍 Like</span>
                                </button>
                            </form>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                <form action="index.php" method="GET" style="display:inline">
                                    <input type="hidden" name="url" value="posts/edit">
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                    <button type="submit"
                                        class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                        <span class="group-hover:text-blue-600">✏️ Edit</span>
                                    </button>
                                </form>

                                <form action="?url=posts/delete" method="POST" style="display:inline"
                                    onsubmit="return confirm('Delete this post?');">
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                    <button type="submit"
                                        class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                        <span class="group-hover:text-red-600">🗑️ Delete</span>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form action="?url=comments/store" method="POST" style="display:inline">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <input type="hidden" name="comment" value="">
                                <button type="button"
                                    onclick="document.getElementById('comment-box-<?php echo $post['id']; ?>').classList.toggle('hidden')"
                                    class="flex items-center space-x-2 hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors group">
                                    <span class="group-hover:text-blue-600">💬 Comment</span>
                                </button>
                            </form>


                        </div>
                        <div class="text-gray-400">Likes: <?php
                        if (class_exists('LikeModel')) {
                            $lm = new LikeModel($db);
                            echo $lm->countLikes($post['id']);
                        } else {
                            echo '0';
                        }
                        ?></div>
                    </div>

                    <div id="comment-box-<?php echo $post['id']; ?>" class="hidden">
                        <form action="?url=comments/store" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="comment" rows="2" class="w-full p-2 border rounded mb-2"
                                placeholder="Write a comment..."></textarea>
                            <div class="text-right">
                                <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php include '../app/views/layouts/footer.php'; ?>