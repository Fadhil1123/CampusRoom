<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-[260px] bg-white border-r border-slate-200 hidden lg:block p-6">
            <h1 class="text-2xl font-bold font-heading mb-10 text-[#14213D]">CampusRoom</h1>
            <nav class="space-y-2">
                <a href="/dashboard" class="flex items-center gap-3 bg-amber-50 text-amber-600 p-3 rounded-xl font-bold">🏠 Dashboard</a>
                <a href="/booking" class="flex items-center gap-3 text-slate-500 hover:bg-slate-50 p-3 rounded-xl">📅 Booking Saya</a>
            </nav>
        </aside>
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>