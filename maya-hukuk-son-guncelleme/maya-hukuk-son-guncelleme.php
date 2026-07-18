<?php
/**
 * Plugin Name:       Maya Hukuk Son Guncelleme Bloku
 * Description:       Gutenberg icin dinamik Son Guncelleme blogu, bio, buton ve bilgi kutusu ayarlari.
 * Version:           1.7.2
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
    const AJAX_OUTDATED_ACTION = 'mh_sg_check_outdated_posts';
    const OPTION_AUTHOR = 'mh_sg_author_name';
    const OPTION_BIO = 'mh_sg_bio_text';
    const OPTION_ACTION_BUTTONS = 'mh_sg_action_buttons';
    const OPTION_INFO_BOXES = 'mh_sg_info_boxes';
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
        add_action('wp_ajax_' . self::AJAX_OUTDATED_ACTION, array($this, 'ajax_check_outdated_posts'));
    }

    public static function activate() {

        if (get_option(self::OPTION_AUTHOR) === false) {
            add_option(self::OPTION_AUTHOR, 'Av. Arb. M. Fatih Yavaş');
        }

        if (get_option(self::OPTION_BIO) === false) {
            add_option(self::OPTION_BIO, self::get_default_bio_text());
        }

        if (get_option(self::OPTION_ACTION_BUTTONS) === false) {
            add_option(self::OPTION_ACTION_BUTTONS, self::get_default_action_buttons());
        }

        if (get_option(self::OPTION_INFO_BOXES) === false) {
            add_option(self::OPTION_INFO_BOXES, self::get_default_info_boxes());
        }

        if (get_option(self::OPTION_TEXT_COLOR) === false) {
            add_option(self::OPTION_TEXT_COLOR, '#FFFFFF');
        }

        if (get_option(self::OPTION_GRADIENT_START) === false) {
            add_option(self::OPTION_GRADIENT_START, '#07111F');
        }

        if (get_option(self::OPTION_GRADIENT_END) === false) {
            add_option(self::OPTION_GRADIENT_END, '#1F3D63');
        }

    }

    public function register_block() {
        $editor_script_handle = 'mh-sg-editor-script';
        $view_script_handle = 'mh-sg-view-script';
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

        wp_register_script(
            $view_script_handle,
            plugins_url('assets/view.js', __FILE__),
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'assets/view.js'),
            true
        );

        wp_localize_script($editor_script_handle, 'mhSgSettings', array(
            'fallbackDate' => wp_date('d.m.Y'),
            'authorName' => $this->get_author_name(),
            'bioText' => $this->get_bio_text(),
            'actionButtons' => $this->get_action_buttons(),
            'infoBoxes' => $this->get_info_boxes(),
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
        $bio_text = $this->get_bio_text();
        $action_buttons = $this->get_action_buttons();
        $info_boxes = $this->get_info_boxes();
        $text_color = $this->get_text_color();
        $gradient_start = $this->get_gradient_start_color();
        $gradient_end = $this->get_gradient_end_color();

        $date_text = $this->get_last_updated_date($block);

        wp_enqueue_script('mh-sg-view-script');

        $styles = sprintf(
            '--mh-sg-text-color: %s; --mh-sg-gradient-start: %s; --mh-sg-gradient-end: %s;',
            esc_attr($text_color),
            esc_attr($gradient_start),
            esc_attr($gradient_end)
        );

        ob_start();
        ?>
        <div class="mh-sg-block" style="<?php echo esc_attr($styles); ?>">
            <div class="mh-sg-content">
                <div class="mh-sg-meta">
                    <p class="mh-sg-date"><?php echo esc_html('Son Güncelleme ' . $date_text); ?></p>
                    <p class="mh-sg-author"><?php echo esc_html($author_name); ?></p>
                </div>

                <?php if ($bio_text !== '') : ?>
                    <p class="mh-sg-bio"><?php echo esc_html($bio_text); ?></p>
                <?php endif; ?>

                <?php if (!empty($info_boxes)) : ?>
                    <div class="mh-sg-info-list" aria-label="Bilgi kutucuklari">
                        <?php foreach ($info_boxes as $info_box) : ?>
                            <div class="mh-sg-info-box">
                                <?php if (!empty($info_box['title'])) : ?>
                                    <span class="mh-sg-info-title"><?php echo esc_html($info_box['title']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($info_box['text'])) : ?>
                                    <span class="mh-sg-info-text"><?php echo esc_html($info_box['text']); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($action_buttons)) : ?>
                    <div class="mh-sg-actions" aria-label="Baglantilar">
                        <?php foreach ($action_buttons as $button) : ?>
                            <a class="mh-sg-action" href="<?php echo esc_url($button['url']); ?>">
                                <span class="mh-sg-action-label"><?php echo esc_html($button['label']); ?></span>
                                <span class="mh-sg-action-icon" aria-hidden="true">&rarr;</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
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

        register_setting(self::SETTINGS_GROUP, self::OPTION_BIO, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_bio_text'),
            'default' => self::get_default_bio_text(),
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_ACTION_BUTTONS, array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_action_buttons'),
            'default' => self::get_default_action_buttons(),
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_INFO_BOXES, array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_info_boxes'),
            'default' => self::get_default_info_boxes(),
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_TEXT_COLOR, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_text_color'),
            'default' => '#FFFFFF',
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_GRADIENT_START, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_gradient_start_color'),
            'default' => '#07111F',
        ));

        register_setting(self::SETTINGS_GROUP, self::OPTION_GRADIENT_END, array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_gradient_end_color'),
            'default' => '#1F3D63',
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
        if ($hook_suffix === 'settings_page_' . self::SETTINGS_PAGE_SLUG && current_user_can('manage_options')) {
            $settings_script_handle = 'mh-sg-settings-script';
            $settings_style_handle = 'mh-sg-settings-style';

            wp_register_script(
                $settings_script_handle,
                plugins_url('assets/settings.js', __FILE__),
                array(),
                filemtime(plugin_dir_path(__FILE__) . 'assets/settings.js'),
                true
            );

            wp_register_style(
                $settings_style_handle,
                plugins_url('assets/settings.css', __FILE__),
                array(),
                filemtime(plugin_dir_path(__FILE__) . 'assets/settings.css')
            );

            wp_enqueue_script($settings_script_handle);
            wp_enqueue_style($settings_style_handle);

            return;
        }

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
            'outdatedAction' => self::AJAX_OUTDATED_ACTION,
            'messages' => array(
                'checking' => 'Kontrol ediliyor...',
                'outdatedEmpty' => 'Güncel olmayan yazı/sayfa bulunamadı.',
                'error' => 'İşlem tamamlanamadı. Lütfen tekrar deneyin.',
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
            <p>Yayın tarihi özgün haliyle korunur. Bu rapor yalnızca son güncelleme tarihi eski olan yazı ve sayfaları bulmanıza yardımcı olur.</p>

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
        $bio_text = $this->get_bio_text();
        $action_buttons = $this->get_action_buttons();
        $info_boxes = $this->get_info_boxes();
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
                        <th scope="row"><label for="<?php echo esc_attr(self::OPTION_BIO); ?>">Bio Alani</label></th>
                        <td>
                            <textarea
                                id="<?php echo esc_attr(self::OPTION_BIO); ?>"
                                name="<?php echo esc_attr(self::OPTION_BIO); ?>"
                                class="large-text mh-sg-bio-field"
                                rows="4"
                            ><?php echo esc_textarea($bio_text); ?></textarea>
                            <p class="description">Yazar, kurum veya sayfa hakkinda kisa bilgi gostermek icin kullanilir. Bos birakirsaniz blokta gorunmez.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Butonlar</th>
                        <td>
                            <div class="mh-sg-settings-repeater" data-mh-sg-repeater data-next-index="<?php echo esc_attr(count($action_buttons)); ?>">
                                <input type="hidden" name="<?php echo esc_attr(self::OPTION_ACTION_BUTTONS); ?>[_mh_empty]" value="1" />
                                <div class="mh-sg-repeater-items" data-mh-sg-items>
                                    <?php foreach ($action_buttons as $index => $button) : ?>
                                        <?php $this->render_action_button_setting_row($index, $button); ?>
                                    <?php endforeach; ?>
                                </div>
                                <template data-mh-sg-template>
                                    <?php $this->render_action_button_setting_row('__INDEX__', array()); ?>
                                </template>
                                <p>
                                    <button type="button" class="button" data-mh-sg-add>Buton ekle</button>
                                </p>
                                <p class="description">Varsayilan olarak Hakkimizda ve Iletisim gelir. Butonlari farkli sayfalara yonlendirebilir, yenisini ekleyebilir veya silebilirsiniz.</p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Bilgi Kutucuklari</th>
                        <td>
                            <div class="mh-sg-settings-repeater" data-mh-sg-repeater data-next-index="<?php echo esc_attr(count($info_boxes)); ?>">
                                <input type="hidden" name="<?php echo esc_attr(self::OPTION_INFO_BOXES); ?>[_mh_empty]" value="1" />
                                <div class="mh-sg-repeater-items" data-mh-sg-items>
                                    <?php foreach ($info_boxes as $index => $info_box) : ?>
                                        <?php $this->render_info_box_setting_row($index, $info_box); ?>
                                    <?php endforeach; ?>
                                </div>
                                <template data-mh-sg-template>
                                    <?php $this->render_info_box_setting_row('__INDEX__', array()); ?>
                                </template>
                                <p>
                                    <button type="button" class="button" data-mh-sg-add>Kutu ekle</button>
                                </p>
                                <p class="description">Varsayilan olarak Baro kutusu gelir. Bu kutulari baska bilgiler icin kullanabilir, cogaltabilir veya tamamen kaldirabilirsiniz.</p>
                            </div>
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

    public function sanitize_bio_text($value) {
        if (!is_scalar($value)) {
            return '';
        }

        return sanitize_textarea_field($value);
    }

    public function sanitize_action_buttons($buttons) {
        if (!is_array($buttons)) {
            return array();
        }

        $sanitized_buttons = array();

        foreach ($buttons as $button) {
            if (!is_array($button)) {
                continue;
            }

            $label = isset($button['label']) && is_scalar($button['label']) ? sanitize_text_field($button['label']) : '';
            $url = isset($button['url']) && is_scalar($button['url']) ? esc_url_raw(trim((string) $button['url'])) : '';

            if ($label === '' || $url === '') {
                continue;
            }

            $sanitized_buttons[] = array(
                'label' => $label,
                'url' => $url,
            );
        }

        return $sanitized_buttons;
    }

    public function sanitize_info_boxes($info_boxes) {
        if (!is_array($info_boxes)) {
            return array();
        }

        $sanitized_boxes = array();

        foreach ($info_boxes as $info_box) {
            if (!is_array($info_box)) {
                continue;
            }

            $title = isset($info_box['title']) && is_scalar($info_box['title']) ? sanitize_text_field($info_box['title']) : '';
            $text = isset($info_box['text']) && is_scalar($info_box['text']) ? sanitize_text_field($info_box['text']) : '';

            if ($title === '' && $text === '') {
                continue;
            }

            $sanitized_boxes[] = array(
                'title' => $title,
                'text' => $text,
            );
        }

        return $sanitized_boxes;
    }

    public function sanitize_text_color($color) {
        return $this->sanitize_color_with_default($color, '#FFFFFF');
    }

    public function sanitize_gradient_start_color($color) {
        return $this->sanitize_color_with_default($color, '#07111F');
    }

    public function sanitize_gradient_end_color($color) {
        return $this->sanitize_color_with_default($color, '#1F3D63');
    }

    private function render_action_button_setting_row($index, $button) {
        $label = isset($button['label']) ? $button['label'] : '';
        $url = isset($button['url']) ? $button['url'] : '';
        ?>
        <div class="mh-sg-repeater-row" data-mh-sg-row>
            <label>
                <span>Buton metni</span>
                <input
                    type="text"
                    name="<?php echo esc_attr(self::OPTION_ACTION_BUTTONS . '[' . $index . '][label]'); ?>"
                    value="<?php echo esc_attr($label); ?>"
                    class="regular-text"
                    placeholder="Hakkımızda"
                />
            </label>
            <label>
                <span>Bağlantı</span>
                <input
                    type="text"
                    name="<?php echo esc_attr(self::OPTION_ACTION_BUTTONS . '[' . $index . '][url]'); ?>"
                    value="<?php echo esc_attr($url); ?>"
                    class="regular-text"
                    placeholder="/hakkimizda/"
                />
            </label>
            <button type="button" class="button button-link-delete mh-sg-remove-row" data-mh-sg-remove>Sil</button>
        </div>
        <?php
    }

    private function render_info_box_setting_row($index, $info_box) {
        $title = isset($info_box['title']) ? $info_box['title'] : '';
        $text = isset($info_box['text']) ? $info_box['text'] : '';
        ?>
        <div class="mh-sg-repeater-row" data-mh-sg-row>
            <label>
                <span>Kutu başlığı</span>
                <input
                    type="text"
                    name="<?php echo esc_attr(self::OPTION_INFO_BOXES . '[' . $index . '][title]'); ?>"
                    value="<?php echo esc_attr($title); ?>"
                    class="regular-text"
                    placeholder="Baro"
                />
            </label>
            <label>
                <span>Kutu metni</span>
                <input
                    type="text"
                    name="<?php echo esc_attr(self::OPTION_INFO_BOXES . '[' . $index . '][text]'); ?>"
                    value="<?php echo esc_attr($text); ?>"
                    class="regular-text"
                    placeholder="Bağlı olunan baro"
                />
            </label>
            <button type="button" class="button button-link-delete mh-sg-remove-row" data-mh-sg-remove>Sil</button>
        </div>
        <?php
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

    private static function get_default_bio_text() {
        return 'Av. Arb. M. Fatih Yavaş tarafından güncel mevzuat ve uygulama notlarıyla hazırlanan kısa bilgilendirme alanı.';
    }

    private static function get_default_action_buttons() {
        return array(
            array(
                'label' => 'Hakkımızda',
                'url' => '/hakkimizda/',
            ),
            array(
                'label' => 'İletişim',
                'url' => '/iletisim/',
            ),
        );
    }

    private static function get_default_info_boxes() {
        return array(
            array(
                'title' => 'Baro',
                'text' => 'Bağlı olunan baro',
            ),
        );
    }

    private function get_author_name() {
        return $this->sanitize_author_name(get_option(self::OPTION_AUTHOR, 'Av. Arb. M. Fatih Yavaş'));
    }

    private function get_bio_text() {
        $bio_text = get_option(self::OPTION_BIO, false);

        if ($bio_text === false) {
            $bio_text = self::get_default_bio_text();
        }

        return $this->sanitize_bio_text($bio_text);
    }

    private function get_action_buttons() {
        $action_buttons = get_option(self::OPTION_ACTION_BUTTONS, false);

        if ($action_buttons === false) {
            $action_buttons = self::get_default_action_buttons();
        }

        return $this->sanitize_action_buttons($action_buttons);
    }

    private function get_info_boxes() {
        $info_boxes = get_option(self::OPTION_INFO_BOXES, false);

        if ($info_boxes === false) {
            $info_boxes = self::get_default_info_boxes();
        }

        return $this->sanitize_info_boxes($info_boxes);
    }

    private function get_text_color() {
        return $this->sanitize_text_color(get_option(self::OPTION_TEXT_COLOR, '#FFFFFF'));
    }

    private function get_gradient_start_color() {
        return $this->sanitize_gradient_start_color(get_option(self::OPTION_GRADIENT_START, '#07111F'));
    }

    private function get_gradient_end_color() {
        return $this->sanitize_gradient_end_color(get_option(self::OPTION_GRADIENT_END, '#1F3D63'));
    }

    private function verify_dashboard_ajax_request() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Bu işlem için yetkiniz yok.'), 403);
        }

        if (!check_ajax_referer(self::DASHBOARD_NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error(array('message' => 'Güvenlik doğrulaması başarısız oldu.'), 403);
        }
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
