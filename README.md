# Maya Hukuk Son Guncelleme Bloku

WordPress Gutenberg editoru icin "Son Guncelleme" blogu ekler. Blok, yazinin son duzenlenme tarihini gosterir ve ayarlar ekranindan belirlenen yazar adini/renklerini kullanir.

Guncel surum: `1.4.0`

## Ozellikler

- Yazi son kaydedildiginde/duzenlendiginde guncellenen tarih: `Son Guncelleme DD.MM.YYYY`
- Blok ekli yayinlanmis iceriklerde WordPress yayin tarihini son duzenlenme tarihine otomatik esleme
- WordPress Baslangic ekraninda manuel tarih uyumsuzlugu kontrolu
- Uyumsuz kayitlar icin editor linki ve editoru acmadan tarih esitleme dugmesi
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

## Tarih Davranisi

- Blok, tarihini ilgili yazi/sayfanin son duzenlenme tarihinden alir.
- Yazi her gun otomatik degismedigi surece tarih de her gun otomatik degismez.
- Bu blok bulunan yayinlanmis bir yazi/sayfa kaydedildiginde WordPress yayin tarihi de son duzenlenme tarihine eslenir.
- Daha once blok eklenmis eski yazilar, tekrar kaydedilmedigi surece otomatik degismez.
- Taslak, revizyon, otomatik kayit ve zamanlanmis iceriklerde yayin tarihi eslemesi yapilmaz.
- Baslangic ekranindaki kontrol kutusu otomatik calismaz; `Kontrol et` dugmesine basildiginda yalnizca uyumsuz kayitlari listeler.
- Listedeki her kayit editor linkiyle acilabilir veya `Tarihi esitle` dugmesiyle editor acilmadan duzeltilebilir.

## Teknik Notlar

- Blok adi: `maya-hukuk/son-guncelleme`
- Render sekli: PHP `render_callback` ile sunucu tarafli dinamik cikti
- Tarih kaynagi: ilgili yazi/sayfanin `modified date` bilgisi; editor onizlemesi de ayni kaynagi izler
- Yayin tarihi esleme: yalnizca `maya-hukuk/son-guncelleme` blogu bulunan yayinlanmis icerikler kaydedilirken calisir
- Baslangic ekrani kontrolu: AJAX ile manuel calisir, yetki ve nonce kontrolu yapar
- Varsayilan yazar: `Av. Arb. M. Fatih Yavas`

## Lisans

Bu proje `GPL-2.0-or-later` lisansi ile yayinlanir.
