<?php include '../app/views/layouts/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$username = $_REQUEST['username'] ?? '';
$questions = isset($questions) ? $questions : [];
?>

<div class="max-w-3xl mx-auto mt-12 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Account Recovery</h2>
    <?php if(!empty($error)) echo "<p class='text-red-500 mb-4'>".htmlspecialchars($error)."</p>"; ?>

    <?php if (empty($questions)): ?>
        <form action="?url=recover" method="POST">
            <input type="hidden" name="action" value="lookup">
            <label class="block mb-2">Enter your username to begin:</label>
            <input type="text" name="username" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($username) ?>" required>
            <div class="flex justify-end">
                <a href="?url=login" class="mr-4 text-gray-700">Back to login</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Lookup</button>
            </div>
        </form>
    <?php else: ?>
        <form action="?url=recover" method="POST">
            <input type="hidden" name="action" value="verify">
            <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
            <p class="mb-4">Answer the following questions to verify your identity.</p>
            <?php foreach($questions as $i => $q): ?>
                <div class="mb-4">
                    <label class="block font-medium mb-1"><?= htmlspecialchars($q['question']) ?></label>
                    <input type="text" name="answer[<?= $i ?>]" class="w-full p-2 border rounded" required>
                </div>
            <?php endforeach; ?>
            <div class="flex justify-between">
                <a href="?url=login" class="text-gray-700">Cancel</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Verify Answers</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
