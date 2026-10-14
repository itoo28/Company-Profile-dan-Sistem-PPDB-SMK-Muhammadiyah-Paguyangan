<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::latest('id');

        if ($cari = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_pelaku', 'like', "%{$cari}%")
                  ->orWhere('jenis_aksi', 'like', "%{$cari}%")
                  ->orWhere('objek', 'like', "%{$cari}%")
                  ->orWhere('keterangan', 'like', "%{$cari}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('pages.admin.log.index', compact('logs', 'cari'));
    }
}
