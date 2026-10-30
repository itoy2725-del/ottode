# ottode

reisenthel ürünleri temalı basit PHP e-ticaret demosu. Eğitim/demo amaçlıdır; gerçek bir mağaza değildir ve gerçek ödeme almaz.

## Sayfalar
| Dosya | İşlev |
|-------|-------|
| `index.php` | Ana sayfa — ürün listesi + arama |
| `urun.php?id=` | Ürün detayı + sepete ekle |
| `sepet.php` | Sepet — adet güncelle / kaldır / boşalt |
| `adres.php` | Teslimat adresi formu |
| `odeme.php` | Ödeme yöntemi + sipariş onayı |

## Yapı
```
includes/   config.php, cart.php, header.php, footer.php
data/       products.php (ürün verisi)
assets/css/ style.css
*.php       sayfalar
vercel.json Vercel (vercel-php runtime) yapılandırması
```

Sepet ve adres `$_SESSION` ile tutulur. Ürünler `data/products.php` içindeki PHP dizisinden gelir (DB yok).

## Yerelde çalıştırma
```bash
php -S localhost:8000
```
Sonra tarayıcıda: http://localhost:8000

## Vercel'e deploy
Vercel PHP'yi [`vercel-php`](https://github.com/vercel-community/php) community runtime ile çalıştırır (`vercel.json` içinde tanımlı).

1. Bu repo'yu GitHub'a push et.
2. Vercel'de **Add New → Project → Import** ile repo'yu seç.
3. Framework Preset: **Other**. Ayarları değiştirmeden **Deploy**.

> Not: Vercel serverless'tır; `$_SESSION` dosyaları geçici (`/tmp`) alanda tutulur ve örnekler arası paylaşılmaz. Demo için yeterlidir; üretimde sepet/oturum için harici bir store (Redis, DB, imzalı cookie) gerekir.

## MySQL & Admin

MySQL sipariş kaydı ve yönetim paneli için aşağıdaki ortam değişkenlerini yerelde veya Vercel Project Settings içinde tanımlayın: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `ADMIN_USER`, `ADMIN_PASS`, `COOKIE_SECRET`. Şifreler kaynak koda eklenmez. `data/ca.pem` Aiven CA sertifikasıdır.

Yönetim paneline `/admin/` adresinden `ADMIN_USER` ve `ADMIN_PASS` ile ilk kez giriş yapın; sonrasında şifreyi Ayarlar bölümünden değiştirebilirsiniz.
