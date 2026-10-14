@extends('layouts.admin')

@section('title', 'Manajemen Pengguna Staf — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen Pengguna & Staf')
@section('header-subtitle', 'Kelola hak akses Super Admin dan Admin Piket PPDB')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    <!-- Form Tambah Staf -->
    <flux:card class="p-6">
        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 m-0">Tambah Akun Staf Baru</h3>
        </div>
        <form action="{{ route('admin.staf.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Kom" required>
                @error('name')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username <span class="text-rose-500">*</span></label>
                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: budi_piket" required>
                @error('username')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email (Opsional)</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="budi@smkmuhpaguyangan.sch.id">
                @error('email')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Peran / Hak Akses <span class="text-rose-500">*</span></label>
                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="admin_piket" {{ old('role') === 'admin_piket' ? 'selected' : '' }}>Admin Piket (Pendaftaran Offline & Seragam)</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh Semua Modul)</option>
                </select>
                @error('role')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password Awal <span class="text-rose-500">*</span></label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                @error('password')
                    <div class="text-rose-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Simpan Akun Baru
            </flux:button>
        </form>
    </flux:card>

    <!-- Tabel Daftar Staf -->
    <flux:card class="p-0 overflow-hidden lg:col-span-2">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 m-0">Daftar Akun Staf Aktif & Terdaftar ({{ $users->count() }})</h3>
        </div>
        
        <div class="admin-desktop-table overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>Nama & Username</th>
                        <th style="width: 140px;">Hak Akses</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                        <th style="width: 260px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong class="text-slate-900">{{ $user->name }}</strong>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    <span>&#64;{{ $user->username }}</span>
                                    @if($user->email)
                                        <span> &bull; {{ $user->email }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <flux:badge color="blue" size="sm">Super Admin</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Admin Piket</flux:badge>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <flux:badge color="{{ $user->is_aktif ? 'emerald' : 'rose' }}" size="sm">
                                    {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </flux:badge>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <!-- Tombol Edit Profil Staf -->
                                    <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editStafModal-{{ $user->id }}').style.display='flex'">
                                        Edit
                                    </flux:button>

                                    <!-- Tombol Reset Password -->
                                    <flux:button type="button" variant="subtle" size="xs" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='flex'">
                                        Reset Sandi
                                    </flux:button>

                                    <!-- Form Toggle Status Aktif -->
                                    @if(auth('web')->id() !== $user->id)
                                        <form action="{{ route('admin.staf.toggle', $user->id) }}" method="POST" class="inline-flex m-0 p-0 items-center">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_aktif)
                                                <flux:button type="submit" variant="ghost" size="xs" onclick="return confirm('Nonaktifkan akun staf {{ addslashes($user->name) }}?');">
                                                    Nonaktifkan
                                                </flux:button>
                                            @else
                                                <flux:button type="submit" variant="primary" size="xs">
                                                    Aktifkan
                                                </flux:button>
                                            @endif
                                        </form>

                                        <!-- Form Hapus Staf -->
                                        <form action="{{ route('admin.staf.destroy', $user->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Hapus permanen akun staf {{ addslashes($user->name) }} (&#64;{{ $user->username }})? Tindakan ini tidak dapat dibatalkan.');" class="inline-flex m-0 p-0 items-center">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="xs" icon="trash" title="Hapus Akun Staf">
                                                Hapus
                                            </flux:button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic px-1">(Sesi Anda)</span>
                                    @endif
                                </div>

                                <!-- Modal Edit Akun Staf -->
                                <div id="editStafModal-{{ $user->id }}" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-lg w-full p-6 text-left shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                            <h4 class="m-0 text-slate-900 text-base font-bold">Edit Akun Staf: {{ $user->name }}</h4>
                                            <button type="button" onclick="document.getElementById('editStafModal-{{ $user->id }}').style.display='none'" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 leading-none">&times;</button>
                                        </div>

                                        <form action="{{ route('admin.staf.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap <span class="text-rose-500">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <label class="form-label">Username <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Email (Opsional)</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Peran / Hak Akses <span class="text-rose-500">*</span></label>
                                                <select name="role" class="form-select" required>
                                                    <option value="admin_piket" {{ $user->role === 'admin_piket' ? 'selected' : '' }}>Admin Piket (Pendaftaran Offline & Seragam)</option>
                                                    <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh Semua Modul)</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Ganti Password (Opsional)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                                            </div>

                                            <div class="mb-5">
                                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                                                    <input type="checkbox" name="is_aktif" value="1" {{ $user->is_aktif ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                                    <span>Akun Aktif (Dapat masuk ke portal administrasi)</span>
                                                </label>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('editStafModal-{{ $user->id }}').style.display='none'">
                                                    Batal
                                                </flux:button>
                                                <flux:button type="submit" variant="primary" size="sm">
                                                    Simpan Perubahan
                                                </flux:button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Reset Password Modal -->
                                <div id="resetModal-{{ $user->id }}" style="display: none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-md w-full p-6 text-left shadow-2xl border border-slate-200">
                                        <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                                            <h4 class="m-0 text-slate-900 text-base font-bold">Reset Password: {{ $user->name }}</h4>
                                            <button type="button" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='none'" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 leading-none">&times;</button>
                                        </div>
                                        <p class="text-xs text-slate-500 m-0 mb-4">Masukkan password baru untuk akun staf ini.</p>
                                        
                                        <form action="{{ route('admin.staf.reset', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="mb-3">
                                                <label class="form-label">Password Baru</label>
                                                <input type="password" name="password" class="form-control" minlength="6" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label">Konfirmasi Password Baru</label>
                                                <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <flux:button type="button" variant="ghost" size="sm" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='none'">
                                                    Batal
                                                </flux:button>
                                                <flux:button type="submit" variant="primary" size="sm">
                                                    Simpan Password
                                                </flux:button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile Android -->
        <div class="admin-mobile-cards p-3 flex flex-col gap-3">
            @foreach($users as $user)
                <div class="admin-data-card p-3.5 bg-white rounded-lg border border-slate-200 shadow-xs">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <strong class="text-sm text-slate-900 block">{{ $user->name }}</strong>
                            <div class="text-xs text-slate-500">
                                <span>&#64;{{ $user->username }}</span>
                                @if($user->email)
                                    <span> &bull; {{ $user->email }}</span>
                                @endif
                            </div>
                        </div>
                        <flux:badge color="{{ $user->is_aktif ? 'emerald' : 'rose' }}" size="sm">
                            {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </div>

                    <div class="flex items-center justify-between text-xs py-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Hak Akses:</span>
                        @if($user->role === 'super_admin')
                            <flux:badge color="blue" size="sm">Super Admin</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Admin Piket</flux:badge>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100 flex-wrap">
                        <flux:button type="button" variant="subtle" size="xs" icon="pencil-square" onclick="document.getElementById('editStafModal-{{ $user->id }}').style.display='flex'">
                            Edit
                        </flux:button>

                        <flux:button type="button" variant="subtle" size="xs" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='flex'">
                            Reset Sandi
                        </flux:button>

                        @if(auth('web')->id() !== $user->id)
                            <form action="{{ route('admin.staf.toggle', $user->id) }}" method="POST" class="inline-flex m-0 p-0 items-center">
                                @csrf
                                @method('PATCH')
                                @if($user->is_aktif)
                                    <flux:button type="submit" variant="ghost" size="xs" onclick="return confirm('Nonaktifkan akun staf {{ addslashes($user->name) }}?');">
                                        Nonaktifkan
                                    </flux:button>
                                @else
                                    <flux:button type="submit" variant="primary" size="xs">
                                        Aktifkan
                                    </flux:button>
                                @endif
                            </form>

                            <form action="{{ route('admin.staf.destroy', $user->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Hapus permanen akun staf {{ addslashes($user->name) }}?');" class="inline-flex m-0 p-0 items-center">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                    Hapus
                                </flux:button>
                            </form>
                        @else
                            <span class="text-xs text-slate-400 italic px-1">(Sesi Anda)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </flux:card>
</div>
@endsection
