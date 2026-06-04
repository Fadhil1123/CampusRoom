@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F8FAFF] p-4">
    <div class="w-full max-w-[1000px] bg-white rounded-[32px] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.1)] flex overflow-hidden min-h-[600px] border border-slate-100">
        
        <div class="hidden lg:flex w-2/5 bg-gradient-to-br from-[#F4B400] to-[#4FC3F7] p-12 flex-col justify-between text-white relative overflow-hidden">
            <div class="z-10">
                <h1 class="text-3xl font-bold font-['Space_Grotesk'] tracking-tight">CampusRoom</h1>
            </div>
            
            <div class="z-10 space-y-6">
                <h2 class="text-4xl font-bold font-['Space_Grotesk'] leading-tight">Kelola ruangan <br>dengan cerdas</h2>
                <ul class="space-y-4 font-['Plus_Jakarta_Sans'] text-white/90">
                    <li class="flex items-center gap-3">✨ Booking instan</li>
                    <li class="flex items-center gap-3">✨ Monitoring real-time</li>
                    <li class="flex items-center gap-3">✨ Notifikasi otomatis</li>
                </ul>
            </div>

            <div class="float-element w-40 h-40 bg-white/10 rounded-3xl absolute -bottom-10 -right-10 backdrop-blur-md"></div>
            <div class="float-element w-24 h-24 bg-white/10 rounded-2xl absolute top-20 -left-10 backdrop-blur-md" style="animation-delay: 1s"></div>
        </div>

        <div class="w-full lg:w-3/5 p-8 lg:p-16 flex flex-col justify-center">
            <div class="max-w-[380px] w-full mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
                <div>
                    <h2 class="text-3xl font-bold text-[#14213D] font-['Plus_Jakarta_Sans']">Selamat Datang Kembali</h2>
                    <p class="text-[#5A6A8A] mt-2 font-['DM_Sans'] text-sm">Masuk menggunakan NIM/NIP Anda</p>
                </div>

                <form action="/login" method="POST" class="space-y-6">
                    @csrf
                    
                    @if(session('error'))
                        <div class="p-4 bg-red-50 text-red-600 rounded-xl text-sm font-semibold border border-red-100 animate-shake flex items-center">
                            ⚠️ {{ session('error') }}
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#14213D] uppercase tracking-wider">NIM / NIP</label>
                        <input type="text" name="nim_nip" required 
                            class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#F4B400] focus:border-transparent outline-none transition duration-200" 
                            placeholder="Contoh: 20261101">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <label class="text-xs font-bold text-[#14213D] uppercase tracking-wider">Password</label>
                        </div>
                        <input type="password" name="password" required 
                            class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#F4B400] focus:border-transparent outline-none transition duration-200" 
                            placeholder="••••••••">
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <label class="flex items-center gap-2 text-[#5A6A8A] cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-[#F4B400] focus:ring-[#F4B400]"> Ingat saya
                        </label>
                        <a href="#" class="text-[#4FC3F7] font-semibold hover:underline">Lupa Password?</a>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-[#14213D] hover:bg-[#2A3B5F] text-white font-bold rounded-xl transition duration-300 hover:scale-[1.01] shadow-lg hover:shadow-xl">
                        MASUK SEKARANG
                    </button>
                </form>

                <p class="text-center text-[10px] text-slate-400 uppercase tracking-widest">v1.0.0 — Sistem Peminjaman Ruangan</p>
            </div>
        </div>
    </div>
</div>
@endsection