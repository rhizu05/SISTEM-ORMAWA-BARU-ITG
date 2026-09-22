{{-- Kop surat resmi dari Konfigurasi (Q-BKHM-07). Dipakai view PDF & pratinjau. --}}
<div class="kop">
    @if(!empty($konfig['kop_logo']) && file_exists(public_path('storage/' . $konfig['kop_logo'])))
        <img src="{{ public_path('storage/' . $konfig['kop_logo']) }}" class="kop-logo" alt="Logo ITG">
    @elseif(file_exists(public_path('images/logo_itg.png')))
        <img src="{{ public_path('images/logo_itg.png') }}" class="kop-logo" alt="Logo ITG">
    @else
        <div class="kop-logo"></div>
    @endif
    <div class="kop-text">
        <div class="kop-1">{{ $konfig['kop_baris1'] ?? 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI' }}</div>
        <div class="kop-2">{{ $konfig['kop_baris2'] ?? 'INSTITUT TEKNOLOGI GARUT' }}</div>
        <div class="kop-3">{{ $konfig['kop_baris3'] ?? 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773' }}</div>
        <div class="kop-4">{{ $konfig['kop_baris4'] ?? 'Website : www.itg.ac.id | Email : info@itg.ac.id' }}</div>
    </div>
    <div class="kop-logo"></div>
</div>
