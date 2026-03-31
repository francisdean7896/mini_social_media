<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-md mx-auto mt-20 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
    <form action="?url=register" method="POST">
        <input type="hidden" name="step" value="1">
        <input type="text" name="full_name" placeholder="Full Name" class="w-full p-2 mb-4 border rounded" required>
        <input type="text" name="username" placeholder="Username" class="w-full p-2 mb-4 border rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border rounded" required>
        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Sign Up</button>
        <p class="mt-3 text-sm text-gray-600">After this step you'll enter personal info and account recovery questions.</p>
    </form>
    <p class="mt-4 text-center">Already have an account? <a href="?url=login" class="text-blue-500">Login</a></p>
    <p class="mt-4 text-center">Already have an account? <a href="?url=login" class="text-blue-500">Login</a></p>
</div>

<?php include '../app/views/layouts/footer.php'; ?>