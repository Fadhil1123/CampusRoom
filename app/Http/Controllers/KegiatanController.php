<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    // =====================================
    // DATA KEGIATAN SELURUH USER (admin)
    // =====================================

    public function index(Request $request)
    {
        $query = Kegiatan::with(['bookings' => function ($q) {
                $q->with('rooms')->orderBy('booking_id', 'desc');
            }])
            ->orderBy('kegiatan_id', 'desc');

        // Search nama kegiatan / penyelenggara
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_kegiatan', 'like', "%$s%")
                  ->orWhere('penyelenggara', 'like', "%$s%");
            });
        }

        // Filter penyelenggara
        if ($request->filled('penyelenggara')) {
            $query->where('penyelenggara', $request->penyelenggara);
        }

        $kegiatanAll = $query->get();

        // Filter berbasis data booking terkait (status, ruangan, tanggal)
        $kegiatanFiltered = $kegiatanAll->filter(function ($k) use ($request) {
            $booking = $k->bookings->first();

            if (!$booking) {
                return !$request->filled('status')
                    && !$request->filled('room_id')
                    && !$request->filled('dari')
                    && !$request->filled('hingga');
            }

            if ($request->filled('status') && $booking->status !== $request->status) return false;
            if ($request->filled('room_id') && !$booking->rooms->contains('room_id', $request->room_id)) return false;
            if ($request->filled('dari') && $booking->tanggal < $request->dari) return false;
            if ($request->filled('hingga') && $booking->tanggal > $request->hingga) return false;

            return true;
        })->values();

        // Manual pagination karena filter dilakukan di level koleksi
        $perPage     = 10;
        $currentPage = $request->get('page', 1);
        $items       = $kegiatanFiltered->forPage($currentPage, $perPage)->values();
        $kegiatan    = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $kegiatanFiltered->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalKegiatan      = $kegiatanFiltered->count();
        $rooms              = \App\Models\Room::orderBy('nama_ruangan')->get();
        $penyelenggaraList  = Kegiatan::whereNotNull('penyelenggara')->distinct()->pluck('penyelenggara');

        return view('admin.kegiatan.index', compact(
            'kegiatan', 'totalKegiatan', 'rooms', 'penyelenggaraList'
        ));
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::find($id);

        return view(
            'admin.kegiatan.edit',
            compact('kegiatan')
        );
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::find($id);

        $kegiatan->update([

            'nama_kegiatan' => $request->nama_kegiatan,

            'deskripsi' => $request->deskripsi,

            'penyelenggara' => $request->penyelenggara,

        ]);

        return redirect('/admin/kegiatan');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::find($id);

        // CEK APAKAH MASIH DIGUNAKAN BOOKING
        if ($kegiatan->bookings()->exists()) {

            return back()->with(
                'error',
                'Kegiatan masih digunakan oleh booking.'
            );
        }

        // HAPUS KEGIATAN
        $kegiatan->delete();

        return redirect('/admin/kegiatan')
            ->with(
                'success',
                'Data kegiatan berhasil dihapus.'
            );
    }

    // =====================================
    // BULK DELETE (dari halaman index)
    // =====================================

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('kegiatan_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada kegiatan yang dipilih.');
        }

        $terpakai = [];
        foreach ($ids as $id) {
            $kegiatan = Kegiatan::find($id);
            if ($kegiatan && $kegiatan->bookings()->exists()) {
                $terpakai[] = $kegiatan->nama_kegiatan;
                continue;
            }
            $kegiatan?->delete();
        }

        if (!empty($terpakai)) {
            return back()->with('error', 'Sebagian kegiatan tidak dihapus karena masih digunakan booking: ' . implode(', ', $terpakai));
        }

        return back()->with('success', 'Kegiatan terpilih berhasil dihapus.');
    }
}