<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-xl border border-gray-200">
    <h2 class="text-xl font-bold mb-4">Edit Comment</h2>
    <form action="?url=comments/update" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($comment['id']); ?>">
        <textarea name="comment" rows="3" class="w-full p-2 mb-4 border rounded"><?php echo htmlspecialchars($comment['content']); ?></textarea>
        <div class="flex justify-end">
            <a href="?url=newsfeed" class="mr-3 text-gray-600">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </div>
    </form>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
