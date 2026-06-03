@extends('layouts.app')

@section('content')
<nav class="fixed w-full z-50 transition-all duration-300" id="navbar">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center backdrop-blur-md bg-white/70 border border-white/20 rounded-2xl mt-4 shadow-sm">
        <h1 class="font-['Space_Grotesk'] text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#F4B400] to-[#4FC3F7]">CampusRoom</h1>
        <div class="hidden md:flex gap-8 font-medium text-[#5A6A8A]">
            <a href="#fitur" class="hover:text-[#F4B400] transition">Fitur</a>
            <a href="#cara-kerja" class="hover:text-[#F4B400] transition">Cara Kerja</a>
        </div>
        <div class="flex gap-4">
            <a href="/login" class="px-5 py-2.5 text-[#14213D] border border-[#14213D] rounded-xl hover:bg-[#14213D] hover:text-white transition">Login</a>
            <a href="#" class="px-5 py-2.5 bg-gradient-to-r from-[#F4B400] to-[#FFB020] text-white rounded-xl shadow-lg hover:scale-105 transition">Register</a>
        </div>
    </div>
</nav>

<section class="relative pt-40 pb-20 px-6 max-w-7xl mx-auto flex flex-col md:flex-row items-center overflow-hidden">
    <div class="absolute top-20 right-10 w-96 h-96 bg-[#F4B400] rounded-full blur-[128px] opacity-20 -z-10"></div>
    <div class="absolute bottom-20 left-10 w-96 h-96 bg-[#4FC3F7] rounded-full blur-[128px] opacity-20 -z-10"></div>

    <div class="md:w-3/5 space-y-6">
        <span class="inline-flex items-center px-3 py-1 bg-[#F4B400]/10 text-[#F4B400] font-bold text-xs uppercase tracking-widest rounded-full border border-[#F4B400]/20">
            ✨ Sistem Baru Kampus
        </span>
        <h2 class="font-['Space_Grotesk'] text-5xl md:text-7xl font-extrabold leading-[1.1] text-[#14213D]">
            Booking Ruangan Kampus,<br>
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#F4B400] to-[#4FC3F7]">Lebih Mudah</span> dari Sebelumnya.
        </h2>
        <p class="font-['Plus_Jakarta_Sans'] text-[#5A6A8A] text-lg md:text-xl max-w-lg leading-relaxed">
            Kelola peminjaman ruang kelas, laboratorium, dan aula kampus dalam satu platform terintegrasi.
        </p>
        <div class="flex gap-4 pt-4">
            <button class="bg-[#14213D] text-white px-8 py-4 rounded-xl font-bold hover:bg-[#1A2340] transition hover:scale-[1.03]">Mulai Booking →</button>
            <button class="text-[#14213D] font-semibold border-b-2 border-[#14213D] hover:text-[#4FC3F7] hover:border-[#4FC3F7] transition">Pelajari Lebih</button>
        </div>
    </div>

    <div class="md:w-2/5 mt-16 md:mt-0 relative flex justify-center">
        <div class="flex flex-col gap-4">
            <div class="floating-card p-5 bg-white/70 backdrop-blur-md rounded-2xl shadow-xl border border-white/50 w-64">
                <p class="text-xs font-bold text-[#5A6A8A] uppercase">Jadwal Hari Ini</p>
                <p class="font-bold text-[#14213D]">08:00 - Lab Kom A</p>
            </div>
            <div class="floating-card p-5 bg-white/70 backdrop-blur-md rounded-2xl shadow-xl border border-white/50 w-64" style="animation-delay: 0.6s">
                <p class="font-bold text-[#00C896]">✅ Booking Approved</p>
            </div>
            <div class="floating-card p-5 bg-white/70 backdrop-blur-md rounded-2xl shadow-xl border border-white/50 w-64" style="animation-delay: 1.2s">
                <p class="font-bold text-[#14213D]">120+ Ruangan</p>
                <p class="text-sm text-[#5A6A8A]">98% Kepuasan</p>
            </div>
        </div>
    </div>
</section>

<div class="py-6 border-y border-[#E8EEF7] bg-[#F8FBFF] overflow-hidden">
    <div class="whitespace-nowrap animate-marquee text-[#5A6A8A] font-semibold font-['Plus_Jakarta_Sans'] tracking-widest uppercase text-sm">
        Laboratorium Big Data • Laboratorium MTI • Laboratorium Komputer Dasar • Ruang A-14 • Ruang A-15 • Perpustakaan • Ruang A-16 • Ruang A-13 • Lobby
    </div>
</div>

<section id="fitur" class="py-24 px-6 max-w-7xl mx-auto">
    <div class="grid md:grid-cols-3 gap-8">
        @foreach([['⚡', 'Booking Cepat', 'Proses selesai dalam hitungan detik'], ['📅', 'Jadwal Real-time', 'Pantau jadwal secara akurat'], ['✅', 'Auto Approve', 'Sistem cerdas untuk disetujui']] as $f)
        <div class="feature-card p-8 bg-white rounded-3xl border border-[#E8EEF7] shadow-sm transition-all duration-300">
            <div class="w-14 h-14 bg-[#F8FBFF] rounded-2xl flex items-center justify-center text-3xl mb-6">{{ $f[0] }}</div>
            <h3 class="text-xl font-bold mb-3 text-[#14213D]">{{ $f[1] }}</h3>
            <p class="text-[#5A6A8A]">{{ $f[2] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-5xl mx-auto bg-[#14213D] rounded-[40px] p-16 text-center text-white relative overflow-hidden">
        <h2 class="text-4xl md:text-5xl font-bold mb-6 font-['Space_Grotesk']">Mulai kelola ruanganmu hari ini</h2>
        <button class="px-10 py-5 bg-gradient-to-r from-[#F4B400] to-[#FFB020] text-white rounded-xl font-bold text-lg hover:scale-[1.03] transition">Masuk Sekarang →</button>
    </div>
</section>

<footer class="py-12 px-6 text-center text-[#5A6A8A] font-['Plus_Jakarta_Sans']">
    <p>© 2026 CampusRoom — Sistem Peminjaman Ruangan</p>
</footer>
@endsection