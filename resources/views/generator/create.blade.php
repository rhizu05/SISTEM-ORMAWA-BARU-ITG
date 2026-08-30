<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Proposal Otomatis') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="proposalGenerator()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('generator.store') }}" method="POST">
                @csrf
                
                <!-- BAB I: PENDAHULUAN -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-indigo-500">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Bab I: Pendahuluan & Detail Kegiatan</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="nama_kegiatan" value="Nama Kegiatan" />
                                <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" required />
                            </div>
                            
                            <div>
                                <x-input-label for="latar_belakang" value="Latar Belakang" />
                                <textarea id="latar_belakang" name="latar_belakang" rows="3" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full" required></textarea>
                            </div>
                            
                            <div>
                                <x-input-label for="tujuan" value="Tujuan Kegiatan" />
                                <textarea id="tujuan" name="tujuan" rows="2" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full" required></textarea>
                            </div>
                            
                            <div>
                                <x-input-label for="sasaran" value="Sasaran Peserta" />
                                <x-text-input id="sasaran" name="sasaran" type="text" class="mt-1 block w-full" required />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUSUNAN PANITIA -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Susunan Kepanitiaan</h3>
                            <button type="button" @click="addPanitia()" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">+ Tambah Panitia</button>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(pan, index) in panitia" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="'pan_jabatan['+index+']'" placeholder="Jabatan (cth: Ketua Pelaksana)" class="border-gray-300 rounded text-sm w-1/3" required>
                                    <input type="text" :name="'pan_nama['+index+']'" placeholder="Nama Lengkap" class="border-gray-300 rounded text-sm w-1/3" required>
                                    <input type="text" :name="'pan_nim['+index+']'" placeholder="NPM / NIM" class="border-gray-300 rounded text-sm w-1/4">
                                    <button type="button" @click="removePanitia(index)" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- RAB -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-green-500">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Rencana Anggaran Biaya (RAB)</h3>
                            <button type="button" @click="addRab()" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">+ Tambah Baris RAB</button>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex gap-2 text-xs font-semibold text-gray-500 uppercase px-1">
                                <div class="w-2/5">Uraian / Rincian</div>
                                <div class="w-1/6">Vol</div>
                                <div class="w-1/6">Satuan</div>
                                <div class="w-1/4">Harga Satuan (Rp)</div>
                                <div class="w-8"></div>
                            </div>
                            <template x-for="(item, index) in rab" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="'rab_rincian['+index+']'" placeholder="Nama barang/kebutuhan" class="border-gray-300 rounded text-sm w-2/5" required>
                                    <input type="number" :name="'rab_vol['+index+']'" placeholder="Vol" class="border-gray-300 rounded text-sm w-1/6" required min="1">
                                    <input type="text" :name="'rab_sat['+index+']'" placeholder="Pcs/Lbr/Box" class="border-gray-300 rounded text-sm w-1/6" required>
                                    <input type="number" :name="'rab_harga['+index+']'" placeholder="Harga satuan" class="border-gray-300 rounded text-sm w-1/4" required min="0">
                                    <button type="button" @click="removeRab(index)" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- PENUTUP -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Penutup</h3>
                        <textarea id="penutup" name="penutup" rows="3" class="border-gray-300 rounded-md shadow-sm block w-full" required>Demikian proposal ini kami susun sebagai kerangka acuan kegiatan. Besar harapan kami atas dukungan dan partisipasi dari semua pihak terkait.</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mb-10">
                    <a href="{{ route('generator.index') }}" class="py-2 px-4 border rounded text-gray-600">Batal</a>
                    <x-primary-button>Generate Dokumen Proposal</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function proposalGenerator() {
            return {
                panitia: [{}],
                rab: [{}],
                addPanitia() { this.panitia.push({}); },
                removePanitia(i) { if(this.panitia.length > 1) this.panitia.splice(i, 1); },
                addRab() { this.rab.push({}); },
                removeRab(i) { if(this.rab.length > 1) this.rab.splice(i, 1); }
            }
        }
    </script>
</x-app-layout>