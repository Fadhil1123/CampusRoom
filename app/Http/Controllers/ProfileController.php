<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;

class ProfileController extends Controller
{
    // =====================================
    // TAMPILKAN HALAMAN PROFIL
    // =====================================

    public function index()
    {
        $userId = session('user')->user_id;
        $user   = User::findOrFail($userId);

        // Statistik booking milik user (untuk mahasiswa)
        $totalBooking    = Booking::where('user_id', $userId)->count();
        $bookingApproved = Booking::where('user_id', $userId)->where('status', 'approved')->count();
        $bookingPending  = Booking::where('user_id', $userId)->where('status', 'pending')->count();

        // Pilih layout sesuai role
        $layout = $user->role === 'admin' ? 'layouts.admin' : 'layouts.dashboard';

        return view('profile.index', compact('user', 'totalBooking', 'bookingApproved', 'bookingPending', 'layout'));
    }

    // =====================================
    // UPDATE DATA PROFIL
    // =====================================

    public function update(Request $request)
    {
        $userId = session('user')->user_id;
        $user   = User::findOrFail($userId);

        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100',
            'no_hp'   => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nama'    => $request->nama,
            'email'   => $request->email,
            'no_hp'   => $request->no_hp,
            'jurusan' => $request->jurusan,
        ];

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                \Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profile-photos', 'public');
        }

        $user->update($data);

        // Update session agar sidebar langsung ikut berubah
        session(['user' => $user->fresh()]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // =====================================
    // GANTI PASSWORD
    // =====================================

    public function updatePassword(Request $request)
    {
        $userId = session('user')->user_id;
        $user   = User::findOrFail($userId);

        $request->validate([
            'password_lama'      => 'required',
            'password_baru'      => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
        ]);

        // Cek password lama — sesuai sistem ini yang menyimpan password plain text
        // (lihat AuthController::login yang membandingkan langsung tanpa Hash::check)
        if ($request->password_lama !== $user->password) {
            return back()->with('error_password', 'Password lama tidak sesuai.');
        }

        $user->update(['password' => $request->password_baru]);

        return back()->with('success_password', 'Password berhasil diperbarui!');
    }

    // =====================================
    // HAPUS FOTO PROFIL
    // =====================================

    public function deletePhoto()
    {
        $userId = session('user')->user_id;
        $user   = User::findOrFail($userId);

        if ($user->foto) {
            \Storage::disk('public')->delete($user->foto);
            $user->update(['foto' => null]);
            session(['user' => $user->fresh()]);
        }

        return back()->with('success', 'Foto profil dihapus.');
    }
}