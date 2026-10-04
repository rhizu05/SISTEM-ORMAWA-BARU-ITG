<section>
    <header class="flex items-start gap-4 mb-6">
        <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Data Tambahan Profil
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Lengkapi data pendukung seperti kontak, kepengurusan, dan tanda tangan digital.
            </p>
        </div>
    </header>

    <hr class="border-slate-100 mb-6">

    <form method="post" action="{{ route('profile.data.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="alamat" :value="in_array($user->roles->first()?->name, ['ormawa', 'bem', 'bpm']) ? __('Alamat Sekretariat') : __('Alamat')" />
            <x-text-input id="alamat" name="alamat" type="text" class="mt-1 block w-full" :value="old('alamat', $user->alamat)" />
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>

        <div>
            <x-input-label for="telepon" :value="__('Nomor Telepon / WA')" />
            <x-text-input id="telepon" name="telepon" type="text" class="mt-1 block w-full" :value="old('telepon', $user->telepon)" />
            <x-input-error class="mt-2" :messages="$errors->get('telepon')" />
        </div>

        @if(in_array($user->roles->first()?->name, ['ormawa', 'bem', 'bpm']))
        <hr class="my-4">
        
        <div>
            <x-input-label for="logo_ormawa" :value="__('Logo Ormawa (PNG/JPG)')" />
            @if($user->logo_ormawa)
                <img src="{{ asset('storage/' . $user->logo_ormawa) }}" alt="Logo" class="h-16 mb-2">
            @endif
            <input id="logo_ormawa" name="logo_ormawa" type="file" class="mt-1 block w-full border rounded p-1" accept="image/*" />
            <x-input-error class="mt-2" :messages="$errors->get('logo_ormawa')" />
        </div>

        <hr class="my-4">

        <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <div class="text-xs text-slate-700 leading-relaxed">
                    <strong class="font-semibold text-slate-900">Sistem Tanda Tangan Digital Resmi Aktif</strong><br>
                    Pengesahan proposal, surat, dan LPJ kini menggunakan tanda tangan digital berbasis token kriptografis dan QR Code. Data Nama dan NIM pejabat kepengurusan berikut digunakan sebagai identitas penandatangan otomatis pada dokumen resmi.
                </div>
            </div>
        </div>

        <!-- Pejabat Kepengurusan -->
        <div class="space-y-5">
            <!-- Ketua -->
            <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span> Ketua Ormawa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nama_ketua" :value="__('Nama Lengkap Ketua')" />
                        <x-text-input id="nama_ketua" name="nama_ketua" type="text" class="mt-1 block w-full" :value="old('nama_ketua', $user->nama_ketua)" placeholder="Nama lengkap beserta gelar jika ada" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_ketua')" />
                    </div>
                    <div>
                        <x-input-label for="nim_ketua" :value="__('NIM Ketua')" />
                        <x-text-input id="nim_ketua" name="nim_ketua" type="text" class="mt-1 block w-full font-mono" :value="old('nim_ketua', $user->nim_ketua)" placeholder="Contoh: 2206001" />
                        <x-input-error class="mt-2" :messages="$errors->get('nim_ketua')" />
                    </div>
                </div>
            </div>

            <!-- Sekretaris -->
            <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Sekretaris Ormawa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nama_sekretaris" :value="__('Nama Lengkap Sekretaris')" />
                        <x-text-input id="nama_sekretaris" name="nama_sekretaris" type="text" class="mt-1 block w-full" :value="old('nama_sekretaris', $user->nama_sekretaris)" placeholder="Nama lengkap sekretaris" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_sekretaris')" />
                    </div>
                    <div>
                        <x-input-label for="nim_sekretaris" :value="__('NIM Sekretaris')" />
                        <x-text-input id="nim_sekretaris" name="nim_sekretaris" type="text" class="mt-1 block w-full font-mono" :value="old('nim_sekretaris', $user->nim_sekretaris)" placeholder="Contoh: 2206002" />
                        <x-input-error class="mt-2" :messages="$errors->get('nim_sekretaris')" />
                    </div>
                </div>
            </div>

            <!-- Bendahara -->
            <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Bendahara Ormawa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nama_bendahara" :value="__('Nama Lengkap Bendahara')" />
                        <x-text-input id="nama_bendahara" name="nama_bendahara" type="text" class="mt-1 block w-full" :value="old('nama_bendahara', $user->nama_bendahara)" placeholder="Nama lengkap bendahara" />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_bendahara')" />
                    </div>
                    <div>
                        <x-input-label for="nim_bendahara" :value="__('NIM Bendahara')" />
                        <x-text-input id="nim_bendahara" name="nim_bendahara" type="text" class="mt-1 block w-full font-mono" :value="old('nim_bendahara', $user->nim_bendahara)" placeholder="Contoh: 2206003" />
                        <x-input-error class="mt-2" :messages="$errors->get('nim_bendahara')" />
                    </div>
                </div>
            </div>

            <!-- Catatan Penggunaan di Dokumen -->
            <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-100/90 border border-slate-200 text-xs text-slate-600">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="leading-relaxed">
                    <strong class="text-slate-800">Catatan Dokumen:</strong> Nama Lengkap dan NIM Ketua, Sekretaris, serta Bendahara di atas akan otomatis tercantum pada <strong>Lembar Pengesahan Proposal Kegiatan</strong>, <strong>Laporan Pertanggungjawaban (LPJ)</strong>, dan <strong>Surat Keluar</strong>, serta terikat pada <strong>QR Code Tanda Tangan Digital</strong> resmi kampus.
                </p>
            </div>
        </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-data-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>