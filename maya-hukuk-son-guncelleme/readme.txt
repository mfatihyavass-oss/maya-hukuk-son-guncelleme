=== Maya Hukuk Son Guncelleme Bloku ===
Contributors: mfatihyavass
Tags: gutenberg, block, update, date, hukuk
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg icin dinamik "Son Guncelleme" blogu. Blok ekli yayinlanmis icerik kaydedildiginde yayin tarihi de son duzenlenme tarihine cekilir; yazar adi ve renkler merkezi ayardan yonetilir.

== Description ==

Maya Hukuk Son Guncelleme Bloku, sayfa ve yazilarinizda tek tikla standart bir son guncelleme alani gostermenizi saglar.

Blok, ilgili yazi/sayfanin son duzenlenme tarihini gosterir ve asagidaki global ayarlari kullanir:

- Yazar adi
- Metin rengi
- Gradyan baslangic rengi
- Gradyan bitis rengi

Boylece tum iceriklerde tek tip, kurumsal bir gorunum elde edilir.

== Installation ==

1. Eklenti dosyalarini `wp-content/plugins/maya-hukuk-son-guncelleme` klasorune yukleyin.
2. WordPress panelinden eklentiyi etkinlestirin.
3. `Ayarlar > Son Guncelleme Bloku` ekranindan yazar/renk ayarlarini kaydedin.
4. Yazinizda veya sayfanizda `Maya Hukuk - Son Guncelleme` blogunu ekleyin.

== Frequently Asked Questions ==

= Tarih her gun otomatik degisir mi? =

Hayir. Tarih, sadece icerik editorunde kaydedildiginde/guncellendiginde degisir.

= WordPress sag panelindeki yayin tarihi de degisir mi? =

Evet. Bu blok ekli olan yayinlanmis yazi/sayfa kaydedildiginde yayin tarihi, son duzenlenme tarihine eslenir.

= Eski yazilardaki uyumsuz tarihleri toplu gormek mumkun mu? =

Evet. WordPress Baslangic ekranindaki `Son Guncelleme Tarih Kontrolu` kutusunda `Kontrol et` dugmesine basin. Eklenti yalnizca Son Guncelleme blogu bulunan ve yayin tarihi son guncelleme tarihiyle uyumsuz olan yayinlanmis yazilari/sayfalari listeler. Her kayit editor linkiyle acilabilir veya listedeki dugmeyle editor acilmadan eslenebilir.

= Tarih manuel degistirilebilir mi? =

Bu surumde blokta manuel tarih alani yoktur. Tarih, yazi/sayfanin son duzenlenme tarihinden gelir.

= Her blokta ayri renk kullanabilir miyim? =

Bu surumde renkler globaldir. Ayarlar ekraninda yaptiginiz degisiklik tum bloklara yansir.

= Eklentiyi kaldirinca ayarlar silinir mi? =

Evet. Eklenti tamamen kaldirildiginda kayitli ayarlar temizlenir.

== Changelog ==

= 1.4.0 =

- WordPress Baslangic ekranina manuel tarih kontrol kutusu eklendi.
- Kontrol sonucu yalnizca Son Guncelleme blogu bulunan ve yayin tarihi son duzenlenme tarihiyle uyumsuz olan yayinlanmis icerikleri listeler.
- Her kayit icin editor linki ve editoru acmadan yayin tarihini esitleme dugmesi eklendi.

= 1.3.1 =

- Yayin tarihi esleme islemi kayittan sonra ayri veritabani guncellemesi yapmak yerine WordPress kaydi sirasinda uygulanir.

= 1.3.0 =

- Bu blok ekli yayinlanmis icerikler kaydedildiginde WordPress yayin tarihi, son duzenlenme tarihine otomatik eslenir.

= 1.2.0 =

- Editor onizlemesindeki tarih, canli sayfadaki gibi yazi/sayfanin son duzenlenme tarihinden okunur.
- Blok scripti WordPress editor verisi degistiginde tarihi otomatik yeniler.

= 1.1.0 =

- Tarih artik gunluk olarak degismez.
- Tarih, yazi/sayfanin son duzenlenme tarihinden okunur.

= 1.0.0 =

- Ilk surum
- Dinamik "Son Guncelleme" blok cikisi
- Global yazar ve renk ayarlari
