<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccountHub | Connect & Share</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #f3f4f6; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

<nav class="bg-blue-600 p-4 text-white shadow-lg">
    <div class="container mx-auto flex justify-between items-center">
        <a href="?url=newsfeed" class="text-2xl font-bold tracking-tight">AccountHub</a>
        
        <div class="space-x-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="hidden md:inline text-blue-100">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="?url=newsfeed" class="hover:text-blue-200 transition">Feed</a>
                <a href="?url=profile" class="hover:text-blue-200 transition">Profile</a>
                <form action="?url=search" method="GET" class="inline-block ml-4">
                    <input type="hidden" name="url" value="search">
                    <input type="text" name="q" placeholder="Search users or posts" class="px-2 py-1 rounded text-sm text-black" />
                    <button type="submit" class="ml-1 text-white bg-blue-700 px-2 py-1 rounded">Search</button>
                </form>
                <a href="?url=logout" class="bg-blue-700 px-4 py-2 rounded hover:bg-blue-800 transition">Logout</a>
            <?php else: ?>
                <a href="?url=login" class="hover:text-blue-200">Login</a>
                <a href="?url=register" class="bg-white text-blue-600 px-4 py-2 rounded font-semibold hover:bg-gray-100 transition">Join Now</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow container mx-auto px-4 py-6">