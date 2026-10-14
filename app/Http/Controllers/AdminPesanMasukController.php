<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\PesanMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPesanMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = PesanMasuk::latest('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $pesanList = $query->paginate(15)->withQueryString();

        return view('pages.admin.pesan-masuk.index', compact('pesanList'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $pesan = PesanMasuk::findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:belum_dibaca,sudah_dibaca,dibalas'],
        ]);

        $pesan->update(['status' => $request->status]);

        LogAktivitas::catat($user, 'update_pesan_masuk', 'Inbox Kontak', "Mengubah status pesan dari {$pesan->nama} menjadi {$request->status}");

        return back()->with('sukses', "Status pesan dari {$pesan->nama} berhasil diubah menjadi: " . ucfirst(str_replace('_', ' ', $request->status)));
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('web')->user();
        $pesan = PesanMasuk::findOrFail($id);
        $nama = $pesan->nama;
        $pesan->delete();

        LogAktivitas::catat($user, 'hapus_pesan_masuk', 'Inbox Kontak', "Menghapus pesan kontak dari {$nama}");

        return back()->with('sukses', "Pesan dari {$nama} berhasil dihapus.");
    }
}
