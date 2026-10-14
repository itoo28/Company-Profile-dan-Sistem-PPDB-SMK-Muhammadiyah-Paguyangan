<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStafController extends Controller
{
    public function index()
    {
        $users = User::latest('id')->get();

        return view('pages.admin.staf.index', compact('users'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:super_admin,admin_piket'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_aktif'] = true;

        $user = User::create($validated);

        LogAktivitas::catat($currentUser, 'tambah_staf', 'Manajemen Pengguna', "Membuat akun staf baru: {$user->name} ({$user->username}) peran {$user->role}");

        return back()->with('sukses', "Akun staf {$user->name} berhasil dibuat.");
    }

    public function toggleAktif(int $id)
    {
        $currentUser = Auth::guard('web')->user();

        if ($currentUser->id === $id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang masuk.');
        }

        $user = User::findOrFail($id);
        $user->is_aktif = !$user->is_aktif;
        $user->save();

        $statusTeks = $user->is_aktif ? 'Diaktifkan' : 'Dinonaktifkan';
        LogAktivitas::catat($currentUser, 'toggle_staf', 'Manajemen Pengguna', "Mengubah status akun staf {$user->username} menjadi {$statusTeks}");

        return back()->with('sukses', "Status akun staf {$user->name} berhasil {$statusTeks}.");
    }

    public function resetPassword(Request $request, int $id)
    {
        $currentUser = Auth::guard('web')->user();
        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        LogAktivitas::catat($currentUser, 'reset_password_staf', 'Manajemen Pengguna', "Reset password untuk akun staf: {$user->username}");

        return back()->with('sukses', "Password untuk akun {$user->name} berhasil diubah.");
    }
}
