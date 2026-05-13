<x-app-layout>
    <x-slot name="title">Manajemen Karyawan</x-slot>
    <x-slot name="subtitle">Kelola akun admin dan kasir</x-slot>
    <x-slot name="actions">
        <a href="{{ route('users.create') }}" class="btn btn-primary">➕ Tambah Karyawan</a>
    </x-slot>

    <div class="card">
        <div class="table-wrapper">
            @if($users->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>Belum ada karyawan</h3>
                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">Tambah Karyawan</a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-center gap-2">
                                    <div style="width:32px;height:32px;border-radius:50%;
                                                background:linear-gradient(135deg,var(--amber-400),var(--coffee-400));
                                                display:flex;align-items:center;justify-content:center;
                                                color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <div style="font-size:.7rem;color:var(--amber-500)">● Anda</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-kasir' }}">
                                    {{ $user->role === 'admin' ? '👑 Admin' : '💳 Kasir' }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">✏️</a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
