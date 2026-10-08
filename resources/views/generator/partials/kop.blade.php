{{-- Kop surat resmi dari Konfigurasi (Q-BKHM-07). Dipakai view PDF & pratinjau. --}}
<table class="kop-table" style="width: 100%; border-collapse: collapse; margin-bottom: 4px; border: none;">
    <tr style="border: none;">
        <td style="width: 80px; text-align: center; vertical-align: middle; border: none; padding: 0;">
            @if(!empty($konfig['kop_logo']) && file_exists(public_path('storage/' . $konfig['kop_logo'])))
                <img src="{{ public_path('storage/' . $konfig['kop_logo']) }}" class="kop-logo" alt="Logo ITG" width="75" height="75" style="width: 75px; height: 75px; max-width: 75px; max-height: 75px; object-fit: contain; display: block; margin: 0 auto;">
            @elseif(file_exists(public_path('images/logo_itg.png')))
                <img src="{{ public_path('images/logo_itg.png') }}" class="kop-logo" alt="Logo ITG" width="75" height="75" style="width: 75px; height: 75px; max-width: 75px; max-height: 75px; object-fit: contain; display: block; margin: 0 auto;">
            @else
                <div style="width: 75px; height: 75px;"></div>
            @endif
        </td>
        <td class="kop-text" style="text-align: center; vertical-align: middle; border: none; padding: 0 10px;">
            <div class="kop-1" style="font-size: 9.5pt; text-transform: uppercase; letter-spacing: 0.5px; color: #222; margin-bottom: 2px;">{{ $konfig['kop_baris1'] ?? 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI' }}</div>
            <div class="kop-2" style="font-size: 13.5pt; font-weight: bold; text-transform: uppercase; color: #0b1528; letter-spacing: 0.5px; margin-bottom: 2px;">{{ $konfig['kop_baris2'] ?? 'INSTITUT TEKNOLOGI GARUT' }}</div>
            <div class="kop-3" style="font-size: 8.5pt; color: #333; line-height: 1.25;">{{ $konfig['kop_baris3'] ?? 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773' }}</div>
            <div class="kop-4" style="font-size: 8pt; color: #555; font-style: italic; margin-top: 1px;">{{ $konfig['kop_baris4'] ?? 'Website : www.itg.ac.id | Email : info@itg.ac.id' }}</div>
        </td>
        <td style="width: 80px; text-align: center; vertical-align: middle; border: none; padding: 0;">
            {{-- Spacer penyeimbang simetris agar teks kop presisi di tengah --}}
            <div style="width: 80px;"></div>
        </td>
    </tr>
</table>
<div class="garis-kop" style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 4px 0 16px 0;"></div>
