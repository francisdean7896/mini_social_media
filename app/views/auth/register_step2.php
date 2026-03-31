<?php include '../app/views/layouts/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$s = $_SESSION['signup'] ?? [];
?>

<div class="max-w-3xl mx-auto mt-12 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Personal Information</h2>
    <form action="?url=register" method="POST">
        <input type="hidden" name="step" value="2">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="street" placeholder="Street" class="p-2 border rounded" value="<?= htmlspecialchars($s['street'] ?? '') ?>">
            <input type="text" name="barangay" placeholder="Barangay" class="p-2 border rounded" value="<?= htmlspecialchars($s['barangay'] ?? '') ?>">
            <input type="text" name="city" placeholder="City" class="p-2 border rounded" value="<?= htmlspecialchars($s['city'] ?? '') ?>">
            <input type="text" name="province" placeholder="Province" class="p-2 border rounded" value="<?= htmlspecialchars($s['province'] ?? '') ?>">
            <input type="text" name="country" placeholder="Country" class="p-2 border rounded" value="<?= htmlspecialchars($s['country'] ?? '') ?>">
            <input type="text" name="postal_code" placeholder="Postal Code" class="p-2 border rounded" value="<?= htmlspecialchars($s['postal_code'] ?? '') ?>">
            <input type="text" name="phone" placeholder="Phone" class="p-2 border rounded col-span-2" value="<?= htmlspecialchars($s['phone'] ?? '') ?>">
            <input type="date" name="birthdate" placeholder="Birthdate" class="p-2 border rounded col-span-2" value="<?= htmlspecialchars($s['birthdate'] ?? '') ?>">
        </div>
        <div class="flex justify-between mt-6">
            <a href="?url=register" class="px-4 py-2 border rounded text-gray-700">Back</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Next</button>
        </div>
    </form>
</div>

<?php include '../app/views/layouts/footer.php'; ?>
