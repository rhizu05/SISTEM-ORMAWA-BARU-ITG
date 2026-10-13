{{-- Kop Surat Resmi Ormawa / HIMA / UKM (Sesuai Struktur Baku Kampus ITG) --}}
@php
    $owner = $user ?? ($letter->user ?? Auth::user());
    
    // Logo Kampus ITG (Lokal & URL)
    $logoItgLocal = null;
    if (file_exists(public_path('images/logos/logo_itg.png'))) {
        $logoItgLocal = public_path('images/logos/logo_itg.png');
    } elseif (file_exists(public_path('images/logo_itg.png'))) {
        $logoItgLocal = public_path('images/logo_itg.png');
    }
    $hasLogoItg = !empty($logoItgLocal);
    $logoItgUrl = file_exists(public_path('images/logos/logo_itg.png')) ? asset('images/logos/logo_itg.png') : asset('images/logo_itg.png');

    // Logo Organisasi Ormawa (Lokal & URL)
    $logoOrmawaLocal = null;
    if (!empty($owner->logo_ormawa) && file_exists(public_path('storage/' . $owner->logo_ormawa))) {
        $logoOrmawaLocal = public_path('storage/' . $owner->logo_ormawa);
    } elseif (!empty($owner->logo_ormawa) && file_exists(storage_path('app/public/' . $owner->logo_ormawa))) {
        $logoOrmawaLocal = storage_path('app/public/' . $owner->logo_ormawa);
    }
    $logoOrmawaUrl = !empty($owner->logo_ormawa) ? asset('storage/' . $owner->logo_ormawa) : null;
    $isPdfMode = $isPdf ?? ($pdf ?? false);

    // Siapkan sumber gambar: untuk mode PDF (DomPDF), gunakan Base64 Data URI agar gambar 100% tersemat tanpa gagal network/path
    if ($isPdfMode) {
        $srcItg = null;
        if (!empty($logoItgLocal) && file_exists($logoItgLocal)) {
            $ext = pathinfo($logoItgLocal, PATHINFO_EXTENSION) ?: 'png';
            $srcItg = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoItgLocal));
        }
        $srcOrmawa = null;
        if (!empty($logoOrmawaLocal) && file_exists($logoOrmawaLocal)) {
            $ext = pathinfo($logoOrmawaLocal, PATHINFO_EXTENSION) ?: 'png';
            $srcOrmawa = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoOrmawaLocal));
        }
        $hasLogoItg = !empty($srcItg);
        $hasLogoOrmawa = !empty($srcOrmawa);
    } else {
        $srcItg = $logoItgUrl;
        $srcOrmawa = $logoOrmawaUrl;
        $hasLogoItg = !empty($logoItgLocal) || !empty($logoItgUrl);
        $hasLogoOrmawa = !empty($owner->logo_ormawa);
    }
@endphp
<table class="kop-table" style="width: 100%; border-collapse: collapse; margin-bottom: 4px; border: none;">
    <tr style="border: none;">
        {{-- Logo Kiri: Logo Resmi Kampus ITG --}}
        <td style="width: 80px; text-align: center; vertical-align: middle; border: none; padding: 0;">
            @if($hasLogoItg)
                <img src="{{ $srcItg }}" class="kop-logo" alt="Logo ITG" width="75" height="75" style="width: 75px; height: 75px; max-width: 75px; max-height: 75px; object-fit: contain; display: block; margin: 0 auto;">
            @else
                <div style="width: 75px; height: 75px;"></div>
            @endif
        </td>

        {{-- Teks Tengah KOP HIMA/UKM (4 Baris Standar) --}}
        <td class="kop-text" style="text-align: center; vertical-align: middle; border: none; padding: 0 10px;">
            {{-- 1. Nama Ormawa / HIMA / UKM --}}
            <div class="kop-1" style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #0b1528; letter-spacing: 0.5px; line-height: 1.25; margin-bottom: 2px;">
                {{ $owner->nama_kop ?: $owner->name }}
            </div>

            {{-- 2. Nama Kampus ITG --}}
            <div class="kop-2" style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #0b1528; letter-spacing: 0.5px; line-height: 1.25; margin-bottom: 2px;">
                INSTITUT TEKNOLOGI GARUT
            </div>

            {{-- 3. Lokasi Kampus --}}
            <div class="kop-3" style="font-size: 8.5pt; color: #333; line-height: 1.25;">
                {{ $owner->alamat ?: 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151' }}{{ !empty($owner->telepon) ? ' Telepon/WA: ' . $owner->telepon : '' }}
            </div>

            {{-- 4. Website & Email Ormawa (fallback kampus) --}}
            <div class="kop-4" style="font-size: 8pt; color: #555; font-style: italic; margin-top: 1px;">
                Website: {{ $owner->website ?: 'www.itg.ac.id' }} | Email: {{ $owner->email_kop ?: ($owner->email ?? 'info@itg.ac.id') }}
            </div>
        </td>

        {{-- Logo Kanan: Logo Organisasi / Ormawa --}}
        <td style="width: 80px; text-align: center; vertical-align: middle; border: none; padding: 0;">
            @if($hasLogoOrmawa)
                <img src="{{ $srcOrmawa }}" class="kop-logo" alt="Logo Ormawa" width="75" height="75" style="width: 75px; height: 75px; max-width: 75px; max-height: 75px; object-fit: contain; display: block; margin: 0 auto;">
            @else
                {{-- Spacer penyeimbang simetris agar teks kop presisi di tengah --}}
                <div style="width: 75px; height: 75px;"></div>
            @endif
        </td>
    </tr>
</table>
<div class="garis-kop" style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 4px 0 16px 0;"></div>
