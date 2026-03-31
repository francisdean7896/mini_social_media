<?php include '../app/views/layouts/header.php'; ?>

<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-xl border border-gray-200">
    <h2 class="text-xl font-bold mb-4">Edit Profile</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'username_taken'): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">The chosen username is already taken.</div>
    <?php endif; ?>

    <form action="?url=profile/update" method="POST" enctype="multipart/form-data">
        <label class="block mb-2 font-medium">Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" class="w-full p-2 mb-4 border rounded" required>

        <label class="block mb-2 font-medium">Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" class="w-full p-2 mb-4 border rounded">

        <label class="block mb-2 font-medium">Bio</label>
        <textarea name="bio" rows="3" class="w-full p-2 mb-4 border rounded"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>

        <label class="block mb-2 font-medium">Address</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <input type="text" name="street" placeholder="Street / House No." value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>" class="w-full p-2 border rounded">
            <input type="text" name="barangay" placeholder="Barangay" value="<?php echo htmlspecialchars($user['barangay'] ?? ''); ?>" class="w-full p-2 border rounded">
            <input type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" class="w-full p-2 border rounded">
            <input type="text" name="province" placeholder="Province" value="<?php echo htmlspecialchars($user['province'] ?? ''); ?>" class="w-full p-2 border rounded">
            <input type="text" name="country" placeholder="Country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" class="w-full p-2 border rounded">
            <input type="text" name="postal_code" placeholder="Postal Code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" class="w-full p-2 border rounded">
        </div>

        <label class="block mb-2 font-medium">Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full p-2 mb-4 border rounded">

        <label class="block mb-2 font-medium">Birthdate</label>
        <input type="date" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate'] ?? ''); ?>" class="w-full p-2 mb-4 border rounded">

        <label class="block mb-2 font-medium">Profile Picture</label>
        <?php if (!empty($user['avatar'])): ?>
            <div class="mb-2">
                <img src="assets/uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="avatar" class="h-20 w-20 rounded-full object-cover" />
            </div>
        <?php endif; ?>
        <input type="file" name="avatar" accept="image/*" class="mb-4">

        <div class="flex justify-end">
            <a href="?url=profile" class="mr-3 text-gray-600">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
