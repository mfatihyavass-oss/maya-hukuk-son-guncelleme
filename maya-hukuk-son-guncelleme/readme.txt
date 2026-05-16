=== Maya Hukuk Son Guncelleme Bloku ===
Contributors: mfatihyavass
Tags: gutenberg, block, update, date, hukuk
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg icin dinamik "Son Guncelleme" blogu. Tarihi yalnizca yazi/sayfa duzenlendiginde degisir; yazar adi ve renkler merkezi ayardan yonetilir.

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

= Tarih manuel degistirilebilir mi? =

Bu surumde blokta manuel tarih alani yoktur. Tarih, yazi/sayfanin son duzenlenme tarihinden gelir.

= Her blokta ayri renk kullanabilir miyim? =

Bu surumde renkler globaldir. Ayarlar ekraninda yaptiginiz degisiklik tum bloklara yansir.

= Eklentiyi kaldirinca ayarlar silinir mi? =

Evet. Eklenti tamamen kaldirildiginda kayitli ayarlar temizlenir.

== Changelog ==

= 1.1.0 =

- Tarih artik gunluk olarak degismez.
- Tarih, yazi/sayfanin son duzenlenme tarihinden okunur.

= 1.0.0 =

- Ilk surum
- Dinamik "Son Guncelleme" blok cikisi
- Global yazar ve renk ayarlari
