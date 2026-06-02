<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::orderBy(
            'kegiatan_id',
            'asc'
        )->get();

        return view(
            'admin.kegiatan.index',
            compact('kegiatan')
        );
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
}