<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
            showAddModal: false, 
            showEditModal: false, 
            showSaldoModal: false,
            editUser: null,
            saldoUser: null 
        }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Daftar Pengguna Sistem</h3>
                        <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Pengguna
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama & Email</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Saldo (Ormawa)</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($users as $user)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-sm">{{ $user->username }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 border rounded-full text-xs font-semibold uppercase">
                                            {{ $user->roles->first()?->name ?? 'None' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($user->status_akun == 'aktif')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if(in_array($user->roles->first()?->name, ['ormawa', 'bem', 'bpm']))
                                            <div class="font-semibold">Rp {{ number_format($user->saldo, 0, ',', '.') }}</div>
                                            <button @click="showSaldoModal = true; saldoUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'saldo' => (float)$user->saldo]) }}" class="text-xs text-indigo-600 hover:underline">Atur Saldo</button>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <button @click="showEditModal = true; editUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'username' => $user->username, 'status_akun' => $user->status_akun, 'role' => $user->roles->first()?->name]) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                        
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddModal = false"></div>
                <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Tambah Pengguna Baru</h3>
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" value="Nama Lengkap/Ormawa" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="email" value="Email" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="username" value="Username" />
                                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="password" value="Password" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="role" value="Role Akses" />
                                <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showEditModal = false"></div>
                <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Edit Pengguna</h3>
                    <form :action="'/admin/users/' + editUser?.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="edit_name" value="Nama Lengkap/Ormawa" />
                                <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" x-model="editUser.name" required />
                            </div>
                            <div>
                                <x-input-label for="edit_email" value="Email" />
                                <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" x-model="editUser.email" required />
                            </div>
                            <div>
                                <x-input-label for="edit_username" value="Username" />
                                <x-text-input id="edit_username" name="username" type="text" class="mt-1 block w-full" x-model="editUser.username" required />
                            </div>
                            <div>
                                <x-input-label for="edit_password" value="Password (Kosongkan jika tidak diubah)" />
                                <x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="edit_role" value="Role Akses" />
                                <select id="edit_role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="editUser.role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="edit_status" value="Status Akun" />
                                <select id="edit_status" name="status_akun" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="editUser.status_akun" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Saldo Modal -->
        <div x-show="showSaldoModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showSaldoModal = false"></div>
                <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Atur Saldo Ormawa</h3>
                    <p class="mb-4 text-sm text-gray-600">Mengatur saldo untuk: <strong x-text="saldoUser?.name"></strong></p>
                    <form :action="'/admin/users/' + saldoUser?.id + '/saldo'" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="saldo" value="Nominal Saldo (Rp)" />
                                <x-text-input id="saldo" name="saldo" type="number" class="mt-1 block w-full" x-model="saldoUser.saldo" required min="0" />
                                <p class="text-xs text-gray-500 mt-1">Saldo ini akan menjadi batas maksimal pengajuan dana.</p>
                            </div>
                            <div>
                                <x-input-label for="catatan_saldo" value="Alasan Perubahan" />
                                <textarea id="catatan_saldo" name="catatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showSaldoModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Simpan Saldo</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-bold mb-4">Riwayat Perubahan Saldo</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50"><tr><th class="p-2 border text-left">Waktu</th><th class="p-2 border text-left">Pengguna</th><th class="p-2 border text-left">Aktor</th><th class="p-2 border text-left">Perubahan</th><th class="p-2 border text-left">Alasan</th></tr></thead>
                        <tbody>
                            @forelse($saldoHistori as $history)
                                <tr><td class="p-2 border">{{ $history->created_at->format('d/m/Y H:i') }}</td><td class="p-2 border">{{ $history->user->name }}</td><td class="p-2 border">{{ $history->actor->name }}</td><td class="p-2 border">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }} → Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</td><td class="p-2 border">{{ $history->catatan }}</td></tr>
                            @empty
                                <tr><td colspan="5" class="p-4 border text-center text-gray-500">Belum ada riwayat perubahan saldo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>