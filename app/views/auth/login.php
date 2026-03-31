<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-md mx-auto mt-20 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Welcome Back</h2>
    <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
    <form action="?url=login" method="POST">
        <input type="text" name="username" placeholder="Username" class="w-full p-2 mb-4 border rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border rounded" required>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
    </form>
    <?php $enteredUsername = isset($_POST['username']) ? trim($_POST['username']) : ''; ?>
    <?php if(!empty($showForgot) && $enteredUsername !== ''): ?>
        <div class="mt-4 text-center">
            <a href="?url=recover&username=<?= urlencode($enteredUsername) ?>" class="text-sm text-red-600">Forgot Password?</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../app/views/layouts/footer.php'; ?>