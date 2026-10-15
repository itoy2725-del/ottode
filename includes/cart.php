<?php
// Sepet işlemleri (cookie tabanlı - serverless uyumlu)
require_once __DIR__ . '/config.php';

function sepet(): array {
    return cookie_oku('sepet') ?? [];
}

function sepete_ekle(int $id, int $adet = 1): void {
    if (!urun_getir($id)) return;
    $s = sepet();
    $s[$id] = ($s[$id] ?? 0) + $adet;
    if ($s[$id] < 1) unset($s[$id]);
    cookie_yaz('sepet', $s);
}

function sepet_guncelle(int $id, int $adet): void {
    $s = sepet();
    if ($adet < 1) {
        unset($s[$id]);
    } else {
        $s[$id] = $adet;
    }
    cookie_yaz('sepet', $s);
}

function sepetten_cikar(int $id): void {
    $s = sepet();
    unset($s[$id]);
    cookie_yaz('sepet', $s);
}

function sepet_bosalt(): void {
    cookie_sil('sepet');
}

function sepet_kalemleri(): array {
    $kalemler = [];
    foreach (sepet() as $id => $adet) {
        $u = urun_getir((int)$id);
        if (!$u) continue;
        $kalemler[] = [
            'urun'     => $u,
            'adet'     => $adet,
            'arasumma' => $u['fiyat'] * $adet,
        ];
    }
    return $kalemler;
}

function sepet_toplam(): float {
    $t = 0.0;
    foreach (sepet_kalemleri() as $k) $t += $k['arasumma'];
    return $t;
}

function sepet_adet(): int {
    return array_sum(sepet());
}

function kargo_ucreti(float $araToplam): float {
    return ($araToplam >= 50 || $araToplam <= 0) ? 0.0 : 4.95;
}
