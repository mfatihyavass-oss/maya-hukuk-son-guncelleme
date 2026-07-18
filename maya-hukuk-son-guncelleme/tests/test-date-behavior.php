<?php
/**
 * Minimal regression test for publication-date preservation.
 * Run: php tests/test-date-behavior.php
 */

define('ABSPATH', __DIR__ . '/');

$GLOBALS['mh_test_actions'] = array();
$GLOBALS['mh_test_filters'] = array();

function add_action($hook, $callback) {
    $GLOBALS['mh_test_actions'][$hook][] = $callback;
}

function add_filter($hook, $callback) {
    $GLOBALS['mh_test_filters'][$hook][] = $callback;
}

function register_activation_hook($file, $callback) {
    // Registration is irrelevant to this regression test.
}

require dirname(__DIR__) . '/maya-hukuk-son-guncelleme.php';

$failures = array();

if (!empty($GLOBALS['mh_test_filters']['wp_insert_post_data'])) {
    $failures[] = 'Eklenti wp_insert_post_data filtresine baglanarak yayin tarihini degistirebilir.';
}

if (!empty($GLOBALS['mh_test_actions']['wp_ajax_mh_sg_sync_publish_date'])) {
    $failures[] = 'Eklenti yayin tarihini son duzenlenme tarihiyle esitleyen AJAX islemini kaydediyor.';
}

foreach (($GLOBALS['mh_test_actions']['admin_init'] ?? array()) as $callback) {
    if (is_array($callback) && isset($callback[1]) && $callback[1] === 'maybe_upgrade') {
        $failures[] = 'Eklenti yonetici isteginde otomatik tarih kurtarma migrasyonu kaydediyor.';
    }
}

if ($failures) {
    fwrite(STDERR, "FAIL\n- " . implode("\n- ", $failures) . "\n");
    exit(1);
}

fwrite(STDOUT, "PASS: Yayin tarihi korunuyor ve otomatik tarih kurtarma calismiyor.\n");
