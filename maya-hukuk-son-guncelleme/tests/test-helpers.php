<?php
/**
 * Tests for the pure helper functions used when rendering the card.
 * Run: php tests/test-helpers.php
 */

define('ABSPATH', __DIR__ . '/');
define('DAY_IN_SECONDS', 86400);

function add_action() {}
function add_filter() {}
function plugin_basename($file) {
    return basename($file);
}
function register_activation_hook() {}

require dirname(__DIR__) . '/maya-hukuk-son-guncelleme.php';

$class = 'Maya_Hukuk_Son_Guncelleme';
$failures = array();

function mh_assert_same($expected, $actual, $label) {
    global $failures;

    if ($expected !== $actual) {
        $failures[] = sprintf('%s: beklenen %s, gelen %s', $label, var_export($expected, true), var_export($actual, true));
    }
}

$date = new DateTimeImmutable('2026-08-05 14:30:00');
mh_assert_same('5 Ağustos 2026', $class::format_date($date), 'Uzun Türkçe tarih');
mh_assert_same('05.08.2026', $class::format_date($date, 'numeric'), 'Sayısal tarih');

mh_assert_same('FY', $class::get_initials('Av. Arb. M. Fatih Yavaş'), 'Unvanlar atlanarak baş harfler');
mh_assert_same('İÖ', $class::get_initials('ilker özdemir'), 'Türkçe büyük harf');
mh_assert_same('A', $class::get_initials('Ayşe'), 'Tek kelime');
mh_assert_same('MH', $class::get_initials('Av. Dr.'), 'Yalnızca unvan');

mh_assert_same('phone', $class::detect_button_icon('tel:+902240000000'), 'Telefon simgesi');
mh_assert_same('mail', $class::detect_button_icon('mailto:info@example.com'), 'E-posta simgesi');
mh_assert_same('whatsapp', $class::detect_button_icon('https://wa.me/905000000000'), 'WhatsApp simgesi');
mh_assert_same('pin', $class::detect_button_icon('https://maps.app.goo.gl/abc'), 'Harita simgesi');
mh_assert_same('arrow', $class::detect_button_icon('/iletisim/'), 'Varsayılan ok');

mh_assert_same(0, $class::estimate_reading_minutes('   '), 'Boş metin');
mh_assert_same(1, $class::estimate_reading_minutes('kısa bir metin'), 'Kısa metin en az 1 dk');
mh_assert_same(3, $class::estimate_reading_minutes(trim(str_repeat('kelime ', 401))), '401 kelime 3 dk');

mh_assert_same('bugün', $class::format_age(3600), 'Aynı gün');
mh_assert_same('12 gün önce', $class::format_age(12 * DAY_IN_SECONDS), 'Gün');
mh_assert_same('3 ay önce', $class::format_age(95 * DAY_IN_SECONDS), 'Ay');
mh_assert_same('2 yıl 1 ay önce', $class::format_age(762 * DAY_IN_SECONDS), 'Yıl ve ay');

$links = $class::get_share_links('https://example.com/yazi/', 'Kira & Tahliye');
mh_assert_same('https://www.linkedin.com/sharing/share-offsite/?url=https%3A%2F%2Fexample.com%2Fyazi%2F', $links['linkedin']['url'], 'LinkedIn bağlantısı');
mh_assert_same(true, strpos($links['whatsapp']['url'], 'Kira%20%26%20Tahliye') !== false, 'WhatsApp metni kodlanır');

mh_assert_same('', $class::icon('olmayan'), 'Bilinmeyen simge');
mh_assert_same(0, strpos($class::icon('phone'), '<svg'), 'Simge SVG döner');

if ($failures) {
    fwrite(STDERR, "FAIL\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS: Yardımcı fonksiyonlar beklendiği gibi çalışıyor.\n");
