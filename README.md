# Maya Hukuk Son Guncelleme Bloku

WordPress Gutenberg editoru icin "Son Guncelleme" blogu ekler. Blok, her goruntulemede guncel tarihi dinamik olarak yazar ve ayarlar ekranindan belirlenen yazar adini/renklerini kullanir.

## Ozellikler

- Dinamik tarih: `Son Guncelleme DD.MM.YYYY`
- Global yazar adi ayari
- Metin rengi ayari
- Arka plan gradyan baslangic ve bitis rengi ayari
- Hem editor onizlemesinde hem de canli sayfada ayni tasarim

## Klasor Yapisi

```text
maya-hukuk-son-guncelleme/
  assets/
    editor.js
    style.css
  block.json
  maya-hukuk-son-guncelleme.php
  readme.txt
  uninstall.php
```

## Kurulum

1. Depoyu indir.
2. `maya-hukuk-son-guncelleme/` klasorunu `wp-content/plugins/` altina kopyala.
3. WordPress yonetim panelinde **Eklentiler** ekranindan eklentiyi aktif et.
4. **Ayarlar > Son Guncelleme Bloku** ekranindan yazar ve renk ayarlarini yap.
5. Yazida/sayfada blok eklerken **Maya Hukuk - Son Guncelleme** blogunu sec.

Alternatif olarak kokteki `maya-hukuk-son-guncelleme.zip` dosyasini WordPress uzerinden yukleyebilirsin.

## Teknik Notlar

- Blok adi: `maya-hukuk/son-guncelleme`
- Render sekli: PHP `render_callback` ile sunucu tarafli dinamik cikti
- Varsayilan yazar: `Av. Arb. M. Fatih Yavas`

## Lisans

Bu proje `GPL-2.0-or-later` lisansi ile yayinlanir.
