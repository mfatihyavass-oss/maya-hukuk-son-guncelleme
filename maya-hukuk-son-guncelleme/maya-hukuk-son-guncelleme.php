<?php
/**
 * Plugin Name:       Maya Hukuk Son Guncelleme Bloku
 * Description:       Gutenberg icin dinamik Son Guncelleme blogu ve global ayarlar.
 * Version:           1.0.0
 * Author:            Maya Hukuk
 * Text Domain:       maya-hukuk-son-guncelleme
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Maya_Hukuk_Son_Guncelleme {
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
            array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n'),
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
            'todayDate' => wp_date('d.m.Y'),
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

        $date_text = wp_date('d.m.Y');

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
}

register_activation_hook(__FILE__, array('Maya_Hukuk_Son_Guncelleme', 'activate'));

new Maya_Hukuk_Son_Guncelleme();
