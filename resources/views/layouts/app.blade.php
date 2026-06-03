<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusRoom | Booking Ruangan Kampus</title>
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="bg-[#F8FAFF] text-[#1A2340] font-['DM_Sans']">
    @yield('content')
</body>
</html>