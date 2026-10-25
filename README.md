# Maya Hukuk Son Guncelleme Bloku

WordPress Gutenberg editoru icin dinamik "Son Guncelleme" ve yazar karti. Kart, yazinin son duzenlenme tarihini gosterir; yazar, fotograf, unvan, bio, butonlar, bilgi kutulari, paylasim ve yasal uyari tek bir ayarlar ekranindan tum siteye uygulanir.

Guncel surum: `2.1.0`

## Ozellikler

### Kart
- `Son Guncelleme 25 Ekim 2026` rozeti; istege bagli yayin tarihi ve okuma suresi rozetleri
- Yazar fotografi (ortam kutuphanesinden) veya ad-soyad bas harflerinden olusan rozet
- Yazar adi, unvan (ornek: `Avukat · Arabulucu`) ve istege bagli yazar profil baglantisi
- Bos birakilabilen bio alani
- Butonlar: ilki vurgulu, digerleri ikincil; `tel:`, `mailto:`, WhatsApp ve Google Haritalar baglantilarina otomatik simge; yeni sekmede acma secenegi; ilk buton ve diger butonlar icin ayri yazi rengi
- Bilgi kutulari (Baro, uzmanlik, ofis vb.)
- Paylasim: WhatsApp, LinkedIn, X, Facebook ve baglanti kopyalama
- Yasal uyari metni
- Dort tema: `Lila` (varsayilan; sitenin lila-beyaz-mor ana sayfa kartlariyla ayni dil), `Lacivert & Altin`, `Acik kart`, `Minimal cizgi`; vurgu rengi (varsayilan `#5B3A91`), metin ve gradyan renkleri ayarlanabilir
- Mobil uyumlu, klavye ile kullanilabilir, hareket azaltma tercihine ve yazdirmaya uyumlu

### Blok bazinda ayarlar
- Her blokta tema secimi (`Genel ayari kullan` varsayilan)
- Her blokta bolum gizleme: tarih rozetleri, bio, bilgi kutulari, butonlar, paylasim, yasal uyari
- Editor onizlemesi canli sayfayla ayni sunucu ciktisini kullanir

### Yonetim araclari
- **Ayarlar > Son Guncelleme Bloku**: bolumlu ayarlar ve siz yazdikca guncellenen canli onizleme
- Secilen icerik turlerinde blogu olmayan yazilara karti otomatik ekleme
- `[maya_son_guncelleme]` kisa kodu (`tema="acik"`, `gizle="share,disclaimer"`)
- Yazi/sayfa listelerinde siralanabilir `Son guncelleme` sutunu: guncellik etiketi ve `Kart yok` uyarisi
- Baslangic ekrani raporu: ozet sayilar, sure filtresi, `Guncel olmayan icerikler` ve `Karti eksik icerikler` raporlari
- Ayarlar kaydedildiginde yaygin onbellek eklentileri (LiteSpeed, WP Rocket, W3 Total Cache, WP Super Cache) otomatik temizlenir

## Klasor Yapisi

```text
maya-hukuk-son-guncelleme/
  assets/
    dashboard.css   Baslangic raporu ve yazi listesi sutunu
    dashboard.js    Baslangic raporu
    editor.js       Blok editoru (ServerSideRender + blok ayarlari)
    settings.css    Ayarlar sayfasi
    settings.js     Ayarlar sayfasi: tekrarlayan satirlar, fotograf secici, canli onizleme
    style.css       Kart tasarimi (on yuz + editor)
    view.js         On yuz etkilesimleri (kopyalama, imlec efekti)
  tests/
    test-date-behavior.php
    test-helpers.php
  block.json
  maya-hukuk-son-guncelleme.php
  readme.txt
  uninstall.php
```

## Kurulum

1. Depoyu indir.
2. `maya-hukuk-son-guncelleme/` klasorunu `wp-content/plugins/` altina kopyala.
3. WordPress yonetim panelinde **Eklentiler** ekranindan eklentiyi aktif et.
4. **Ayarlar > Son Guncelleme Bloku** ekranindan yazar, fotograf, bio, buton, bilgi kutusu, yasal uyari ve gorunum ayarlarini yap.
5. Yazida/sayfada **Maya Hukuk - Son Guncelleme** blogunu ekle ya da otomatik eklemeyi ac.

Alternatif olarak kokteki `maya-hukuk-son-guncelleme.zip` dosyasini WordPress uzerinden yukleyebilirsin. 1.x surumunden guncellerken mevcut ayarlar korunur; yeni ayarlar varsayilan degerleriyle gelir.

## Uyumluluk

- WordPress: `6.3+`; `7.0` ile uyumlu.
- PHP: minimum `7.4`; `8.2`, `8.3`, `8.4` ve `8.5` ile uyumlu sozdizimi.

## Tarih Davranisi

- Kart, tarihini ilgili yazi/sayfanin son duzenlenme tarihinden alir; tarih her gun otomatik degismez.
- WordPress ilk yayin tarihi korunur; eklenti `post_date` ve `post_date_gmt` alanlarini degistirmez.
- Yazi baglami olmayan yerlerde (widget alanlari gibi) tarih rozeti gosterilmez.
- Baslangic ekranindaki raporlar otomatik calismaz; `Kontrol et` dugmesine basildiginda hazirlanir ve en eski 100 kaydi listeler.

## Testler

```bash
php maya-hukuk-son-guncelleme/tests/test-date-behavior.php
php maya-hukuk-son-guncelleme/tests/test-helpers.php
```

## Teknik Notlar

- Blok adi: `maya-hukuk/son-guncelleme`
- Render: PHP `render_callback` ile sunucu tarafli dinamik cikti; editor ayni ciktiyi `ServerSideRender` ile gosterir
- Blok nitelikleri: `theme` (bos = genel ayar), `hiddenSections` (`dates`, `bio`, `info`, `buttons`, `share`, `disclaimer`)
- Kisa kod: `[maya_son_guncelleme tema="" gizle=""]`
- Ayarlar degistiginde `mh_sg_settings_updated` eylemi tetiklenir
- AJAX islemleri yetki ve nonce kontrolu yapar

## Lisans

Bu proje `GPL-2.0-or-later` lisansi ile yayinlanir.
