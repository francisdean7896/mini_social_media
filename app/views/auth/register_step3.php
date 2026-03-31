<?php include '../app/views/layouts/header.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$s = $_SESSION['signup'] ?? [];
?>

<div class="max-w-3xl mx-auto mt-12 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Account Recovery Questions</h2>
    <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>".htmlspecialchars($error)."</p>"; ?>
    <p class="mb-4">Provide at least 3 question & answer pairs that can be used to verify your identity if you forget your password.</p>
    <form action="?url=register" method="POST" id="recoveryForm">
        <input type="hidden" name="step" value="3">
        <div id="qaList">
            <?php for ($i=0;$i<3;$i++): ?>
            <div class="mb-4 border-b pb-4">
                <input type="text" name="question[]" placeholder="Question" class="w-full p-2 mb-2 border rounded">
                <input type="text" name="answer[]" placeholder="Answer" class="w-full p-2 border rounded">
            </div>
            <?php endfor; ?>
        </div>
        <div class="flex items-center gap-3 mt-4">
            <button type="button" id="addQA" class="px-3 py-2 border rounded">Add another Q&A</button>
            <a href="?url=register&step=2" class="text-gray-700">Back</a>
            <button type="submit" class="ml-auto bg-green-500 text-white px-4 py-2 rounded">Finish & Register</button>
        </div>
    </form>
</div>

<script>
document.getElementById('addQA').addEventListener('click', function(){
    const wrapper = document.createElement('div');
    wrapper.className = 'mb-4 border-b pb-4';
    wrapper.innerHTML = '<input type="text" name="question[]" placeholder="Question" class="w-full p-2 mb-2 border rounded">' +
                        '<input type="text" name="answer[]" placeholder="Answer" class="w-full p-2 border rounded">';
    document.getElementById('qaList').appendChild(wrapper);
});
</script>

<?php include '../app/views/layouts/footer.php'; ?>
