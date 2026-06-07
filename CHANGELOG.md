# Changelog

## 1.5.1 - 2026-06-07

- `Guncel olmayan yazilar` raporu, Son Guncelleme blogu olsun olmasin tum yayinlanmis yazilari/sayfalari kontrol edecek sekilde genisletildi.
- Rapor son guncelleme tarihi en eski olan icerikleri en ustte gostermeye devam eder.

## 1.5.0 - 2026-06-07

- Tarih uyumsuzlugu raporu saat farkini yok sayacak sekilde guncellendi; ayni gun icindeki saat farklari artik rapora girmez.
- Uyumsuzluk raporu yil farki, ay farki ve gun farki olarak gruplanip yil farklari en ustte, ay farklari ortada, gun farklari en altta gosterilir.
- Her rapora `Raporu sil` dugmesi eklendi.
- Son Guncelleme blogu bulunan yayinlanmis icerikleri son guncelleme tarihi en eski olandan baslayarak listeleyen ayri `Guncel olmayan yazilar` raporu eklendi.
- Guncel olmayan yazilar raporunda tarih esitleme yerine ilgili editoru acan `Yaziyi guncelle` dugmesi kullanilir.

## 1.4.0 - 2026-06-07

- WordPress Baslangic ekranina manuel `Son Guncelleme Tarih Kontrolu` kutusu eklendi.
- `Kontrol et` dugmesi, yalnizca Son Guncelleme blogu bulunan ve yayin tarihi son duzenlenme tarihiyle uyumsuz olan yayinlanmis yazilari/sayfalari listeler.
- Her uyumsuz kayit icin editor linki ve editor acilmadan yayin tarihini son duzenlenme tarihine esitleyen dugme eklendi.

## 1.3.1 - 2026-06-07

- Yayin tarihi eslemesi, kayittan sonra dogrudan veritabani guncellemesi yapmak yerine WordPress'in kayit verisi filtresinde uygulanacak sekilde duzeltildi.
- Boylece editor cevabi, cache ve kayit akisinda daha tutarli yayin tarihi davranisi elde edilir.

## 1.3.0 - 2026-06-07

- Bu blok ekli yayinlanmis yazi/sayfalar kaydedildiginde WordPress yayin tarihi, son duzenlenme tarihine otomatik eslenecek sekilde guncellendi.
- Taslak, revizyon, otomatik kayit ve zamanlanmis iceriklerde yayin tarihi eslemesi yapilmaz.

## 1.2.0 - 2026-06-07

- WordPress editor onizlemesindeki tarih, canli blok cikisi gibi yazi/sayfanin son duzenlenme tarihinden okunacak sekilde guncellendi.
- Editor verisi kayit sonrasi yenilendiginde sag paneldeki/preview alandaki tarih otomatik yenilenir.

## 1.1.0 - 2026-05-16

- "Son Guncelleme" tarihi, gunluk degisim yerine yazi/sayfanin son duzenlenme tarihinden okunacak sekilde guncellendi.
- Dinamik render tarafinda post baglamindan `postId` kullanimi eklendi.
- Blok metadata surumu `1.1.0` yapildi.

## 1.0.1 - 2026-05-16

- GitHub `README.md` eklendi.
- WordPress uyumlu `readme.txt` eklendi.
- Eklenti basligina lisans bilgisi eklendi.
- Kaldirma sirasinda ayarlari temizlemek icin `uninstall.php` eklendi.

## 1.0.0 - 2026-04-30

- Ilk yayinlanan surum.
- Gutenberg icin dinamik "Son Guncelleme" blogu.
- Yazar adi, metin rengi ve gradyan renkleri icin global ayarlar.
