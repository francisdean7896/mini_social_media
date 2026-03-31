<?php include '../app/views/layouts/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$error = $error ?? '';
?>

<div class="max-w-md mx-auto mt-12 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
    <?php if(!empty($error)) echo "<p class='text-red-500 mb-4'>".htmlspecialchars($error)."</p>"; ?>
    <form action="?url=recover" method="POST">
        <input type="hidden" name="action" value="reset">
        <label class="block mb-2">New Password</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-4" required>
        <label class="block mb-2">Confirm Password</label>
        <input type="password" name="password_confirm" class="w-full p-2 border rounded mb-4" required>
        <div class="flex justify-end">
            <a href="?url=login" class="mr-4 text-gray-700">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Reset Password</button>
        </div>
    </form>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
