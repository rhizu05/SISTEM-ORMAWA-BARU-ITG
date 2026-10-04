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
            selectedRole: 'ormawa',
            editUser: null,
            saldoUser: null 
        }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Pengguna Sistem</h3>
                    <p class="text-sm text-slate-500">Kelola akun pengguna, hak akses role organisasi, dan saldo pagu anggaran</p>
                </div>
                <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Tambah Pengguna
                </button>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama & Email</x-table.th>
                        <x-table.th>Username</x-table.th>
                        <x-table.th align="center">Role</x-table.th>
                        <x-table.th align="center">Status</x-table.th>
                        <x-table.th align="right">Saldo Pagu (Ormawa)</x-table.th>
                        <x-table.th align="center">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                    <x-table.tr>
                        <x-table.td>
                            <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            @if($user->file_sk)
                                <div class="mt-1 flex items-center gap-1.5">
                                    <a href="{{ route('dokumen.sk-ormawa', $user) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded border border-indigo-200 transition" title="Buka berkas Surat Keputusan resmi">
                                        <svg class="w-3 h-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>SK: {{ $user->nomor_sk ?: 'Lihat PDF' }}</span>
                                    </a>
                                </div>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60">{{ $user->username }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            @php
                                $roleName = $user->roles->first()?->name ?? 'None';
                                $roleClass = match($roleName) {
                                    'admin' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'wr3', 'bkhm', 'bendahara', 'sarpras' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'bem', 'bpm' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'ormawa' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase border {{ $roleClass }}">
                                {{ $roleName }}
                            </span>
                        </x-table.td>
                        <x-table.td align="center">
                            @if($user->status_akun == 'aktif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Nonaktif</span>
                            @endif
                        </x-table.td>
                        <x-table.td align="right">
                            @if(in_array($user->roles->first()?->name, ['ormawa', 'bem', 'bpm']))
                                <div class="font-mono font-bold text-slate-900">Rp {{ number_format($user->saldo, 0, ',', '.') }}</div>
                                <button @click="showSaldoModal = true; saldoUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'saldo' => (float)$user->saldo]) }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors mt-0.5">
                                    Atur Saldo &rarr;
                                </button>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </x-table.td>
                        <x-table.td align="center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="showEditModal = true; editUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'username' => $user->username, 'status_akun' => $user->status_akun, 'role' => $user->roles->first()?->name, 'nomor_sk' => $user->nomor_sk]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Edit</button>
                                
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                                </form>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="6" message="Belum ada pengguna sistem." />
                    @endforelse
                </tbody>
            </x-table>
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddModal = false"></div>
                <div class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-bold mb-4">Tambah Pengguna Baru</h3>
                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
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
                                <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" x-model="selectedRole" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bidang Khusus SK untuk Ormawa / BEM / BPM -->
                            <div x-show="['ormawa', 'bem', 'bpm'].includes(selectedRole)" x-transition class="space-y-4 pt-2 border-t border-slate-200">
                                <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-2">
                                    <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span><strong>Legalitas Dokumen:</strong> Berkas SK Kepengurusan resmi wajib dilampirkan oleh BKHM dan akan otomatis diarsipkan pada arsip surat ormawa serta arsip persuratan BKHM.</span>
                                </div>

                                <div>
                                    <x-input-label for="nomor_sk" value="Nomor SK Pengesahan Kepengurusan *" />
                                    <x-text-input id="nomor_sk" name="nomor_sk" type="text" class="mt-1 block w-full text-xs font-mono" placeholder="Contoh: 015/SK/ITG-BKHM/2026" x-bind:required="['ormawa', 'bem', 'bpm'].includes(selectedRole)" />
                                    <x-input-error :messages="$errors->get('nomor_sk')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="tanggal_sk" value="Tanggal Pengesahan SK" />
                                    <x-text-input id="tanggal_sk" name="tanggal_sk" type="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full text-xs" />
                                    <x-input-error :messages="$errors->get('tanggal_sk')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="file_sk" value="Berkas Dokumen SK Resmi (PDF, Maks 10MB) *" />
                                    <input id="file_sk" name="file_sk" type="file" accept=".pdf" class="mt-1 block w-full text-xs text-slate-700 border border-slate-300 rounded-lg p-2 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" x-bind:required="['ormawa', 'bem', 'bpm'].includes(selectedRole)" />
                                    <p class="text-[11px] text-slate-500 mt-1">Format wajib PDF. Diperoleh dari ormawa untuk diverifikasi BKHM.</p>
                                    <x-input-error :messages="$errors->get('file_sk')" class="mt-1" />
                                </div>
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
                    <form :action="'/admin/users/' + editUser?.id" method="POST" enctype="multipart/form-data">
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

                            <!-- Bidang SK pada Edit User -->
                            <div x-show="['ormawa', 'bem', 'bpm'].includes(editUser?.role)" x-transition class="space-y-4 pt-2 border-t border-slate-200">
                                <div>
                                    <x-input-label for="edit_nomor_sk" value="Nomor SK Pengesahan" />
                                    <x-text-input id="edit_nomor_sk" name="nomor_sk" type="text" class="mt-1 block w-full text-xs font-mono" x-model="editUser.nomor_sk" placeholder="Cth: 015/SK/ITG-BKHM/2026" />
                                </div>
                                <div>
                                    <x-input-label for="edit_file_sk" value="Perbarui Berkas SK (PDF, Kosongkan jika tetap)" />
                                    <input id="edit_file_sk" name="file_sk" type="file" accept=".pdf" class="mt-1 block w-full text-xs text-slate-700 border border-slate-300 rounded-lg p-2 bg-slate-50 focus:outline-none" />
                                    <p class="text-[11px] text-slate-500 mt-1">Kosongkan berkas ini jika tidak ingin mengubah dokumen SK yang sudah tersimpan.</p>
                                </div>
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

        <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900">Riwayat Perubahan Saldo</h3>
                <p class="text-xs text-slate-500">Log audit riwayat penyesuaian saldo pagu anggaran ormawa</p>
            </div>
            
            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Waktu Pencatatan</x-table.th>
                        <x-table.th>Target Akun</x-table.th>
                        <x-table.th>Aktor Eksekusi</x-table.th>
                        <x-table.th>Perubahan Nominal</x-table.th>
                        <x-table.th>Alasan / Catatan</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($saldoHistori as $history)
                    <x-table.tr>
                        <x-table.td>
                            <span class="text-xs text-slate-600 font-medium">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="font-semibold text-slate-900">{{ $history->user->name }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $history->actor->name }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="font-mono text-xs text-slate-500">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                            <span class="mx-1.5 text-slate-400">&rarr;</span>
                            <span class="font-mono font-bold text-xs text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-xs text-slate-600">{{ $history->catatan }}</span>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="5" message="Belum ada riwayat perubahan saldo." />
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </div>
</x-app-layout>