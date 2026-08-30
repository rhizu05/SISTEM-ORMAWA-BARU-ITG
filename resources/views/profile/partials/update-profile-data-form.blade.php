<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Data Tambahan Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui data tambahan seperti kontak, kepengurusan, dan tanda tangan digital untuk keperluan cetak surat.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.data.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="alamat" :value="__('Alamat Sekretariat')" />
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="nama_ketua" :value="__('Nama Ketua')" />
                <x-text-input id="nama_ketua" name="nama_ketua" type="text" class="mt-1 block w-full" :value="old('nama_ketua', $user->nama_ketua)" />
            </div>
            <div>
                <x-input-label for="ttd_ketua" :value="__('TTD Ketua (PNG Transparan)')" />
                @if($user->ttd_ketua)
                    <img src="{{ asset('storage/' . $user->ttd_ketua) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_ketua" name="ttd_ketua" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
            </div>
        </div>

        <!-- Sekretaris -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="nama_sekretaris" :value="__('Nama Sekretaris')" />
                <x-text-input id="nama_sekretaris" name="nama_sekretaris" type="text" class="mt-1 block w-full" :value="old('nama_sekretaris', $user->nama_sekretaris)" />
            </div>
            <div>
                <x-input-label for="ttd_sekretaris" :value="__('TTD Sekretaris (PNG)')" />
                @if($user->ttd_sekretaris)
                    <img src="{{ asset('storage/' . $user->ttd_sekretaris) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_sekretaris" name="ttd_sekretaris" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
            </div>
        </div>

        <!-- Bendahara -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="nama_bendahara" :value="__('Nama Bendahara')" />
                <x-text-input id="nama_bendahara" name="nama_bendahara" type="text" class="mt-1 block w-full" :value="old('nama_bendahara', $user->nama_bendahara)" />
            </div>
            <div>
                <x-input-label for="ttd_bendahara" :value="__('TTD Bendahara (PNG)')" />
                @if($user->ttd_bendahara)
                    <img src="{{ asset('storage/' . $user->ttd_bendahara) }}" alt="TTD" class="h-10 mb-1 border bg-gray-50">
                @endif
                <input id="ttd_bendahara" name="ttd_bendahara" type="file" class="mt-1 block w-full border rounded p-1 text-sm" accept=".png" />
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