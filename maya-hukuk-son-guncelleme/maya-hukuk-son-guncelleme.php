<?php
/**
 * Plugin Name:       Maya Hukuk Son Guncelleme Bloku
 * Description:       Gutenberg icin dinamik Son Guncelleme blogu ve global ayarlar.
 * Version:           1.5.2
 * Author:            Maya Hukuk
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maya-hukuk-son-guncelleme
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Maya_Hukuk_Son_Guncelleme {
    const BLOCK_NAME = 'maya-hukuk/son-guncelleme';
    const DASHBOARD_NONCE_ACTION = 'mh_sg_dashboard_date_audit';
    const AJAX_CHECK_ACTION = 'mh_sg_check_date_mismatches';
    const AJAX_OUTDATED_ACTION = 'mh_sg_check_outdated_posts';
    const AJAX_SYNC_ACTION = 'mh_sg_sync_publish_date';
    const OPTION_AUTHOR = 'mh_sg_author_name';
    const OPTION_TEXT_COLOR = 'mh_sg_text_color';
    const OPTION_GRADIENT_START = 'mh_sg_gradient_start';
    const OPTION_GRADIENT_END = 'mh_sg_gradient_end';
    const SETTINGS_GROUP = 'mh_sg_settings_group';
    const SETTINGS_PAGE_SLUG = 'mh-son-guncelleme-bloku';

    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dashboard_assets'));
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));
        add_action('wp_ajax_' . self::AJAX_CHECK_ACTION, array($this, 'ajax_check_date_mismatches'));
        add_action('wp_ajax_' . self::AJAX_OUTDATED_ACTION, array($this, 'ajax_check_outdated_posts'));
        add_action('wp_ajax_' . self::AJAX_SYNC_ACTION, array($this, 'ajax_sync_publish_date'));
        add_filter('wp_insert_post_data', array($this, 'sync_publish_date_before_save'), 20, 4);
    }

    public static function activate() {
        if (get_option(self::OPTION_AUTHOR) === false) {
            add_option(self::OPTION_AUTHOR, 'Av. Arb. M. Fatih Yavaş');
        }

        if (get_option(self::OPTION_TEXT_COLOR) === false) {
            add_option(self::OPTION_TEXT_COLOR, '#FFFFFF');
        }

        if (get_option(self::OPTION_GRADIENT_START) === false) {
            add_option(self::OPTION_GRADIENT_START, '#0B1530');
        }

        if (get_option(self::OPTION_GRADIENT_END) === false) {
            add_option(self::OPTION_GRADIENT_END, '#122A57');
        }
    }

    public function register_block() {
        $editor_script_handle = 'mh-sg-editor-script';
        $style_handle = 'mh-sg-style';

        wp_register_script(
            $editor_script_handle,
            plugins_url('assets/editor.js', __FILE__),
            array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-data', 'wp-i18n'),
            filemtime(plugin_dir_path(__FILE__) . 'assets/editor.js'),
            true
        );

        wp_register_style(
            $style_handle,
            plugins_url('assets/style.css', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/style.css')
        );

        wp_localize_script($editor_script_handle, 'mhSgSettings', array(
            'fallbackDate' => wp_date('d.m.Y'),
            'authorName' => $this->get_author_name(),
            'textColor' => $this->get_text_color(),
            'gradientStart' => $this->get_gradient_start_color(),
            'gradientEnd' => $this->get_gradient_end_color(),
        ));

        register_block_type(__DIR__ . '/block.json', array(
            'editor_script' => $editor_script_handle,
            'style' => $style_handle,
            'editor_style' => $style_handle,
            'render_callback' => array($this, 'render_block'),
        ));
    }

    public function render_block($attributes = array(), $content = '', $block = null) {
        $author_name = $this->get_author_name();
        $text_color = $this->get_text_color();
        $gradient_start = $this->get_gradient_start_color();
        $gradient_end = $this->get_gradient_end_color();

        $date_text = $this->get_last_updated_date($block);

        $styles = sprintf(
            '--mh-sg-text-color: %s; --mh-sg-gradient-start: %s; --mh-sg-gradient-end: %s;',
            esc_attr($text_color),
            esc_attr($gradient_start),
            esc_attr($gradient_end)
        );

        ob_start();
        ?>
        <div class="mh-sg-block" style="<?php echo esc_attr($styles); ?>">
            <p class="mh-sg-date"><?php echo esc_html('Son Güncelleme ' . $date_text); ?></p>
            <p class="mh-sg-author"><?php echo esc_html($author_name); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_settings() {
        register_setting(self::SETTINGS_GROUP, self::OPTION_AUTHOR, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_author_name'),
            'default' => 'Av. Arb. M. Fatih Yavaş',
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_TEXT_COLOR, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_text_color'),
            'default' => '#FFFFFF',
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_GRADIENT_START, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_gradient_start_color'),
            'default' => '#0B1530',
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_GRADIENT_END, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_gradient_end_color'),
            'default' => '#122A57',
        ));
    }

    public function register_settings_page() {
        add_options_page(
            'Son Guncelleme Bloku',
            'Son Guncelleme Bloku',
            'manage_options',
            self::SETTINGS_PAGE_SLUG,
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_dashboard_assets($hook_suffix) {
        if ($hook_suffix !== 'index.php' || !current_user_can('edit_posts')) {
            return;
        }

        $dashboard_script_handle = 'mh-sg-dashboard-script';
        $dashboard_style_handle = 'mh-sg-dashboard-style';

        wp_register_script(
            $dashboard_script_handle,
            plugins_url('assets/dashboard.js', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/dashboard.js'),
            true
        );

        wp_register_style(
            $dashboard_style_handle,
            plugins_url('assets/dashboard.css', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/dashboard.css')
        );

        wp_localize_script($dashboard_script_handle, 'mhSgDashboardAudit', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::DASHBOARD_NONCE_ACTION),
            'checkAction' => self::AJAX_CHECK_ACTION,
            'outdatedAction' => self::AJAX_OUTDATED_ACTION,
            'syncAction' => self::AJAX_SYNC_ACTION,
            'messages' => array(
                'checking' => 'Kontrol ediliyor...',
                'syncing' => 'Eşitleniyor...',
                'empty' => 'Uyumsuz yayın tarihi bulunan yazı/sayfa yok.',
                'outdatedEmpty' => 'Güncel olmayan yazı/sayfa bulunamadı.',
                'error' => 'İşlem tamamlanamadı. Lütfen tekrar deneyin.',
                'synced' => 'Yayın tarihi eşitlendi.',
            ),
        ));

        wp_enqueue_script($dashboard_script_handle);
        wp_enqueue_style($dashboard_style_handle);
    }

    public function register_dashboard_widget() {
        if (!current_user_can('edit_posts')) {
            return;
        }

        wp_add_dashboard_widget(
            'mh_sg_date_audit_dashboard_widget',
            'Son Güncelleme Tarih Kontrolü',
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget() {
        if (!current_user_can('edit_posts')) {
            return;
        }
        ?>
        <div class="mh-sg-dashboard-audit">
            <p>Bu kontrol, Son Güncelleme bloğu bulunan yayınlanmış yazı/sayfalarda yayın tarihi ile son güncelleme tarihi uyuşmayan kayıtları listeler.</p>
            <p>Kontrol ve eşitleme otomatik çalışmaz; işlem yalnızca butona bastığınızda yapılır.</p>

            <div class="mh-sg-dashboard-report" id="mh-sg-date-mismatch-report">
                <h3>Yayın tarihi uyumsuzluk raporu</h3>
                <p>Saat farkı dikkate alınmaz; yalnızca gün, ay veya yıl farklıysa listelenir.</p>
                <p>
                    <button type="button" class="button button-primary" id="mh-sg-check-date-mismatches">Kontrol et</button>
                    <button type="button" class="button" id="mh-sg-clear-date-mismatches">Raporu sil</button>
                    <span class="spinner" id="mh-sg-date-audit-spinner"></span>
                </p>
                <div id="mh-sg-date-audit-message" class="mh-sg-date-audit-message" aria-live="polite"></div>
                <div id="mh-sg-date-audit-results" class="mh-sg-date-audit-results"></div>
            </div>

            <div class="mh-sg-dashboard-report" id="mh-sg-outdated-report">
                <h3>Güncel olmayan yazılar raporu</h3>
                <p>Yayınlanmış tüm yazı/sayfaları son güncelleme tarihi en eski olandan başlayarak listeler.</p>
                <p>
                    <button type="button" class="button button-primary" id="mh-sg-check-outdated-posts">Güncel olmayanları kontrol et</button>
                    <button type="button" class="button" id="mh-sg-clear-outdated-posts">Raporu sil</button>
                    <span class="spinner" id="mh-sg-outdated-spinner"></span>
                </p>
                <div id="mh-sg-outdated-message" class="mh-sg-date-audit-message" aria-live="polite"></div>
                <div id="mh-sg-outdated-results" class="mh-sg-date-audit-results"></div>
            </div>
        </div>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $author_name = $this->get_author_name();
        $text_color = $this->get_text_color();
        $gradient_start = $this->get_gradient_start_color();
        $gradient_end = $this->get_gradient_end_color();
        ?>
        <div class="wrap">
            <h1>Son Guncelleme Bloku Ayarlari</h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::SETTINGS_GROUP); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::OPTION_AUTHOR); ?>">Yazar Adi</label></th>
                        <td>
                            <input
                                type="text"
                                id="<?php echo esc_attr(self::OPTION_AUTHOR); ?>"
                                name="<?php echo esc_attr(self::OPTION_AUTHOR); ?>"
                                value="<?php echo esc_attr($author_name); ?>"
                                class="regular-text"
                            />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::OPTION_TEXT_COLOR); ?>">Metin Rengi</label></th>
                        <td>
                            <input
                                type="color"
                                id="<?php echo esc_attr(self::OPTION_TEXT_COLOR); ?>"
                                name="<?php echo esc_attr(self::OPTION_TEXT_COLOR); ?>"
                                value="<?php echo esc_attr($text_color); ?>"
                            />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::OPTION_GRADIENT_START); ?>">Gradyan Baslangic Rengi</label></th>
                        <td>
                            <input
                                type="color"
                                id="<?php echo esc_attr(self::OPTION_GRADIENT_START); ?>"
                                name="<?php echo esc_attr(self::OPTION_GRADIENT_START); ?>"
                                value="<?php echo esc_attr($gradient_start); ?>"
                            />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr(self::OPTION_GRADIENT_END); ?>">Gradyan Bitis Rengi</label></th>
                        <td>
                            <input
                                type="color"
                                id="<?php echo esc_attr(self::OPTION_GRADIENT_END); ?>"
                                name="<?php echo esc_attr(self::OPTION_GRADIENT_END); ?>"
                                value="<?php echo esc_attr($gradient_end); ?>"
                            />
                        </td>
                    </tr>
                </table>

                <?php submit_button('Kaydet'); ?>
            </form>
        </div>
        <?php
    }

    public function sanitize_author_name($value) {
        $sanitized = sanitize_text_field($value);

        if ($sanitized === '') {
            return 'Av. Arb. M. Fatih Yavaş';
        }

        return $sanitized;
    }

    public function sanitize_text_color($color) {
        return $this->sanitize_color_with_default($color, '#FFFFFF');
    }

    public function sanitize_gradient_start_color($color) {
        return $this->sanitize_color_with_default($color, '#0B1530');
    }

    public function sanitize_gradient_end_color($color) {
        return $this->sanitize_color_with_default($color, '#122A57');
    }

    public function sync_publish_date_before_save($data, $postarr, $unsanitized_postarr, $update) {
        if (!$update || !isset($data['post_status'], $data['post_type'], $data['post_content'])) {
            return $data;
        }

        if ($data['post_status'] !== 'publish' || $data['post_type'] === 'revision') {
            return $data;
        }

        if (!has_block(self::BLOCK_NAME, $data['post_content'])) {
            return $data;
        }

        if (!$this->is_valid_mysql_datetime($data['post_modified']) || !$this->is_valid_mysql_datetime($data['post_modified_gmt'])) {
            return $data;
        }

        $data['post_date'] = $data['post_modified'];
        $data['post_date_gmt'] = $data['post_modified_gmt'];

        return $data;
    }

    public function ajax_check_date_mismatches() {
        $this->verify_dashboard_ajax_request();

        $items = $this->get_publish_date_mismatches();

        wp_send_json_success(array(
            'items' => $items,
            'count' => count($items),
        ));
    }

    public function ajax_sync_publish_date() {
        $this->verify_dashboard_ajax_request();

        $post_id = isset($_POST['postId']) ? absint(wp_unslash($_POST['postId'])) : 0;

        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => 'Bu yazı için yetkiniz yok.'), 403);
        }

        $result = $this->sync_existing_publish_date($post_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()), 400);
        }

        wp_send_json_success(array(
            'message' => 'Yayın tarihi eşitlendi.',
            'item' => $this->build_date_mismatch_item($result),
        ));
    }

    public function ajax_check_outdated_posts() {
        $this->verify_dashboard_ajax_request();

        $items = $this->get_outdated_posts();

        wp_send_json_success(array(
            'items' => $items,
            'count' => count($items),
        ));
    }

    private function sanitize_color_with_default($color, $default_color) {
        $sanitized = sanitize_hex_color($color);

        if (!$sanitized) {
            return strtoupper($default_color);
        }

        return strtoupper($sanitized);
    }

    private function get_author_name() {
        return $this->sanitize_author_name(get_option(self::OPTION_AUTHOR, 'Av. Arb. M. Fatih Yavaş'));
    }

    private function get_text_color() {
        return $this->sanitize_text_color(get_option(self::OPTION_TEXT_COLOR, '#FFFFFF'));
    }

    private function get_gradient_start_color() {
        return $this->sanitize_gradient_start_color(get_option(self::OPTION_GRADIENT_START, '#0B1530'));
    }

    private function get_gradient_end_color() {
        return $this->sanitize_gradient_end_color(get_option(self::OPTION_GRADIENT_END, '#122A57'));
    }

    private function verify_dashboard_ajax_request() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Bu işlem için yetkiniz yok.'), 403);
        }

        if (!check_ajax_referer(self::DASHBOARD_NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error(array('message' => 'Güvenlik doğrulaması başarısız oldu.'), 403);
        }
    }

    private function get_publish_date_mismatches() {
        $query = new WP_Query(array(
            'post_type' => $this->get_auditable_post_types(),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'modified',
            'order' => 'DESC',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));

        $items = array();

        foreach ($query->posts as $post_id) {
            if (!current_user_can('edit_post', $post_id)) {
                continue;
            }

            $post = get_post($post_id);

            if (!$post instanceof WP_Post || !has_block(self::BLOCK_NAME, $post->post_content)) {
                continue;
            }

            if (!$this->has_publish_modified_date_difference($post)) {
                continue;
            }

            $items[] = $this->build_date_mismatch_item($post);
        }

        usort($items, array($this, 'sort_date_mismatch_items'));

        return $items;
    }

    private function get_outdated_posts() {
        $query = new WP_Query(array(
            'post_type' => $this->get_auditable_post_types(),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'modified',
            'order' => 'ASC',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));

        $items = array();

        foreach ($query->posts as $post_id) {
            if (!current_user_can('edit_post', $post_id)) {
                continue;
            }

            $post = get_post($post_id);

            if (!$post instanceof WP_Post) {
                continue;
            }

            $items[] = $this->build_outdated_item($post);
        }

        return $items;
    }

    private function get_auditable_post_types() {
        $post_types = get_post_types(array(
            'public' => true,
            'show_ui' => true,
        ), 'names');

        return array_values(array_diff($post_types, array('attachment')));
    }

    private function has_publish_modified_date_difference($post) {
        return $this->get_date_difference_type($post) !== '';
    }

    private function get_date_difference_type($post) {
        if (!$post instanceof WP_Post) {
            return '';
        }

        $publish_parts = $this->get_mysql_date_parts($post->post_date);
        $modified_parts = $this->get_mysql_date_parts($post->post_modified);

        if (!$publish_parts || !$modified_parts) {
            return '';
        }

        if ($publish_parts['year'] !== $modified_parts['year']) {
            return 'year';
        }

        if ($publish_parts['month'] !== $modified_parts['month']) {
            return 'month';
        }

        if ($publish_parts['day'] !== $modified_parts['day']) {
            return 'day';
        }

        return '';
    }

    private function get_mysql_date_parts($mysql_date) {
        if (!$this->is_valid_mysql_datetime($mysql_date)) {
            return null;
        }

        return array(
            'year' => substr($mysql_date, 0, 4),
            'month' => substr($mysql_date, 5, 2),
            'day' => substr($mysql_date, 8, 2),
        );
    }

    private function get_date_difference_label($difference_type) {
        $labels = array(
            'year' => 'Yıl farkı',
            'month' => 'Ay farkı',
            'day' => 'Gün farkı',
        );

        return isset($labels[$difference_type]) ? $labels[$difference_type] : '';
    }

    private function get_date_difference_priority($difference_type) {
        $priorities = array(
            'year' => 1,
            'month' => 2,
            'day' => 3,
        );

        return isset($priorities[$difference_type]) ? $priorities[$difference_type] : 99;
    }

    private function sort_date_mismatch_items($left, $right) {
        if ($left['differencePriority'] !== $right['differencePriority']) {
            return $left['differencePriority'] - $right['differencePriority'];
        }

        return strcmp($right['modifiedRaw'], $left['modifiedRaw']);
    }

    private function build_date_mismatch_item($post) {
        $post_type_object = get_post_type_object($post->post_type);
        $title = get_the_title($post);
        $difference_type = $this->get_date_difference_type($post);

        if ($title === '') {
            $title = sprintf('#%d', $post->ID);
        }

        return array(
            'id' => $post->ID,
            'title' => wp_strip_all_tags($title),
            'postType' => $post_type_object && !empty($post_type_object->labels->singular_name) ? $post_type_object->labels->singular_name : $post->post_type,
            'differenceType' => $difference_type,
            'differenceLabel' => $this->get_date_difference_label($difference_type),
            'differencePriority' => $this->get_date_difference_priority($difference_type),
            'publishDate' => $this->format_admin_datetime($post->post_date),
            'modifiedDate' => $this->format_admin_datetime($post->post_modified),
            'modifiedRaw' => $post->post_modified,
            'editUrl' => get_edit_post_link($post->ID, 'raw'),
            'canSync' => current_user_can('edit_post', $post->ID),
            'isSynced' => !$this->has_publish_modified_date_difference($post),
        );
    }

    private function build_outdated_item($post) {
        $post_type_object = get_post_type_object($post->post_type);
        $title = get_the_title($post);

        if ($title === '') {
            $title = sprintf('#%d', $post->ID);
        }

        return array(
            'id' => $post->ID,
            'title' => wp_strip_all_tags($title),
            'postType' => $post_type_object && !empty($post_type_object->labels->singular_name) ? $post_type_object->labels->singular_name : $post->post_type,
            'publishDate' => $this->format_admin_datetime($post->post_date),
            'modifiedDate' => $this->format_admin_datetime($post->post_modified),
            'modifiedRaw' => $post->post_modified,
            'editUrl' => get_edit_post_link($post->ID, 'raw'),
        );
    }

    private function format_admin_datetime($mysql_date) {
        if (!$this->is_valid_mysql_datetime($mysql_date)) {
            return '-';
        }

        return mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $mysql_date);
    }

    private function is_valid_mysql_datetime($mysql_date) {
        return !empty($mysql_date) && $mysql_date !== '0000-00-00 00:00:00';
    }

    private function sync_existing_publish_date($post_id) {
        $post = get_post($post_id);

        if (!$post instanceof WP_Post) {
            return new WP_Error('mh_sg_missing_post', 'Yazı bulunamadı.');
        }

        if ($post->post_status !== 'publish') {
            return new WP_Error('mh_sg_invalid_status', 'Yalnızca yayınlanmış yazı/sayfalar eşitlenebilir.');
        }

        if (!has_block(self::BLOCK_NAME, $post->post_content)) {
            return new WP_Error('mh_sg_missing_block', 'Bu içerikte Son Güncelleme bloğu bulunamadı.');
        }

        if (!$this->is_valid_mysql_datetime($post->post_modified) || !$this->is_valid_mysql_datetime($post->post_modified_gmt)) {
            return new WP_Error('mh_sg_missing_modified_date', 'Son güncelleme tarihi okunamadı.');
        }

        global $wpdb;

        $updated = $wpdb->update(
            $wpdb->posts,
            array(
                'post_date' => $post->post_modified,
                'post_date_gmt' => $post->post_modified_gmt,
            ),
            array('ID' => $post_id),
            array('%s', '%s'),
            array('%d')
        );

        if ($updated === false) {
            return new WP_Error('mh_sg_sync_failed', 'Yayın tarihi eşitlenemedi.');
        }

        clean_post_cache($post_id);

        return get_post($post_id);
    }

    private function get_last_updated_date($block = null) {
        $post_id = 0;

        if ($block instanceof WP_Block && isset($block->context['postId'])) {
            $post_id = absint($block->context['postId']);
        }

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if ($post_id) {
            $modified_date = get_the_modified_date('d.m.Y', $post_id);

            if (!empty($modified_date)) {
                return $modified_date;
            }
        }

        return wp_date('d.m.Y');
    }
}

register_activation_hook(__FILE__, array('Maya_Hukuk_Son_Guncelleme', 'activate'));

new Maya_Hukuk_Son_Guncelleme();
