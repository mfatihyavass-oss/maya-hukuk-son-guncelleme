# Changelog

## 2.4.0 - 2026-10-25

- Ayarlar > Yazar bolumune `Fotograf gorunumu` eklendi: `Temaya gore` (varsayilan), `Kutu icinde` (ana sayfadaki portre gibi lila cerceve), `Cercevesiz`, `Yuvarlak`.
- Kart ve kutu renklerine `Baslik metni` (Yazar etiketi, unvan ve kutu basliklari) ve `Fotograf kutusu` eklendi; `Bio ve yasal uyari metni` rengi `Govde metni` olarak adlandirildi ve Paylas etiketini de kapsar.
- `Kutu basligi` rengi secilirse `Baslik metni` rengine gore onceliklidir.

## 2.3.0 - 2026-10-25

- Lila ve diger temalarda kutu ve kart renkleri secilemiyordu (eski metin/gradyan ayarlari yalnizca Lacivert temada calisiyordu).
- Ayarlara tum temalarda calisan `Kart ve kutu renkleri` bolumu eklendi: kart zemini, kart cercevesi, yazar adi, bio ve yasal uyari metni, tarih rozeti zemini ve yazisi, kutu zemini, kutu cercevesi, kutu sol seridi, kutu basligi, kutu metni, ilk buton zemini ve diger butonlarin zemini.
- Her renk `Ozel renk kullan` ile acilir; isaret kaldirilinca temanin rengine donulur. Canli onizleme aninda guncellenir.
- Gorunum ayarlari `Gorunum`, `Kart ve kutu renkleri` ve `Yerlesim` bolumlerine ayrildi.

## 2.2.0 - 2026-10-25

- Duzeltme: blok temalari yazi icindeki bloklari icerik genisligine (or. 645 px) daralttigi icin kart genis ekranda dar aciliyordu.
- Ayarlar > Gorunum'e `Kart genisligi` eklendi: `Genis` (varsayilan, en fazla 1200 px, ana sayfa bolumleriyle ayni), `Yazi metni kadar`, `Kapsayicinin tamami`.
- Blok hizalamasi (genis/tam) secildiginde genel ayar devre disi kalir; editor onizlemesi de ayni genisligi kullanir.
- Genis kartta bio metni satir sinirina takilmadan yayilir.

## 2.1.0 - 2026-10-25

- Ayarlar > Butonlar bolumune `Ilk buton yazi rengi` ve `Diger butonlarin yazi rengi` secenekleri eklendi.
- `Ozel renk kullan` isaretlenmezse renk temaya gore otomatik belirlenir; renk secildiginde kutu kendiliginden isaretlenir ve canli onizleme guncellenir.
- Secilen renkler tum temalarda ve fare ustune gelindiginde de korunur.
- Kaldirmada yeni ayarlar da temizlenir.

## 2.0.0 - 2026-10-25

### Yeni tasarım
- Kart bastan tasarlandi: altin ust cizgi, terazi filigrani, yazar fotografi veya bas harf rozeti, unvan satiri, simgeli tarih rozetleri, sol vurgulu bilgi kutulari.
- Dort tema: sitenin lila-beyaz-mor kimligine uygun `Lila` (varsayilan), `Lacivert & Altin`, `Acik kart` ve `Minimal cizgi`. Tema genel ayardan veya her blokta ayri secilebilir.
- Varsayilan vurgu rengi site moru `#5B3A91`; Lila temadaki tum tonlar bu renkten turetilir.
- Ilk buton vurgulu, digerleri ikincil gorunur; `tel:`, `mailto:`, WhatsApp ve Google Haritalar baglantilarina otomatik simge eklenir; istege bagli yeni sekmede acma.
- Mobilde butonlar tam genislik, bilgi kutulari iki sutun; yazdirma gorunumu eklendi.

### Yeni ozellikler
- Yazar fotografi (ortam kutuphanesinden), unvan ve yazar profil baglantisi.
- Yayin tarihi rozeti (guncelleme tarihinden farkliysa) ve okuma suresi rozeti.
- Paylasim dugmeleri (WhatsApp, LinkedIn, X, Facebook) ve baglanti kopyalama.
- Yasal uyari metni (hukuki danismanlik niteligi tasimaz notu).
- Uzun Turkce tarih bicimi (`25 Ekim 2026`) veya sayisal bicim (`25.10.2026`).
- Blok ayarlarindan bu karttaki temayi secme ve bolumleri (tarih, bio, bilgi kutulari, butonlar, paylasim, yasal uyari) gizleme.
- Secilen icerik turlerinde blogu olmayan yazilara karti otomatik ekleme ve `[maya_son_guncelleme]` kisa kodu (`tema` ve `gizle` parametreleriyle).
- Ayarlar sayfasi yeniden duzenlendi: bolumlu kartlar, siz yazdikca guncellenen canli onizleme, satirlari yukari/asagi tasima, kaydedilmemis degisiklik uyarisi.
- Yazi ve sayfa listelerine siralanabilir `Son guncelleme` sutunu: renkli guncellik etiketi ve `Kart yok` uyarisi.
- Baslangic ekrani raporu: ozet sayilar, sure filtresi (6 ay / esik / 24 ay / tumu), `Karti eksik icerikler` raporu, `Gor` baglantisi.
- Eklentiler listesine `Ayarlar` baglantisi.

### Duzeltilen sorunlar
- Editor onizlemesi artik canli sayfayla ayni sunucu ciktisini kullanir; iki ayri sablonun birbirinden kaymasi engellendi.
- Metin rengi degistirildiginde bio, bilgi kutusu ve tarih metinleri beyaz kalip okunmaz hale geliyordu; tum ikincil renkler artik secilen renklerden turetilir.
- Gradyan renkleri degistirildiginde orta ton sabit lacivert kaliyordu; orta ton artik secilen iki renkten hesaplanir.
- Yazi baglami olmayan yerlerde (widget, arsiv) blok bugunun tarihini gosteriyordu; artik tarih rozeti gizlenir.
- Tarih makine tarafindan okunabilir `<time datetime>` etiketiyle verilir; kart `<aside>`, bilgi kutulari `<dl>` olarak isaretlenir; `aria-label` gecersiz `div` kullanimlari duzeltildi.
- Rapor tum yazilari tek seferde yukluyordu; artik en fazla 100 kayit getirir ve toplam sayiyi ayrica gosterir.
- Ayarlar kaydedildiginde LiteSpeed, WP Rocket, W3 Total Cache ve WP Super Cache onbellekleri temizlenir; degisiklik tum yazilarda hemen gorunur.
- On yuz scripti `defer` ile yuklenir, imlec efekti yalnizca fare kullanan cihazlarda ve `requestAnimationFrame` ile calisir.
- Arayuz metinleri Turkce karakterlerle duzeltildi.
- Blok kenar bosluklari (margin) ve genis/tam hizalama destegi eklendi.
- Kaldirmada tum yeni ayarlar da temizlenir.

## 1.7.2 - 2026-07-18

- Bagimsiz guvenlik ve mantik incelemesinden gecmeyen 1.7.1 otomatik tarih kurtarma yaklasimi tamamen kaldirildi.
- `_wp_old_date` kayitlarinin kesin ilk yayin tarihi olmadigi kabul edilerek varsayima dayali hicbir otomatik veri degisikligi yapilmamasi saglandi.
- Eklentinin yonetici isteginde veya surum gecisinde `post_date` ve `post_date_gmt` alanlarini degistirmedigi regresyon testiyle dogrulandi.
- 1.7.0'daki guvenli davranis korunur: ilk yayin tarihi degismez, blok yalnizca son duzenlenme tarihini gosterir.

## 1.7.1 - 2026-07-18

- Otomatik tarih kurtarma denemesi bagimsiz incelemede guvenli bulunmadigi icin yayinlanmadan geri cekildi.

## 1.7.0 - 2026-07-18

- SEO ve yapilandirilmis veri uyumu icin ilk yayin tarihini son duzenlenme tarihiyle otomatik esitleyen davranis kaldirildi.
- `wp_insert_post_data` filtresi ve yayin tarihi esitleme AJAX islemi kaldirildi.
- Baslangic ekranindaki yayin tarihi uyumsuzluk raporu ile `Tarihi esitle` dugmesi kaldirildi.
- Blok, WordPress ilk yayin tarihini koruyarak son duzenlenme tarihini gostermeye devam eder.
- Son guncelleme tarihi eski icerikleri listeleyen rapor korunmustur.

## 1.6.1 - 2026-07-07

- Blok tasarimi koyu lacivert ve altin vurgulu stile guncellendi.
- On yuz icin imleci takip eden vurgu efekti, buton hover hareketi, ok animasyonu ve tiklama geri bildirimi eklendi.
- Varsayilan gradyan renkleri yeni stile uygun olarak guncellendi.

## 1.6.0 - 2026-07-07

- Global bio alani eklendi; bos birakildiginda blokta gizlenir.
- Varsayilan `Hakkimizda` ve `Iletisim` butonlari eklendi.
- Varsayilan `Baro` bilgi kutucugu eklendi.
- Butonlar ve bilgi kutucuklari ayarlar ekranindan cogaltilabilir, duzenlenebilir veya tamamen kaldirilabilir hale getirildi.

## 1.5.2 - 2026-06-07

- WordPress uyumluluk bilgisi 7.0 icin guncellendi.
- README'ye guncel destekli PHP surumleri ve WordPress 7.0 PHP gereksinimi notu eklendi.

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
