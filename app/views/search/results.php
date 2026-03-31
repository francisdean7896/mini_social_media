<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-3xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Search results for "<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"</h2>

    <div class="bg-white p-4 rounded mb-6">
        <h3 class="font-semibold mb-2">Users</h3>
        <?php if (empty($users)): ?>
            <div class="text-gray-500">No users found.</div>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <div class="py-2 border-b last:border-b-0">
                    <a href="?url=profile&id=<?php echo $u['id']; ?>" class="font-semibold text-blue-600 hover:underline"><?php echo htmlspecialchars($u['full_name'] ?: $u['username']); ?></a>
                    <div class="text-sm text-gray-500"><a href="?url=profile&id=<?php echo $u['id']; ?>" class="hover:underline">@<?php echo htmlspecialchars($u['username']); ?></a></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="bg-white p-4 rounded">
        <h3 class="font-semibold mb-2">Posts</h3>
        <?php if (empty($posts)): ?>
            <div class="text-gray-500">No posts found.</div>
        <?php else: ?>
            <?php foreach ($posts as $p): ?>
                <div class="border-b py-3">
                    <div class="font-bold">
                        <a href="?url=profile&id=<?php echo $p['user_id']; ?>" class="hover:underline text-blue-600"><?php echo htmlspecialchars($p['full_name'] ?: $p['username']); ?></a> 
                        <span class="text-sm text-gray-500"><a href="?url=profile&id=<?php echo $p['user_id']; ?>" class="hover:underline">@<?php echo htmlspecialchars($p['username']); ?></a> • <?php echo date('M j, Y', strtotime($p['created_at'])); ?></span>
                    </div>
                    <div class="mt-1"><?php echo nl2br(htmlspecialchars($p['content'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
