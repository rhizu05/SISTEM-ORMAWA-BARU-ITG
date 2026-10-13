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
        <!-- Section: Pengaturan KOP Surat & Proposal Resmi -->
        <div class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-200">
            <div class="flex items-start gap-3 mb-4">
                <div class="p-2 bg-indigo-600 text-white rounded-xl shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-indigo-950 uppercase tracking-wide">Pengaturan KOP Surat &amp; Proposal Resmi</h3>
                    <p class="text-xs text-indigo-700 leading-relaxed mt-0.5">
                        Konfigurasi identitas 4 baris KOP resmi yang tercetak pada seluruh dokumen proposal kegiatan, surat-menyurat, dan LPJ.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="nama_kop" :value="__('Nama Organisasi pada KOP (Baris 1)')" />
                    <x-text-input id="nama_kop" name="nama_kop" type="text" class="mt-1 block w-full uppercase" :value="old('nama_kop', $user->nama_kop)" placeholder="Contoh: HIMPUNAN MAHASISWA TEKNIK INFORMATIKA" />
                    <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika ingin menggunakan nama akun organisasi (<strong>{{ $user->name }}</strong>).</p>
                    <x-input-error class="mt-2" :messages="$errors->get('nama_kop')" />
                </div>

                <div>
                    <x-input-label for="prodi" :value="__('Program Studi (Khusus HIMA)')" />
                    <x-text-input id="prodi" name="prodi" type="text" class="mt-1 block w-full" :value="old('prodi', $user->prodi)" placeholder="Contoh: Teknik Informatika" />
                    <x-input-error class="mt-2" :messages="$errors->get('prodi')" />
                </div>

                <div>
                    <x-input-label for="periode_kepengurusan" :value="__('Periode Kepengurusan')" />
                    <x-text-input id="periode_kepengurusan" name="periode_kepengurusan" type="text" class="mt-1 block w-full" :value="old('periode_kepengurusan', $user->periode_kepengurusan)" placeholder="Contoh: Periode 2025/2026" />
                    <x-input-error class="mt-2" :messages="$errors->get('periode_kepengurusan')" />
                </div>

                <div>
                    <x-input-label for="website" :value="__('Alamat Website (Baris 4, Opsional)')" />
                    <x-text-input id="website" name="website" type="text" class="mt-1 block w-full" :value="old('website', $user->website)" placeholder="Contoh: himatif.itg.ac.id" />
                    <p class="text-[11px] text-slate-500 mt-1">Jika kosong, otomatis menggunakan website kampus (<strong>www.itg.ac.id</strong>).</p>
                    <x-input-error class="mt-2" :messages="$errors->get('website')" />
                </div>

                <div>
                    <x-input-label for="email_kop" :value="__('Email Resmi Organisasi pada KOP (Baris 4, Opsional)')" />
                    <x-text-input id="email_kop" name="email_kop" type="text" class="mt-1 block w-full" :value="old('email_kop', $user->email_kop)" placeholder="Contoh: himatif@itg.ac.id" />
                    <p class="text-[11px] text-slate-500 mt-1">Jika kosong, otomatis menggunakan email akun (<strong>{{ $user->email }}</strong>) atau info@itg.ac.id.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('email_kop')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="logo_ormawa" :value="__('Logo Ormawa (PNG/JPG)')" />
                    @if($user->logo_ormawa)
                        <div class="flex items-center gap-3 my-2">
                            <img src="{{ asset('storage/' . $user->logo_ormawa) }}" alt="Logo" class="h-14 w-14 object-contain p-1 border rounded-lg bg-white shadow-sm">
                            <span class="text-xs text-slate-500">Logo ormawa aktif terpasang pada KOP dokumen resmi.</span>
                        </div>
                    @endif
                    <input id="logo_ormawa" name="logo_ormawa" type="file" class="mt-1 block w-full border border-slate-300 rounded-lg p-1.5 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*" />
                    <x-input-error class="mt-2" :messages="$errors->get('logo_ormawa')" />
                </div>
            </div>

            <!-- Live Preview Mini Card KOP -->
            <div class="mt-5 pt-4 border-t border-indigo-200/70">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-900 block mb-2">Pratinjau KOP Dokumen Resmi (Logo ITG di Kiri, Logo Ormawa di Kanan):</span>
                <div class="bg-white rounded-xl p-4 border border-slate-300 shadow-sm" style="font-family: 'Times New Roman', Times, serif;">
                    <div class="flex items-center justify-between gap-3">
                        <div class="w-12 h-12 shrink-0 flex items-center justify-center">
                            @if(file_exists(public_path('images/logos/logo_itg.png')) || file_exists(public_path('images/logo_itg.png')))
                                <img src="{{ file_exists(public_path('images/logos/logo_itg.png')) ? asset('images/logos/logo_itg.png') : asset('images/logo_itg.png') }}" alt="Logo ITG" class="max-h-12 max-w-12 object-contain">
                            @else
                                <div class="w-12 h-12 border border-dashed rounded flex items-center justify-center text-[9px] text-slate-400">ITG</div>
                            @endif
                        </div>
                        <div class="flex-1 text-center">
                            <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase; color: #0b1528;">
                                {{ $user->nama_kop ?: $user->name }}
                            </div>
                            <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase; color: #0b1528;">
                                INSTITUT TEKNOLOGI GARUT
                            </div>
                            <div style="font-size: 8pt; color: #333; line-height: 1.3;">
                                {{ $user->alamat ?: 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151' }}{{ !empty($user->telepon) ? ' Telepon/WA: ' . $user->telepon : '' }}
                            </div>
                            <div style="font-size: 7.5pt; color: #555; font-style: italic;">
                                Website: {{ $user->website ?: 'www.itg.ac.id' }} | Email: {{ $user->email_kop ?: ($user->email ?: 'info@itg.ac.id') }}
                            </div>
                        </div>
                        <div class="w-12 h-12 shrink-0 flex items-center justify-center">
                            @if($user->logo_ormawa)
                                <img src="{{ asset('storage/' . $user->logo_ormawa) }}" alt="Logo Ormawa" class="max-h-12 max-w-12 object-contain">
                            @else
                                <div class="w-12 h-12 border border-dashed border-slate-200 rounded flex items-center justify-center text-[8px] text-slate-300">Ormawa</div>
                            @endif
                        </div>
                    </div>
                    <div style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 2px; margin-top: 8px;"></div>
                </div>
            </div>
        </div>

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