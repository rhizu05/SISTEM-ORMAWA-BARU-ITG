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

        <!-- Ketua -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="nama_ketua" :value="__('Nama Ketua')" />
                <x-text-input id="nama_ketua" name="nama_ketua" type="text" class="mt-1 block w-full" :value="old('nama_ketua', $user->nama_ketua)" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_ketua')" />
            </div>
            <div>
                <x-input-label for="nim_ketua" :value="__('NIM Ketua')" />
                <x-text-input id="nim_ketua" name="nim_ketua" type="text" class="mt-1 block w-full font-mono" :value="old('nim_ketua', $user->nim_ketua)" placeholder="Contoh: 2106001" />
                <x-input-error class="mt-2" :messages="$errors->get('nim_ketua')" />
            </div>
            <div>
                <x-input-label for="ttd_ketua" :value="__('TTD Ketua (PNG Transparan)')" />
                @if($user->ttd_ketua)
                    <img src="{{ asset('storage/' . $user->ttd_ketua) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_ketua" name="ttd_ketua" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
                <x-input-error class="mt-2" :messages="$errors->get('ttd_ketua')" />
            </div>
        </div>

        <!-- Sekretaris -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="nama_sekretaris" :value="__('Nama Sekretaris')" />
                <x-text-input id="nama_sekretaris" name="nama_sekretaris" type="text" class="mt-1 block w-full" :value="old('nama_sekretaris', $user->nama_sekretaris)" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_sekretaris')" />
            </div>
            <div>
                <x-input-label for="nim_sekretaris" :value="__('NIM Sekretaris')" />
                <x-text-input id="nim_sekretaris" name="nim_sekretaris" type="text" class="mt-1 block w-full font-mono" :value="old('nim_sekretaris', $user->nim_sekretaris)" placeholder="Contoh: 2106002" />
                <x-input-error class="mt-2" :messages="$errors->get('nim_sekretaris')" />
            </div>
            <div>
                <x-input-label for="ttd_sekretaris" :value="__('TTD Sekretaris (PNG)')" />
                @if($user->ttd_sekretaris)
                    <img src="{{ asset('storage/' . $user->ttd_sekretaris) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_sekretaris" name="ttd_sekretaris" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
                <x-input-error class="mt-2" :messages="$errors->get('ttd_sekretaris')" />
            </div>
        </div>

        <!-- Bendahara -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="nama_bendahara" :value="__('Nama Bendahara')" />
                <x-text-input id="nama_bendahara" name="nama_bendahara" type="text" class="mt-1 block w-full" :value="old('nama_bendahara', $user->nama_bendahara)" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_bendahara')" />
            </div>
            <div>
                <x-input-label for="nim_bendahara" :value="__('NIM Bendahara')" />
                <x-text-input id="nim_bendahara" name="nim_bendahara" type="text" class="mt-1 block w-full font-mono" :value="old('nim_bendahara', $user->nim_bendahara)" placeholder="Contoh: 2106003" />
                <x-input-error class="mt-2" :messages="$errors->get('nim_bendahara')" />
            </div>
            <div>
                <x-input-label for="ttd_bendahara" :value="__('TTD Bendahara (PNG)')" />
                @if($user->ttd_bendahara)
                    <img src="{{ asset('storage/' . $user->ttd_bendahara) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_bendahara" name="ttd_bendahara" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
                <x-input-error class="mt-2" :messages="$errors->get('ttd_bendahara')" />
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