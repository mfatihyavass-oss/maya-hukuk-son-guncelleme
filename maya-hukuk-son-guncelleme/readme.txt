=== Maya Hukuk Son Guncelleme Bloku ===
Contributors: mfatihyavass
Tags: gutenberg, block, update, date, hukuk
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg icin dinamik "Son Guncelleme" ve yazar karti. Yayin tarihini degistirmeden son duzenlenme tarihini gosterir; yazar, fotograf, butonlar, paylasim, yasal uyari ve tema merkezi ayardan yonetilir.

== Description ==

Maya Hukuk Son Guncelleme Bloku, sayfa ve yazilarinizda tek tikla standart bir son guncelleme alani gostermenizi saglar.

Blok, ilgili yazi/sayfanin son duzenlenme tarihini gosterir ve asagidaki global ayarlari kullanir:

- Yazar adi, unvan, profil baglantisi ve fotograf
- Bio alani
- Simgeli butonlar (telefon, e-posta, WhatsApp, harita algilama)
- Bilgi kutulari
- Paylasim ve baglanti kopyalama
- Yasal uyari
- Dort tema (varsayilan: site renklerine uygun Lila) ve renk ayarlari
- Yayin tarihi ve okuma suresi rozetleri
- Otomatik ekleme ve kisa kod
- Yazi listesinde guncellik sutunu ve Baslangic ekrani raporlari

Boylece tum iceriklerde tek tip, kurumsal bir gorunum elde edilir.

== Installation ==

1. Eklenti dosyalarini `wp-content/plugins/maya-hukuk-son-guncelleme` klasorune yukleyin.
2. WordPress panelinden eklentiyi etkinlestirin.
3. `Ayarlar > Son Guncelleme Bloku` ekranindan yazar, bio, buton, bilgi kutusu ve renk ayarlarini kaydedin.
4. Yazinizda veya sayfanizda `Maya Hukuk - Son Guncelleme` blogunu ekleyin.

== Frequently Asked Questions ==

= Tarih her gun otomatik degisir mi? =

Hayir. Tarih, sadece icerik editorunde kaydedildiginde/guncellendiginde degisir.

= WordPress sag panelindeki yayin tarihi degisir mi? =

Hayir. Ilk yayin tarihi korunur; blok yalnizca son duzenlenme tarihini gosterir.

= Guncel olmayan yazilari nasil gorurum? =

Baslangic ekranindaki `Guncel olmayanlari kontrol et` dugmesi Son Guncelleme blogu olsun olmasin tum yayinlanmis yazilari/sayfalari son guncelleme tarihi en eski olandan baslayarak listeler. Bu raporda tarih esitleme dugmesi yoktur; `Yaziyi guncelle` dugmesi ilgili editoru acar.

= Raporlar temizlenebilir mi? =

Evet. Raporda `Raporu sil` dugmesi vardir.

= Tarih manuel degistirilebilir mi? =

Bu surumde blokta manuel tarih alani yoktur. Tarih, yazi/sayfanin son duzenlenme tarihinden gelir.

= Her blokta ayri gorunum kullanabilir miyim? =

Evet. Blok ayarlarindan o karta ozel tema secebilir ve bolumleri gizleyebilirsiniz. Renkler ve icerik genel ayardan gelir.

= Blogu tum yazilara tek tek eklemem gerekir mi? =

Hayir. Ayarlar ekranindaki `Karti otomatik ekle` seceneginden icerik turlerini secerseniz blogu olmayan yazilarin sonuna kart otomatik eklenir. Blogu zaten iceren yazilarda iki kez gosterilmez.

= Varsayilan butonlari veya Baro kutusunu kaldirabilir miyim? =

Evet. Ayarlar ekraninda buton veya bilgi kutusu satirlarini silebilir ya da bos birakabilirsiniz. Bos satirlar kaydedilmez ve on yuzde gorunmez.

= Eklentiyi kaldirinca ayarlar silinir mi? =

Evet. Eklenti tamamen kaldirildiginda kayitli ayarlar temizlenir.

== Changelog ==

= 2.0.0 =

- Kart bastan tasarlandi; site renklerine uygun varsayilan Lila tema dahil dort tema, yazar fotografi, unvan, simgeli tarih rozetleri.
- Paylasim, baglanti kopyalama, yasal uyari, okuma suresi ve yayin tarihi rozeti eklendi.
- Blok bazinda tema ve bolum gizleme; otomatik ekleme ve kisa kod.
- Canli onizlemeli yeni ayarlar sayfasi.
- Yazi listesinde guncellik sutunu; Baslangic raporunda ozet, sure filtresi ve karti eksik icerikler raporu.
- Renk turetme, editor/on yuz farki, baglamsiz tarih, erisilebilirlik, rapor performansi ve onbellek sorunlari duzeltildi.

= 1.7.2 =

- Guvenilir bicimde kanitlanamayan eski yayin tarihlerini otomatik degistiren 1.7.1 kurtarma yaklasimi tamamen kaldirildi.
- Eklenti hicbir otomatik gecis, yonetici istegi veya AJAX islemiyle yayin tarihini degistirmez.
- Ilk yayin tarihi korunur; blok yalnizca son duzenlenme tarihini gosterir.

= 1.7.1 =

- Guvenlik incelemesinde `_wp_old_date` kayitlarinin kesin ilk yayin tarihi olmadigi belirlendigi icin bu surum geri cekilmistir.

= 1.7.0 =

- Ilk yayin tarihini son duzenlenme tarihiyle otomatik degistiren davranis kaldirildi.
- Yayin tarihi esitleme raporu, AJAX islemi ve `Tarihi esitle` dugmesi kaldirildi.
- Blok, yayin tarihini koruyarak yalnizca son duzenlenme tarihini gostermeye devam eder.
- Guncel olmayan yazilar raporu korunmustur.

= 1.6.1 =

- Blok tasarimi koyu lacivert ve altin vurgulu yeni stile guncellendi.
- Buton hover hareketi, ok animasyonu ve tiklama geri bildirimi eklendi.
- On yuzde imleci takip eden hafif vurgu efekti eklendi.

= 1.6.0 =

- Global bio alani eklendi.
- Varsayilan Hakkimizda ve Iletisim butonlari eklendi.
- Varsayilan Baro bilgi kutucugu eklendi.
- Butonlar ve bilgi kutucuklari ayarlar ekranindan cogaltilabilir, duzenlenebilir veya kaldirilabilir hale getirildi.

= 1.5.2 =

- WordPress uyumluluk bilgisi 7.0 icin guncellendi.
- Dokumantasyona guncel PHP uyumluluk notu eklendi.

= 1.5.1 =

- Guncel olmayan yazilar raporu artik Son Guncelleme blogu olsun olmasin tum yayinlanmis yazilari/sayfalari listeler.

= 1.5.0 =

- Tarih uyumsuzlugu raporu saat farkini yok sayacak sekilde guncellendi.
- Uyumsuzluk raporu yil farki, ay farki ve gun farki olarak siralanir.
- Raporu sil dugmeleri eklendi.
- Son guncelleme tarihi en eski olan bloklu icerikler icin ayri `Guncel olmayan yazilar` raporu eklendi.
- Guncel olmayan yazilar raporuna `Yaziyi guncelle` editor linki eklendi.

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
