@extends('layouts.admin')

@section('title', 'Manajemen Pengguna Staf — SMK Muhammadiyah Paguyangan')
@section('header-title', 'Manajemen Pengguna & Staf')
@section('header-subtitle', 'Kelola hak akses Super Admin dan Admin Piket PPDB')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; align-items: start;">
    
    <!-- Form Tambah Staf -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Tambah Akun Staf Baru</h3>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.staf.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Kom" required>
                    @error('name')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username <span style="color: #dc2626;">*</span></label>
                    <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Contoh: budi_piket" required>
                    @error('username')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email (Opsional)</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="budi@smkmuhpaguyangan.sch.id">
                    @error('email')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="role" class="form-label">Peran / Hak Akses <span style="color: #dc2626;">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="admin_piket" {{ old('role') === 'admin_piket' ? 'selected' : '' }}>Admin Piket (Pendaftaran Offline & Seragam)</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh Semua Modul)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password Awal <span style="color: #dc2626;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                    @error('password')
                        <div class="invalid-feedback" style="color: #dc2626; font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Akun Baru
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Staf -->
    <div class="admin-card" style="grid-column: span 1 / -1;">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Daftar Akun Staf Aktif & Terdaftar ({{ $users->count() }})</h3>
        </div>
        
        <div class="admin-table-container" style="border: none; border-radius: 0; box-shadow: none;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama & Username</th>
                        <th style="width: 140px;">Hak Akses</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                        <th style="width: 220px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong style="color: #092347; font-size: 0.9rem;">{{ $user->name }}</strong>
                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 2px;">
                                    <span>&#64;{{ $user->username }}</span>
                                    @if($user->email)
                                        <span> &bull; {{ $user->email }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge-status badge-status-info">
                                        <span class="badge-status-dot"></span>
                                        Super Admin
                                    </span>
                                @else
                                    <span class="badge-status" style="background-color: #f1f5f9; color: #475569;">
                                        Admin Piket
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($user->is_aktif)
                                    <span class="badge-status badge-status-success">
                                        <span class="badge-status-dot"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge-status badge-status-danger">
                                        <span class="badge-status-dot"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    <!-- Tombol Reset Password -->
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='flex'">
                                        Reset Sandi
                                    </button>

                                    <!-- Form Toggle Status Aktif -->
                                    @if(auth('web')->id() !== $user->id)
                                        <form action="{{ route('admin.staf.toggle', $user->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_aktif)
                                                <button type="submit" class="btn-action-delete" onclick="return confirm('Nonaktifkan akun staf {{ addslashes($user->name) }}?');">
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success" style="padding: 5px 10px; font-size: 0.8rem; font-weight: 600;">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic; padding: 0 4px;">(Sesi Anda)</span>
                                    @endif
                                </div>

                                <!-- Simple Reset Password Modal -->
                                <div id="resetModal-{{ $user->id }}" style="display: none; position: fixed; inset: 0; background: rgba(9,35,71,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
                                    <div style="background: white; border-radius: 10px; max-width: 400px; width: 100%; padding: 24px; text-align: left; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                                        <h4 style="margin: 0 0 8px 0; color: #092347; font-size: 1.1rem; font-weight: 700;">Reset Password: {{ $user->name }}</h4>
                                        <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 16px 0;">Masukkan password baru untuk akun staf ini.</p>
                                        
                                        <form action="{{ route('admin.staf.reset', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group mb-3">
                                                <label class="form-label">Password Baru</label>
                                                <input type="password" name="password" class="form-control" minlength="6" required>
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="form-label">Konfirmasi Password Baru</label>
                                                <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                                            </div>
                                            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('resetModal-{{ $user->id }}').style.display='none'">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Password</button>
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
    </div>
</div>
@endsection
