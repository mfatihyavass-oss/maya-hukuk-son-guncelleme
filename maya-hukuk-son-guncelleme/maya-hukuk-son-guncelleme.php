<?php
/**
 * Plugin Name:       Maya Hukuk Son Güncelleme Bloku
 * Description:       Gutenberg için dinamik yazar ve son güncelleme kartı: yazar fotoğrafı, unvan, bio, tarih rozetleri, okuma süresi, butonlar, bilgi kutuları, paylaşım, yasal uyarı, otomatik ekleme ve güncellik raporları.
 * Version:           2.3.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Maya Hukuk
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       maya-hukuk-son-guncelleme
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Maya_Hukuk_Son_Guncelleme {
    const VERSION = '2.3.0';
    const BLOCK_NAME = 'maya-hukuk/son-guncelleme';
    const SHORTCODE = 'maya_son_guncelleme';
    const DASHBOARD_NONCE_ACTION = 'mh_sg_dashboard_date_audit';
    const PREVIEW_NONCE_ACTION = 'mh_sg_settings_preview';
    const AJAX_OUTDATED_ACTION = 'mh_sg_check_outdated_posts';
    const AJAX_PREVIEW_ACTION = 'mh_sg_settings_preview';

    const OPTION_AUTHOR = 'mh_sg_author_name';
    const OPTION_AUTHOR_TITLE = 'mh_sg_author_title';
    const OPTION_AUTHOR_URL = 'mh_sg_author_url';
    const OPTION_AVATAR_ID = 'mh_sg_avatar_id';
    const OPTION_BIO = 'mh_sg_bio_text';
    const OPTION_DISCLAIMER = 'mh_sg_disclaimer';
    const OPTION_ACTION_BUTTONS = 'mh_sg_action_buttons';
    const OPTION_INFO_BOXES = 'mh_sg_info_boxes';
    const OPTION_THEME = 'mh_sg_theme';
    const OPTION_DATE_STYLE = 'mh_sg_date_style';
    const OPTION_WIDTH = 'mh_sg_width';
    const OPTION_FEATURES = 'mh_sg_features';
    const OPTION_TEXT_COLOR = 'mh_sg_text_color';
    const OPTION_GRADIENT_START = 'mh_sg_gradient_start';
    const OPTION_GRADIENT_END = 'mh_sg_gradient_end';
    const OPTION_ACCENT_COLOR = 'mh_sg_accent_color';
    const OPTION_BUTTON_TEXT_COLOR = 'mh_sg_button_text_color';
    const OPTION_BUTTON2_TEXT_COLOR = 'mh_sg_button2_text_color';
    const OPTION_AUTO_INSERT = 'mh_sg_auto_insert';
    const OPTION_STALE_MONTHS = 'mh_sg_stale_months';

    const SETTINGS_GROUP = 'mh_sg_settings_group';
    const SETTINGS_PAGE_SLUG = 'mh-son-guncelleme-bloku';

    const DEFAULT_AUTHOR = 'Av. Arb. M. Fatih Yavaş';
    const DEFAULT_AUTHOR_TITLE = 'Avukat · Arabulucu';
    const DEFAULT_TEXT_COLOR = '#FFFFFF';
    const DEFAULT_GRADIENT_START = '#07111F';
    const DEFAULT_GRADIENT_END = '#1F3D63';
    const DEFAULT_ACCENT_COLOR = '#5B3A91';
    const DEFAULT_THEME = 'lila';
    const DEFAULT_WIDTH = 'wide';

    /**
     * Optional colors that work in every theme. Empty value = theme color.
     * key => array(option name, label, color shown in the picker by default).
     */
    const CUSTOM_COLORS = array(
        'card-bg' => array('mh_sg_card_bg_color', 'Kart zemini', '#FCFAFF'),
        'card-border' => array('mh_sg_card_border_color', 'Kart çerçevesi', '#E8DFF2'),
        'name' => array('mh_sg_name_color', 'Yazar adı', '#3F286E'),
        'body' => array('mh_sg_body_color', 'Bio ve yasal uyarı metni', '#655D70'),
        'badge-bg' => array('mh_sg_badge_bg_color', 'Tarih rozeti zemini', '#ECE2F4'),
        'badge-text' => array('mh_sg_badge_text_color', 'Tarih rozeti yazısı', '#5B3A91'),
        'box-bg' => array('mh_sg_box_bg_color', 'Kutu zemini', '#FFFFFF'),
        'box-border' => array('mh_sg_box_border_color', 'Kutu çerçevesi', '#E8DFF2'),
        'box-stripe' => array('mh_sg_box_stripe_color', 'Kutu sol şeridi', '#B69BCE'),
        'box-title' => array('mh_sg_box_title_color', 'Kutu başlığı', '#795B9D'),
        'box-text' => array('mh_sg_box_text_color', 'Kutu metni', '#3F286E'),
        'button-bg' => array('mh_sg_button_bg_color', 'İlk buton zemini', '#5B3A91'),
        'button2-bg' => array('mh_sg_button2_bg_color', 'Diğer butonların zemini', '#F5EFFA'),
    );

    const WIDTHS = array(
        'wide' => 'Geniş — sitedeki bölümlerle aynı (en fazla 1200 px)',
        'content' => 'Yazı metni kadar (temanın içerik genişliği)',
        'full' => 'Kapsayıcının tamamı',
    );
    const DEFAULT_STALE_MONTHS = 12;
    const REPORT_LIMIT = 100;
    const WORDS_PER_MINUTE = 200;

    const THEMES = array(
        'lila' => 'Lila (site teması)',
        'lacivert' => 'Lacivert & Altın (koyu)',
        'acik' => 'Açık kart',
        'minimal' => 'Minimal çizgi',
    );

    const FEATURES = array(
        'publish_date' => 'Yayın tarihini göster (güncelleme tarihinden farklıysa)',
        'reading_time' => 'Okuma süresini göster',
        'share' => 'Paylaşım ve bağlantı kopyalama düğmelerini göster',
        'avatar' => 'Yazar fotoğrafını veya baş harf rozetini göster',
    );

    const DEFAULT_FEATURES = array('publish_date', 'share', 'avatar');

    const SECTIONS = array(
        'dates' => 'Tarih rozetleri',
        'bio' => 'Bio',
        'info' => 'Bilgi kutuları',
        'buttons' => 'Butonlar',
        'share' => 'Paylaşım',
        'disclaimer' => 'Yasal uyarı',
    );

    const TURKISH_MONTHS = array(
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 5 => 'Mayıs', 6 => 'Haziran',
        7 => 'Temmuz', 8 => 'Ağustos', 9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
    );

    /** @var bool */
    private $cache_purged = false;

    /** @var int[]|null */
    private $pattern_ids = null;

    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('init', array($this, 'register_shortcode'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'register_admin_columns'));

        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));
        add_action('wp_ajax_' . self::AJAX_OUTDATED_ACTION, array($this, 'ajax_check_outdated_posts'));
        add_action('wp_ajax_' . self::AJAX_PREVIEW_ACTION, array($this, 'ajax_settings_preview'));

        add_filter('the_content', array($this, 'maybe_auto_insert'), 20);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_plugin_action_links'));

        foreach (self::get_option_names() as $option_name) {
            add_action('update_option_' . $option_name, array($this, 'purge_page_caches'));
            add_action('add_option_' . $option_name, array($this, 'purge_page_caches'));
        }
    }

    public static function activate() {
        if (get_option(self::OPTION_AUTHOR) === false) {
            add_option(self::OPTION_AUTHOR, self::DEFAULT_AUTHOR);
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
            add_option(self::OPTION_TEXT_COLOR, self::DEFAULT_TEXT_COLOR);
        }

        if (get_option(self::OPTION_GRADIENT_START) === false) {
            add_option(self::OPTION_GRADIENT_START, self::DEFAULT_GRADIENT_START);
        }

        if (get_option(self::OPTION_GRADIENT_END) === false) {
            add_option(self::OPTION_GRADIENT_END, self::DEFAULT_GRADIENT_END);
        }
    }

    public static function get_option_names() {
        $custom_color_options = array();

        foreach (self::CUSTOM_COLORS as $color) {
            $custom_color_options[] = $color[0];
        }

        return array_merge($custom_color_options, array(
            self::OPTION_AUTHOR,
            self::OPTION_AUTHOR_TITLE,
            self::OPTION_AUTHOR_URL,
            self::OPTION_AVATAR_ID,
            self::OPTION_BIO,
            self::OPTION_DISCLAIMER,
            self::OPTION_ACTION_BUTTONS,
            self::OPTION_INFO_BOXES,
            self::OPTION_THEME,
            self::OPTION_DATE_STYLE,
            self::OPTION_WIDTH,
            self::OPTION_FEATURES,
            self::OPTION_TEXT_COLOR,
            self::OPTION_GRADIENT_START,
            self::OPTION_GRADIENT_END,
            self::OPTION_ACCENT_COLOR,
            self::OPTION_BUTTON_TEXT_COLOR,
            self::OPTION_BUTTON2_TEXT_COLOR,
            self::OPTION_AUTO_INSERT,
            self::OPTION_STALE_MONTHS,
        ));
    }

    /* ------------------------------------------------------------------
     * Block, shortcode and automatic insertion
     * ------------------------------------------------------------------ */

    public function register_block() {
        $editor_script_handle = 'mh-sg-editor-script';
        $view_script_handle = 'mh-sg-view-script';
        $style_handle = 'mh-sg-style';

        wp_register_script(
            $editor_script_handle,
            plugins_url('assets/editor.js', __FILE__),
            array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-server-side-render'),
            $this->asset_version('assets/editor.js'),
            true
        );

        wp_register_style(
            $style_handle,
            plugins_url('assets/style.css', __FILE__),
            array(),
            $this->asset_version('assets/style.css')
        );

        wp_register_script(
            $view_script_handle,
            plugins_url('assets/view.js', __FILE__),
            array(),
            $this->asset_version('assets/view.js'),
            array(
                'in_footer' => true,
                'strategy' => 'defer',
            )
        );

        wp_localize_script($editor_script_handle, 'mhSgEditor', array(
            'settingsUrl' => current_user_can('manage_options') ? admin_url('options-general.php?page=' . self::SETTINGS_PAGE_SLUG) : '',
            'themes' => self::THEMES,
            'sections' => self::SECTIONS,
            'globalTheme' => $this->get_settings()['theme'],
            'globalWidth' => $this->get_settings()['width'],
        ));

        register_block_type(__DIR__ . '/block.json', array(
            'editor_script' => $editor_script_handle,
            'style' => $style_handle,
            'editor_style' => $style_handle,
            'render_callback' => array($this, 'render_block'),
        ));
    }

    public function register_shortcode() {
        add_shortcode(self::SHORTCODE, array($this, 'render_shortcode'));
    }

    public function render_shortcode($atts = array()) {
        $atts = shortcode_atts(array(
            'tema' => '',
            'gizle' => '',
        ), $atts, self::SHORTCODE);

        $hidden = array_filter(array_map('trim', explode(',', (string) $atts['gizle'])));

        return render_block(array(
            'blockName' => self::BLOCK_NAME,
            'attrs' => array(
                'theme' => (string) $atts['tema'],
                'hiddenSections' => array_values($hidden),
            ),
            'innerBlocks' => array(),
            'innerHTML' => '',
            'innerContent' => array(),
        ));
    }

    /**
     * Appends the card to singular posts of the selected types when the post
     * content does not already contain it.
     */
    public function maybe_auto_insert($content) {
        $post_types = $this->get_settings()['auto_insert'];

        if (empty($post_types) || is_admin() || is_feed() || wp_is_json_request()) {
            return $content;
        }

        if (doing_filter('get_the_excerpt') || !in_the_loop() || !is_main_query() || !is_singular($post_types)) {
            return $content;
        }

        $post = get_post();

        if (!$post instanceof WP_Post || (int) $post->ID !== (int) get_queried_object_id()) {
            return $content;
        }

        if ($this->post_has_card($post) || strpos($content, 'mh-sg-block') !== false) {
            return $content;
        }

        return $content . render_block(array(
            'blockName' => self::BLOCK_NAME,
            'attrs' => array(),
            'innerBlocks' => array(),
            'innerHTML' => '',
            'innerContent' => array(),
        ));
    }

    public function render_block($attributes = array(), $content = '', $block = null) {
        $attributes = is_array($attributes) ? $attributes : array();
        $post_id = 0;

        if ($block instanceof WP_Block && isset($block->context['postId'])) {
            $post_id = absint($block->context['postId']);
        }

        if (!$post_id) {
            $post_id = (int) get_the_ID();
        }

        $settings = $this->get_settings();
        $context = $this->build_post_context($post_id ? get_post($post_id) : null);
        $options = $this->normalize_block_options($attributes);

        $extra_class = isset($attributes['className']) && is_string($attributes['className']) ? $attributes['className'] : '';
        $options['use_wrapper'] = $block instanceof WP_Block && function_exists('get_block_wrapper_attributes');
        $options['extra_class'] = $options['use_wrapper'] ? '' : $extra_class;

        wp_enqueue_script('mh-sg-view-script');

        return $this->render_card($settings, $context, $options);
    }

    /* ------------------------------------------------------------------
     * Card rendering
     * ------------------------------------------------------------------ */

    private function render_card(array $s, array $ctx, array $options) {
        $theme = $options['theme'] !== '' ? $options['theme'] : $s['theme'];
        $hidden = $options['hidden'];
        $features = $s['features'];

        $show = array(
            'dates' => !in_array('dates', $hidden, true) && !empty($ctx['modified']),
            'bio' => !in_array('bio', $hidden, true) && $s['bio'] !== '',
            'info' => !in_array('info', $hidden, true) && !empty($s['info_boxes']),
            'buttons' => !in_array('buttons', $hidden, true) && !empty($s['action_buttons']),
            'share' => !in_array('share', $hidden, true) && in_array('share', $features, true) && $ctx['permalink'] !== '',
            'disclaimer' => !in_array('disclaimer', $hidden, true) && $s['disclaimer'] !== '',
            'avatar' => in_array('avatar', $features, true),
        );

        $classes = array('mh-sg-block', 'mh-sg-theme-' . $theme);

        // Blokta "geniş/tam genişlik" hizalaması seçildiyse genel genişlik ayarı devreye girmez.
        if ($options['align'] === '') {
            $classes[] = 'mh-sg-width-' . $s['width'];
        }

        if ($show['avatar']) {
            $classes[] = 'has-avatar';
        }

        if ($options['extra_class'] !== '') {
            $classes[] = $options['extra_class'];
        }

        $styles = sprintf(
            '--mh-sg-c-text:%s;--mh-sg-c-start:%s;--mh-sg-c-end:%s;--mh-sg-c-accent:%s;',
            $s['text_color'],
            $s['gradient_start'],
            $s['gradient_end'],
            $s['accent_color']
        );

        // Buton yazı renkleri boşsa tema kendi rengini kullanır.
        if ($s['button_text_color'] !== '') {
            $classes[] = 'has-button-text-color';
            $styles .= '--mh-sg-c-button-text:' . $s['button_text_color'] . ';';
        }

        if ($s['button2_text_color'] !== '') {
            $classes[] = 'has-button2-text-color';
            $styles .= '--mh-sg-c-button2-text:' . $s['button2_text_color'] . ';';
        }

        foreach ($s['custom_colors'] as $color_key => $color_value) {
            if ($color_value !== '') {
                $classes[] = 'has-c-' . $color_key;
                $styles .= '--mh-sg-c-' . $color_key . ':' . $color_value . ';';
            }
        }

        if ($options['use_wrapper']) {
            $wrapper_attributes = get_block_wrapper_attributes(array(
                'class' => implode(' ', $classes),
                'style' => $styles,
            ));
        } else {
            $wrapper_attributes = sprintf('class="%s" style="%s"', esc_attr(implode(' ', $classes)), esc_attr($styles));
        }

        $author_tag = $s['author_url'] !== '' ? 'a' : 'span';

        ob_start();
        ?>
        <aside <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above or by core. ?> aria-label="<?php echo esc_attr('Yazar ve güncelleme bilgisi'); ?>">
            <span class="mh-sg-watermark" aria-hidden="true"><?php echo self::icon('scale'); // phpcs:ignore ?></span>
            <div class="mh-sg-content">
                <?php if ($show['dates']) : ?>
                    <ul class="mh-sg-badges" role="list">
                        <li class="mh-sg-badge is-updated">
                            <?php echo self::icon('refresh'); // phpcs:ignore ?>
                            <span class="mh-sg-date">Son Güncelleme <time datetime="<?php echo esc_attr($ctx['modified']->format('c')); ?>"><?php echo esc_html(self::format_date($ctx['modified'], $s['date_style'])); ?></time></span>
                        </li>
                        <?php if (in_array('publish_date', $features, true) && $ctx['published'] && $ctx['published']->format('Y-m-d') !== $ctx['modified']->format('Y-m-d')) : ?>
                            <li class="mh-sg-badge">
                                <?php echo self::icon('calendar'); // phpcs:ignore ?>
                                <span>Yayın <time datetime="<?php echo esc_attr($ctx['published']->format('c')); ?>"><?php echo esc_html(self::format_date($ctx['published'], $s['date_style'])); ?></time></span>
                            </li>
                        <?php endif; ?>
                        <?php if (in_array('reading_time', $features, true) && $ctx['reading_minutes'] > 0) : ?>
                            <li class="mh-sg-badge">
                                <?php echo self::icon('clock'); // phpcs:ignore ?>
                                <span><?php echo esc_html($ctx['reading_minutes'] . ' dk okuma'); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>

                <div class="mh-sg-profile">
                    <?php if ($show['avatar']) : ?>
                        <div class="mh-sg-avatar">
                            <?php
                            $avatar_html = $s['avatar_id'] ? wp_get_attachment_image($s['avatar_id'], 'thumbnail', false, array(
                                'class' => 'mh-sg-avatar-image',
                                'alt' => $s['author'],
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            )) : '';

                            if ($avatar_html) {
                                echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput -- core output.
                            } else {
                                printf('<span class="mh-sg-initials" aria-hidden="true">%s</span>', esc_html(self::get_initials($s['author'])));
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="mh-sg-meta">
                        <span class="mh-sg-eyebrow">Yazar</span>
                        <<?php echo $author_tag; ?> class="mh-sg-author"<?php echo $author_tag === 'a' ? ' href="' . esc_url($s['author_url']) . '" rel="author"' : ''; ?>><?php echo esc_html($s['author']); ?></<?php echo $author_tag; ?>>
                        <?php if ($s['author_title'] !== '') : ?>
                            <span class="mh-sg-role"><?php echo esc_html($s['author_title']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($show['bio']) : ?>
                    <p class="mh-sg-bio"><?php echo esc_html($s['bio']); ?></p>
                <?php endif; ?>

                <?php if ($show['info']) : ?>
                    <dl class="mh-sg-info-list">
                        <?php foreach ($s['info_boxes'] as $info_box) : ?>
                            <div class="mh-sg-info-box">
                                <?php if ($info_box['title'] !== '') : ?>
                                    <dt class="mh-sg-info-title"><?php echo esc_html($info_box['title']); ?></dt>
                                <?php endif; ?>
                                <?php if ($info_box['text'] !== '') : ?>
                                    <dd class="mh-sg-info-text"><?php echo esc_html($info_box['text']); ?></dd>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>

                <?php if ($show['buttons'] || $show['share']) : ?>
                    <div class="mh-sg-footer">
                        <?php if ($show['buttons']) : ?>
                            <div class="mh-sg-actions" role="group" aria-label="Bağlantılar">
                                <?php foreach ($s['action_buttons'] as $index => $button) : ?>
                                    <?php
                                    $icon = self::detect_button_icon($button['url']);
                                    $button_classes = 'mh-sg-action ' . ($index === 0 ? 'is-primary' : 'is-secondary') . ' has-icon-' . $icon;
                                    ?>
                                    <a class="<?php echo esc_attr($button_classes); ?>" href="<?php echo esc_url($button['url']); ?>"<?php echo $button['new_tab'] ? ' target="_blank" rel="noopener"' : ''; ?>>
                                        <?php if ($icon !== 'arrow') : ?>
                                            <span class="mh-sg-action-lead" aria-hidden="true"><?php echo self::icon($icon); // phpcs:ignore ?></span>
                                        <?php endif; ?>
                                        <span class="mh-sg-action-label"><?php echo esc_html($button['label']); ?></span>
                                        <span class="mh-sg-action-icon" aria-hidden="true"><?php echo self::icon($button['new_tab'] ? 'external' : 'arrow'); // phpcs:ignore ?></span>
                                        <?php if ($button['new_tab']) : ?>
                                            <span class="screen-reader-text mh-sg-sr">(yeni sekmede açılır)</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show['share']) : ?>
                            <div class="mh-sg-share" role="group" aria-label="Bu yazıyı paylaş">
                                <span class="mh-sg-share-label">Paylaş</span>
                                <?php foreach (self::get_share_links($ctx['permalink'], $ctx['title']) as $network => $link) : ?>
                                    <a class="mh-sg-share-link is-<?php echo esc_attr($network); ?>" href="<?php echo esc_url($link['url']); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr($link['label']); ?>" title="<?php echo esc_attr($link['label']); ?>">
                                        <?php echo self::icon($network); // phpcs:ignore ?>
                                    </a>
                                <?php endforeach; ?>
                                <button type="button" class="mh-sg-share-link is-copy" data-mh-sg-copy="<?php echo esc_url($ctx['permalink']); ?>" aria-label="Bağlantıyı kopyala" title="Bağlantıyı kopyala">
                                    <?php echo self::icon('link'); // phpcs:ignore ?>
                                </button>
                                <span class="mh-sg-copy-status" role="status" aria-live="polite"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($show['disclaimer']) : ?>
                    <p class="mh-sg-disclaimer">
                        <?php echo self::icon('info'); // phpcs:ignore ?>
                        <span><?php echo esc_html($s['disclaimer']); ?></span>
                    </p>
                <?php endif; ?>
            </div>
        </aside>
        <?php
        return trim(ob_get_clean());
    }

    private function build_post_context($post) {
        $context = array(
            'modified' => null,
            'published' => null,
            'reading_minutes' => 0,
            'permalink' => '',
            'title' => '',
        );

        if (!$post instanceof WP_Post) {
            return $context;
        }

        $modified = get_post_datetime($post, 'modified');
        $published = get_post_datetime($post, 'date');

        $context['modified'] = $modified ? $modified : null;
        $context['published'] = $published ? $published : null;
        $context['reading_minutes'] = self::estimate_reading_minutes(wp_strip_all_tags(strip_shortcodes($post->post_content)));

        if ($post->post_status === 'publish') {
            $context['permalink'] = (string) get_permalink($post);
        }

        $context['title'] = wp_strip_all_tags(get_the_title($post));

        return $context;
    }

    private function normalize_block_options(array $attributes) {
        $theme = isset($attributes['theme']) && is_string($attributes['theme']) ? $attributes['theme'] : '';
        $hidden = isset($attributes['hiddenSections']) && is_array($attributes['hiddenSections']) ? $attributes['hiddenSections'] : array();

        return array(
            'theme' => isset(self::THEMES[$theme]) ? $theme : '',
            'hidden' => array_values(array_intersect(array_map('strval', $hidden), array_keys(self::SECTIONS))),
            'align' => isset($attributes['align']) && in_array($attributes['align'], array('wide', 'full'), true) ? $attributes['align'] : '',
            'use_wrapper' => false,
            'extra_class' => '',
        );
    }

    /* ------------------------------------------------------------------
     * Pure helpers (covered by tests/test-helpers.php)
     * ------------------------------------------------------------------ */

    public static function format_date(DateTimeInterface $date, $style = 'long') {
        if ($style === 'numeric') {
            return $date->format('d.m.Y');
        }

        return $date->format('j') . ' ' . self::TURKISH_MONTHS[(int) $date->format('n')] . ' ' . $date->format('Y');
    }

    public static function get_initials($name) {
        $words = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_values(array_filter($words, function ($word) {
            // "Av.", "Arb.", "Dr.", "M." gibi unvan ve kisaltmalari atla.
            return substr($word, -1) !== '.';
        }));

        if (empty($words)) {
            return 'MH';
        }

        $first = $words[0];
        $last = count($words) > 1 ? $words[count($words) - 1] : '';
        $initials = mb_substr($first, 0, 1, 'UTF-8') . ($last !== '' ? mb_substr($last, 0, 1, 'UTF-8') : '');

        return mb_strtoupper(strtr($initials, array('i' => 'İ', 'ı' => 'I')), 'UTF-8');
    }

    public static function detect_button_icon($url) {
        $url = strtolower(trim((string) $url));

        if (strpos($url, 'tel:') === 0) {
            return 'phone';
        }

        if (strpos($url, 'mailto:') === 0) {
            return 'mail';
        }

        if (preg_match('#^(https?://)?(wa\.me|api\.whatsapp\.com|(www\.)?whatsapp\.com)/#', $url) || strpos($url, 'whatsapp:') === 0) {
            return 'whatsapp';
        }

        if (preg_match('#^(https?://)?(maps\.app\.goo\.gl|(www\.)?google\.[a-z.]+/maps|goo\.gl/maps)#', $url)) {
            return 'pin';
        }

        return 'arrow';
    }

    public static function estimate_reading_minutes($plain_text) {
        $words = preg_split('/\s+/u', trim((string) $plain_text), -1, PREG_SPLIT_NO_EMPTY);
        $count = is_array($words) ? count($words) : 0;

        if ($count === 0) {
            return 0;
        }

        return max(1, (int) ceil($count / self::WORDS_PER_MINUTE));
    }

    public static function format_age($seconds) {
        $days = (int) floor(max(0, $seconds) / DAY_IN_SECONDS);

        if ($days < 1) {
            return 'bugün';
        }

        if ($days < 31) {
            return $days . ' gün önce';
        }

        $months = (int) floor($days / 30.4375);

        if ($months < 12) {
            return $months . ' ay önce';
        }

        $years = (int) floor($months / 12);
        $rest = $months % 12;

        return $years . ' yıl' . ($rest ? ' ' . $rest . ' ay' : '') . ' önce';
    }

    public static function get_share_links($url, $title) {
        $encoded_url = rawurlencode($url);
        $encoded_text = rawurlencode(trim($title . ' ' . $url));

        return array(
            'whatsapp' => array(
                'label' => 'WhatsApp ile paylaş',
                'url' => 'https://wa.me/?text=' . $encoded_text,
            ),
            'linkedin' => array(
                'label' => 'LinkedIn\'de paylaş',
                'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
            ),
            'x' => array(
                'label' => 'X\'te paylaş',
                'url' => 'https://x.com/intent/post?url=' . $encoded_url . '&text=' . rawurlencode($title),
            ),
            'facebook' => array(
                'label' => 'Facebook\'ta paylaş',
                'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
            ),
        );
    }

    public static function icon($name) {
        $paths = array(
            'refresh' => '<path d="M20 11a8 8 0 0 0-14.9-4M4 4v4h4"/><path d="M4 13a8 8 0 0 0 14.9 4M20 20v-4h-4"/>',
            'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
            'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
            'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'external' => '<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
            'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1"/>',
            'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
            'pin' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'whatsapp' => '<path d="M4 20l1.3-4.2A8.5 8.5 0 1 1 8.4 19z"/><path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.6-2-1-1 .8a5 5 0 0 1-2.7-2.7l.8-1-1-2z"/>',
            'linkedin' => '<rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/><path d="M8 10.5V16M8 7.8v.1M11.5 16v-5.5M11.5 13c0-1.6 1-2.6 2.3-2.6s2.2.9 2.2 2.6V16"/>',
            'x' => '<path d="M4.5 4h4l11 16h-4zM19.5 4l-6.2 7M4.5 20l6.2-7"/>',
            'facebook' => '<path d="M14.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4a20 20 0 0 0-2.4-.1c-2.3 0-3.9 1.4-3.9 4v2.2H8.7v3h2.6V21"/>',
            'link' => '<path d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1 1"/><path d="M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1-1"/>',
            'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
            'info' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5M12 8v.1"/>',
            'scale' => '<path d="M12 3v18M7 21h10M5 6h14M12 4.5 5 6M12 4.5 19 6"/><path d="M5 6 2.5 12a2.5 2.5 0 0 0 5 0zM19 6l-2.5 6a2.5 2.5 0 0 0 5 0z"/>',
        );

        if (!isset($paths[$name])) {
            return '';
        }

        return '<svg class="mh-sg-icon mh-sg-icon-' . $name . '" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
    }

    /* ------------------------------------------------------------------
     * Settings
     * ------------------------------------------------------------------ */

    public function register_settings() {
        $settings = array(
            self::OPTION_AUTHOR => array('string', 'sanitize_author_name', self::DEFAULT_AUTHOR),
            self::OPTION_AUTHOR_TITLE => array('string', 'sanitize_plain_text', self::DEFAULT_AUTHOR_TITLE),
            self::OPTION_AUTHOR_URL => array('string', 'sanitize_url_value', ''),
            self::OPTION_AVATAR_ID => array('integer', 'sanitize_avatar_id', 0),
            self::OPTION_BIO => array('string', 'sanitize_bio_text', self::get_default_bio_text()),
            self::OPTION_DISCLAIMER => array('string', 'sanitize_bio_text', self::get_default_disclaimer()),
            self::OPTION_ACTION_BUTTONS => array('array', 'sanitize_action_buttons', self::get_default_action_buttons()),
            self::OPTION_INFO_BOXES => array('array', 'sanitize_info_boxes', self::get_default_info_boxes()),
            self::OPTION_THEME => array('string', 'sanitize_theme', self::DEFAULT_THEME),
            self::OPTION_DATE_STYLE => array('string', 'sanitize_date_style', 'long'),
            self::OPTION_WIDTH => array('string', 'sanitize_width', self::DEFAULT_WIDTH),
            self::OPTION_FEATURES => array('array', 'sanitize_features', self::DEFAULT_FEATURES),
            self::OPTION_TEXT_COLOR => array('string', 'sanitize_text_color', self::DEFAULT_TEXT_COLOR),
            self::OPTION_GRADIENT_START => array('string', 'sanitize_gradient_start_color', self::DEFAULT_GRADIENT_START),
            self::OPTION_GRADIENT_END => array('string', 'sanitize_gradient_end_color', self::DEFAULT_GRADIENT_END),
            self::OPTION_ACCENT_COLOR => array('string', 'sanitize_accent_color', self::DEFAULT_ACCENT_COLOR),
            self::OPTION_BUTTON_TEXT_COLOR => array('string', 'sanitize_optional_color', ''),
            self::OPTION_BUTTON2_TEXT_COLOR => array('string', 'sanitize_optional_color', ''),
            self::OPTION_AUTO_INSERT => array('array', 'sanitize_auto_insert', array()),
            self::OPTION_STALE_MONTHS => array('integer', 'sanitize_stale_months', self::DEFAULT_STALE_MONTHS),
        );

        foreach (self::CUSTOM_COLORS as $color) {
            $settings[$color[0]] = array('string', 'sanitize_optional_color', '');
        }

        foreach ($settings as $option_name => $config) {
            register_setting(self::SETTINGS_GROUP, $option_name, array(
                'type' => $config[0],
                'sanitize_callback' => array($this, $config[1]),
                'default' => $config[2],
            ));
        }
    }

    /**
     * Returns every setting in its sanitized, ready-to-render form.
     *
     * @param array|null $source Raw values keyed by option name. Defaults to saved options.
     */
    public function get_settings($source = null) {
        $read = function ($option_name, $default) use ($source) {
            if (is_array($source)) {
                return array_key_exists($option_name, $source) ? $source[$option_name] : null;
            }

            $value = get_option($option_name, false);

            return $value === false ? $default : $value;
        };

        $custom_colors = array();

        foreach (self::CUSTOM_COLORS as $color_key => $color) {
            $custom_colors[$color_key] = $this->sanitize_optional_color($read($color[0], ''));
        }

        return array(
            'custom_colors' => $custom_colors,
            'author' => $this->sanitize_author_name($read(self::OPTION_AUTHOR, self::DEFAULT_AUTHOR)),
            'author_title' => $this->sanitize_plain_text($read(self::OPTION_AUTHOR_TITLE, self::DEFAULT_AUTHOR_TITLE)),
            'author_url' => $this->sanitize_url_value($read(self::OPTION_AUTHOR_URL, '')),
            'avatar_id' => $this->sanitize_avatar_id($read(self::OPTION_AVATAR_ID, 0)),
            'bio' => $this->sanitize_bio_text($read(self::OPTION_BIO, self::get_default_bio_text())),
            'disclaimer' => $this->sanitize_bio_text($read(self::OPTION_DISCLAIMER, self::get_default_disclaimer())),
            'action_buttons' => $this->sanitize_action_buttons($read(self::OPTION_ACTION_BUTTONS, self::get_default_action_buttons())),
            'info_boxes' => $this->sanitize_info_boxes($read(self::OPTION_INFO_BOXES, self::get_default_info_boxes())),
            'theme' => $this->sanitize_theme($read(self::OPTION_THEME, self::DEFAULT_THEME)),
            'date_style' => $this->sanitize_date_style($read(self::OPTION_DATE_STYLE, 'long')),
            'width' => $this->sanitize_width($read(self::OPTION_WIDTH, self::DEFAULT_WIDTH)),
            'features' => $this->sanitize_features($read(self::OPTION_FEATURES, self::DEFAULT_FEATURES)),
            'text_color' => $this->sanitize_text_color($read(self::OPTION_TEXT_COLOR, self::DEFAULT_TEXT_COLOR)),
            'gradient_start' => $this->sanitize_gradient_start_color($read(self::OPTION_GRADIENT_START, self::DEFAULT_GRADIENT_START)),
            'gradient_end' => $this->sanitize_gradient_end_color($read(self::OPTION_GRADIENT_END, self::DEFAULT_GRADIENT_END)),
            'accent_color' => $this->sanitize_accent_color($read(self::OPTION_ACCENT_COLOR, self::DEFAULT_ACCENT_COLOR)),
            'button_text_color' => $this->sanitize_optional_color($read(self::OPTION_BUTTON_TEXT_COLOR, '')),
            'button2_text_color' => $this->sanitize_optional_color($read(self::OPTION_BUTTON2_TEXT_COLOR, '')),
            'auto_insert' => $this->sanitize_auto_insert($read(self::OPTION_AUTO_INSERT, array())),
            'stale_months' => $this->sanitize_stale_months($read(self::OPTION_STALE_MONTHS, self::DEFAULT_STALE_MONTHS)),
        );
    }

    public function sanitize_author_name($value) {
        $sanitized = is_scalar($value) ? sanitize_text_field($value) : '';

        return $sanitized === '' ? self::DEFAULT_AUTHOR : $sanitized;
    }

    public function sanitize_plain_text($value) {
        return is_scalar($value) ? sanitize_text_field($value) : '';
    }

    public function sanitize_url_value($value) {
        return is_scalar($value) ? esc_url_raw(trim((string) $value)) : '';
    }

    public function sanitize_avatar_id($value) {
        $attachment_id = is_scalar($value) ? absint($value) : 0;

        if ($attachment_id && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($attachment_id)) {
            return 0;
        }

        return $attachment_id;
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
                'new_tab' => !empty($button['new_tab']),
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

    public function sanitize_theme($value) {
        return is_string($value) && isset(self::THEMES[$value]) ? $value : self::DEFAULT_THEME;
    }

    public function sanitize_width($value) {
        return is_string($value) && isset(self::WIDTHS[$value]) ? $value : self::DEFAULT_WIDTH;
    }

    public function sanitize_date_style($value) {
        return $value === 'numeric' ? 'numeric' : 'long';
    }

    public function sanitize_features($value) {
        if (!is_array($value)) {
            return array();
        }

        return array_values(array_intersect(array_keys(self::FEATURES), array_map('strval', $value)));
    }

    public function sanitize_auto_insert($value) {
        if (!is_array($value)) {
            return array();
        }

        $allowed = function_exists('get_post_types') ? $this->get_auditable_post_types() : array();

        return array_values(array_intersect($allowed, array_map('strval', $value)));
    }

    public function sanitize_stale_months($value) {
        $months = is_scalar($value) ? (int) $value : 0;

        return $months >= 1 && $months <= 120 ? $months : self::DEFAULT_STALE_MONTHS;
    }

    public function sanitize_text_color($color) {
        return $this->sanitize_color_with_default($color, self::DEFAULT_TEXT_COLOR);
    }

    public function sanitize_gradient_start_color($color) {
        return $this->sanitize_color_with_default($color, self::DEFAULT_GRADIENT_START);
    }

    public function sanitize_gradient_end_color($color) {
        return $this->sanitize_color_with_default($color, self::DEFAULT_GRADIENT_END);
    }

    public function sanitize_accent_color($color) {
        return $this->sanitize_color_with_default($color, self::DEFAULT_ACCENT_COLOR);
    }

    /**
     * Optional color: the form sends array('custom' => '1', 'color' => '#hex').
     * Without the "custom" checkbox the theme color is used and '' is stored.
     */
    public function sanitize_optional_color($value) {
        if (is_array($value)) {
            if (empty($value['custom']) || !isset($value['color']) || !is_string($value['color'])) {
                return '';
            }

            $value = $value['color'];
        }

        $sanitized = is_string($value) ? sanitize_hex_color($value) : '';

        return $sanitized ? strtoupper($sanitized) : '';
    }

    private function sanitize_color_with_default($color, $default_color) {
        $sanitized = is_string($color) ? sanitize_hex_color($color) : '';

        return strtoupper($sanitized ? $sanitized : $default_color);
    }

    private static function get_default_bio_text() {
        return 'Av. Arb. M. Fatih Yavaş tarafından güncel mevzuat ve uygulama notlarıyla hazırlanan kısa bilgilendirme alanı.';
    }

    private static function get_default_disclaimer() {
        return 'Bu içerik genel bilgilendirme amacıyla hazırlanmıştır ve hukuki danışmanlık niteliği taşımaz. Somut durumunuz için bir avukattan görüş almanız önerilir.';
    }

    private static function get_default_action_buttons() {
        return array(
            array(
                'label' => 'Hakkımızda',
                'url' => '/hakkimizda/',
                'new_tab' => false,
            ),
            array(
                'label' => 'İletişim',
                'url' => '/iletisim/',
                'new_tab' => false,
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

    /**
     * Clears common page caches once per request after a setting changes, so
     * that every post shows the new card immediately.
     */
    public function purge_page_caches() {
        if ($this->cache_purged) {
            return;
        }

        $this->cache_purged = true;

        do_action('litespeed_purge_all');

        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }

        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }

        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }

        do_action('mh_sg_settings_updated');
    }

    /* ------------------------------------------------------------------
     * Admin: settings page
     * ------------------------------------------------------------------ */

    public function register_settings_page() {
        add_options_page(
            'Son Güncelleme Bloku',
            'Son Güncelleme Bloku',
            'manage_options',
            self::SETTINGS_PAGE_SLUG,
            array($this, 'render_settings_page')
        );
    }

    public function add_plugin_action_links($links) {
        array_unshift($links, sprintf(
            '<a href="%s">Ayarlar</a>',
            esc_url(admin_url('options-general.php?page=' . self::SETTINGS_PAGE_SLUG))
        ));

        return $links;
    }

    public function enqueue_admin_assets($hook_suffix) {
        if ($hook_suffix === 'settings_page_' . self::SETTINGS_PAGE_SLUG && current_user_can('manage_options')) {
            wp_enqueue_media();

            wp_enqueue_script(
                'mh-sg-settings-script',
                plugins_url('assets/settings.js', __FILE__),
                array(),
                $this->asset_version('assets/settings.js'),
                true
            );

            wp_localize_script('mh-sg-settings-script', 'mhSgSettingsPage', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce(self::PREVIEW_NONCE_ACTION),
                'previewAction' => self::AJAX_PREVIEW_ACTION,
                'avatarOption' => self::OPTION_AVATAR_ID,
            ));

            wp_enqueue_style(
                'mh-sg-settings-style',
                plugins_url('assets/settings.css', __FILE__),
                array(),
                $this->asset_version('assets/settings.css')
            );

            wp_enqueue_style('mh-sg-style');
            wp_enqueue_script('mh-sg-view-script');

            return;
        }

        if (in_array($hook_suffix, array('edit.php'), true)) {
            wp_enqueue_style(
                'mh-sg-dashboard-style',
                plugins_url('assets/dashboard.css', __FILE__),
                array(),
                $this->asset_version('assets/dashboard.css')
            );

            return;
        }

        if ($hook_suffix !== 'index.php' || !current_user_can('edit_posts')) {
            return;
        }

        wp_enqueue_script(
            'mh-sg-dashboard-script',
            plugins_url('assets/dashboard.js', __FILE__),
            array(),
            $this->asset_version('assets/dashboard.js'),
            true
        );

        wp_localize_script('mh-sg-dashboard-script', 'mhSgDashboardAudit', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::DASHBOARD_NONCE_ACTION),
            'outdatedAction' => self::AJAX_OUTDATED_ACTION,
            'messages' => array(
                'checking' => 'Kontrol ediliyor...',
                'outdatedEmpty' => 'Bu ölçüte uyan güncel olmayan yazı/sayfa bulunamadı.',
                'missingEmpty' => 'Tüm yayınlanmış içeriklerde Son Güncelleme kartı bulunuyor.',
                'error' => 'İşlem tamamlanamadı. Lütfen tekrar deneyin.',
            ),
        ));

        wp_enqueue_style(
            'mh-sg-dashboard-style',
            plugins_url('assets/dashboard.css', __FILE__),
            array(),
            $this->asset_version('assets/dashboard.css')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $s = $this->get_settings();
        $avatar_url = $s['avatar_id'] ? wp_get_attachment_image_url($s['avatar_id'], 'thumbnail') : '';
        ?>
        <div class="wrap mh-sg-settings">
            <div class="mh-sg-settings-header">
                <div>
                    <h1>Son Güncelleme Bloku</h1>
                    <p>Buradaki ayarlar sitenizdeki tüm Son Güncelleme kartlarına aynı anda uygulanır. Sağdaki önizleme siz yazdıkça güncellenir.</p>
                </div>
                <span class="mh-sg-version">Sürüm <?php echo esc_html(self::VERSION); ?></span>
            </div>

            <form method="post" action="options.php" id="mh-sg-settings-form" class="mh-sg-settings-layout">
                <?php settings_fields(self::SETTINGS_GROUP); ?>

                <div class="mh-sg-settings-main">
                    <section class="mh-sg-card">
                        <h2>Yazar</h2>
                        <div class="mh-sg-field-grid">
                            <?php $this->render_text_field(self::OPTION_AUTHOR, 'Yazar adı', $s['author'], 'Boş bırakılırsa varsayılan ad kullanılır.'); ?>
                            <?php $this->render_text_field(self::OPTION_AUTHOR_TITLE, 'Unvan', $s['author_title'], 'Örnek: Avukat · Arabulucu'); ?>
                            <?php $this->render_text_field(self::OPTION_AUTHOR_URL, 'Yazar profil bağlantısı', $s['author_url'], 'Doluysa yazar adı bu sayfaya bağlanır (ör. /hakkimizda/).'); ?>
                        </div>

                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Yazar fotoğrafı</span>
                            <div class="mh-sg-avatar-picker" data-mh-sg-avatar>
                                <input type="hidden" name="<?php echo esc_attr(self::OPTION_AVATAR_ID); ?>" value="<?php echo esc_attr($s['avatar_id']); ?>" data-mh-sg-avatar-input />
                                <span class="mh-sg-avatar-thumb" data-mh-sg-avatar-thumb>
                                    <?php if ($avatar_url) : ?>
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="" />
                                    <?php else : ?>
                                        <span><?php echo esc_html(self::get_initials($s['author'])); ?></span>
                                    <?php endif; ?>
                                </span>
                                <button type="button" class="button" data-mh-sg-avatar-select>Fotoğraf seç</button>
                                <button type="button" class="button-link button-link-delete" data-mh-sg-avatar-remove<?php echo $s['avatar_id'] ? '' : ' hidden'; ?>>Kaldır</button>
                            </div>
                            <p class="description">Fotoğraf seçilmezse ad ve soyadın baş harflerinden oluşan bir rozet gösterilir.</p>
                        </div>

                        <div class="mh-sg-field">
                            <label class="mh-sg-label" for="<?php echo esc_attr(self::OPTION_BIO); ?>">Bio</label>
                            <textarea id="<?php echo esc_attr(self::OPTION_BIO); ?>" name="<?php echo esc_attr(self::OPTION_BIO); ?>" class="large-text" rows="3"><?php echo esc_textarea($s['bio']); ?></textarea>
                            <p class="description">Boş bırakırsanız kartta gösterilmez.</p>
                        </div>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Butonlar</h2>
                        <p class="description">İlk buton vurgulu, diğerleri ikincil görünür. <code>tel:</code>, <code>mailto:</code>, WhatsApp ve Google Haritalar bağlantılarına otomatik simge eklenir.</p>
                        <?php $this->render_repeater(self::OPTION_ACTION_BUTTONS, $s['action_buttons'], 'render_action_button_setting_row', 'Buton ekle'); ?>
                        <div class="mh-sg-field-grid is-colors">
                            <?php $this->render_optional_color_field(self::OPTION_BUTTON_TEXT_COLOR, 'İlk buton yazı rengi', $s['button_text_color'], '#FFFFFF'); ?>
                            <?php $this->render_optional_color_field(self::OPTION_BUTTON2_TEXT_COLOR, 'Diğer butonların yazı rengi', $s['button2_text_color'], $s['accent_color']); ?>
                        </div>
                        <p class="description">İşaretlemezseniz yazı rengi seçili temaya göre otomatik belirlenir (Lila temada ilk buton beyaz, diğerleri vurgu rengi).</p>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Bilgi kutuları</h2>
                        <p class="description">Baro, sicil numarası, uzmanlık alanı, ofis adresi gibi kısa bilgiler için kullanılır.</p>
                        <?php $this->render_repeater(self::OPTION_INFO_BOXES, $s['info_boxes'], 'render_info_box_setting_row', 'Kutu ekle'); ?>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Yasal uyarı</h2>
                        <div class="mh-sg-field">
                            <label class="mh-sg-label" for="<?php echo esc_attr(self::OPTION_DISCLAIMER); ?>">Uyarı metni</label>
                            <textarea id="<?php echo esc_attr(self::OPTION_DISCLAIMER); ?>" name="<?php echo esc_attr(self::OPTION_DISCLAIMER); ?>" class="large-text" rows="3"><?php echo esc_textarea($s['disclaimer']); ?></textarea>
                            <p class="description">Kartın en altında küçük puntoyla gösterilir. Boş bırakırsanız gizlenir.</p>
                        </div>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Görünüm</h2>
                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Tema</span>
                            <div class="mh-sg-theme-choices">
                                <?php foreach (self::THEMES as $theme_key => $theme_label) : ?>
                                    <label class="mh-sg-theme-choice is-<?php echo esc_attr($theme_key); ?>">
                                        <input type="radio" name="<?php echo esc_attr(self::OPTION_THEME); ?>" value="<?php echo esc_attr($theme_key); ?>" <?php checked($s['theme'], $theme_key); ?> />
                                        <span class="mh-sg-theme-swatch" aria-hidden="true"></span>
                                        <span><?php echo esc_html($theme_label); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mh-sg-field-grid is-colors">
                            <?php $this->render_color_field(self::OPTION_ACCENT_COLOR, 'Vurgu rengi', $s['accent_color'], 'Tüm temalarda kullanılır. Site moru: #5B3A91, lacivert tema için altın: #C8A24A'); ?>
                            <?php $this->render_color_field(self::OPTION_TEXT_COLOR, 'Metin rengi', $s['text_color'], 'Lacivert tema'); ?>
                            <?php $this->render_color_field(self::OPTION_GRADIENT_START, 'Gradyan başlangıç', $s['gradient_start'], 'Lacivert tema'); ?>
                            <?php $this->render_color_field(self::OPTION_GRADIENT_END, 'Gradyan bitiş', $s['gradient_end'], 'Lacivert tema'); ?>
                        </div>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Kart ve kutu renkleri</h2>
                        <p class="description">Bu renkler tüm temalarda çalışır. Bir rengi değiştirdiğinizde "Özel renk kullan" kendiliğinden işaretlenir; işareti kaldırırsanız temanın rengine döner.</p>
                        <div class="mh-sg-field-grid is-colors">
                            <?php foreach (self::CUSTOM_COLORS as $color_key => $color) : ?>
                                <?php $this->render_optional_color_field($color[0], $color[1], $s['custom_colors'][$color_key], $color[2]); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Yerleşim</h2>

                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Kart genişliği</span>
                            <?php foreach (self::WIDTHS as $width_key => $width_label) : ?>
                                <label class="mh-sg-block-choice"><input type="radio" name="<?php echo esc_attr(self::OPTION_WIDTH); ?>" value="<?php echo esc_attr($width_key); ?>" <?php checked($s['width'], $width_key); ?> /> <?php echo esc_html($width_label); ?></label>
                            <?php endforeach; ?>
                            <p class="description">Tema yazı içindeki blokları metin genişliğine daraltır. "Geniş" seçeneği kartı ana sayfanızdaki bölümler gibi geniş ekranda 1200 px'e kadar açar; dar ekranda her zaman ekrana sığar.</p>
                        </div>

                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Tarih biçimi</span>
                            <label class="mh-sg-inline-choice"><input type="radio" name="<?php echo esc_attr(self::OPTION_DATE_STYLE); ?>" value="long" <?php checked($s['date_style'], 'long'); ?> /> 25 Ekim 2026</label>
                            <label class="mh-sg-inline-choice"><input type="radio" name="<?php echo esc_attr(self::OPTION_DATE_STYLE); ?>" value="numeric" <?php checked($s['date_style'], 'numeric'); ?> /> 25.10.2026</label>
                        </div>

                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Gösterilecek öğeler</span>
                            <?php foreach (self::FEATURES as $feature_key => $feature_label) : ?>
                                <label class="mh-sg-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_FEATURES); ?>[]" value="<?php echo esc_attr($feature_key); ?>" <?php checked(in_array($feature_key, $s['features'], true)); ?> />
                                    <span class="mh-sg-toggle-ui" aria-hidden="true"></span>
                                    <span><?php echo esc_html($feature_label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="mh-sg-card">
                        <h2>Otomatik ekleme ve raporlar</h2>
                        <div class="mh-sg-field">
                            <span class="mh-sg-label">Kartı otomatik ekle</span>
                            <?php foreach ($this->get_auditable_post_types() as $post_type) : ?>
                                <?php $post_type_object = get_post_type_object($post_type); ?>
                                <label class="mh-sg-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_AUTO_INSERT); ?>[]" value="<?php echo esc_attr($post_type); ?>" <?php checked(in_array($post_type, $s['auto_insert'], true)); ?> />
                                    <span class="mh-sg-toggle-ui" aria-hidden="true"></span>
                                    <span><?php echo esc_html($post_type_object ? $post_type_object->labels->name : $post_type); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <p class="description">Seçilen içerik türlerinde blok eklenmemiş yazıların sonuna kart otomatik eklenir. Bloğu zaten içeren yazılarda iki kez gösterilmez. Kısa kod: <code>[<?php echo esc_html(self::SHORTCODE); ?>]</code></p>
                        </div>

                        <div class="mh-sg-field">
                            <label class="mh-sg-label" for="<?php echo esc_attr(self::OPTION_STALE_MONTHS); ?>">Eski sayılma süresi (ay)</label>
                            <input type="number" min="1" max="120" id="<?php echo esc_attr(self::OPTION_STALE_MONTHS); ?>" name="<?php echo esc_attr(self::OPTION_STALE_MONTHS); ?>" value="<?php echo esc_attr($s['stale_months']); ?>" class="small-text" />
                            <p class="description">Yazı listesindeki güncellik etiketleri ve Başlangıç ekranı raporu bu süreyi kullanır.</p>
                        </div>
                    </section>

                    <div class="mh-sg-submit">
                        <?php submit_button('Değişiklikleri kaydet', 'primary large', 'submit', false); ?>
                        <span class="description">Kaydettiğinizde LiteSpeed, WP Rocket, W3 Total Cache ve WP Super Cache önbellekleri otomatik temizlenir.</span>
                    </div>
                </div>

                <div class="mh-sg-settings-preview">
                    <div class="mh-sg-preview-sticky">
                        <div class="mh-sg-preview-head">
                            <h2>Canlı önizleme</h2>
                            <span class="spinner" data-mh-sg-preview-spinner></span>
                        </div>
                        <div class="mh-sg-preview-frame" data-mh-sg-preview>
                            <?php echo $this->render_card($s, $this->get_preview_context(), $this->normalize_block_options(array())); // phpcs:ignore ?>
                        </div>
                        <p class="description">Önizlemede örnek tarih ve okuma süresi kullanılır.</p>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function ajax_settings_preview() {
        if (!current_user_can('manage_options') || !check_ajax_referer(self::PREVIEW_NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error(array('message' => 'Bu işlem için yetkiniz yok.'), 403);
        }

        $source = array();

        foreach (self::get_option_names() as $option_name) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized by get_settings().
            $source[$option_name] = isset($_POST[$option_name]) ? wp_unslash($_POST[$option_name]) : null;
        }

        $settings = $this->get_settings($source);

        wp_send_json_success(array(
            'html' => $this->render_card($settings, $this->get_preview_context(), $this->normalize_block_options(array())),
        ));
    }

    private function get_preview_context() {
        $now = current_datetime();

        return array(
            'modified' => $now,
            'published' => $now->modify('-5 months'),
            'reading_minutes' => 6,
            'permalink' => home_url('/'),
            'title' => get_bloginfo('name'),
        );
    }

    private function render_text_field($option_name, $label, $value, $description = '') {
        ?>
        <div class="mh-sg-field">
            <label class="mh-sg-label" for="<?php echo esc_attr($option_name); ?>"><?php echo esc_html($label); ?></label>
            <input type="text" id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
            <?php if ($description !== '') : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_color_field($option_name, $label, $value, $description = '') {
        ?>
        <div class="mh-sg-field mh-sg-color-field">
            <label class="mh-sg-label" for="<?php echo esc_attr($option_name); ?>"><?php echo esc_html($label); ?></label>
            <input type="color" id="<?php echo esc_attr($option_name); ?>" name="<?php echo esc_attr($option_name); ?>" value="<?php echo esc_attr($value); ?>" />
            <?php if ($description !== '') : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_optional_color_field($option_name, $label, $value, $fallback) {
        $is_custom = $value !== '';
        $id = $option_name . '_color';
        ?>
        <div class="mh-sg-field mh-sg-color-field">
            <label class="mh-sg-label" for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label>
            <input type="color" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($option_name); ?>[color]" value="<?php echo esc_attr($is_custom ? $value : $fallback); ?>" />
            <label class="mh-sg-checkbox">
                <input type="checkbox" name="<?php echo esc_attr($option_name); ?>[custom]" value="1" <?php checked($is_custom); ?> data-mh-sg-custom-color />
                <span>Özel renk kullan</span>
            </label>
        </div>
        <?php
    }

    private function render_repeater($option_name, $rows, $row_renderer, $add_label) {
        ?>
        <div class="mh-sg-settings-repeater" data-mh-sg-repeater data-next-index="<?php echo esc_attr(count($rows)); ?>">
            <input type="hidden" name="<?php echo esc_attr($option_name); ?>[_mh_empty]" value="1" />
            <div class="mh-sg-repeater-items" data-mh-sg-items>
                <?php foreach ($rows as $index => $row) : ?>
                    <?php $this->$row_renderer($index, $row); ?>
                <?php endforeach; ?>
            </div>
            <template data-mh-sg-template>
                <?php $this->$row_renderer('__INDEX__', array()); ?>
            </template>
            <p><button type="button" class="button" data-mh-sg-add>+ <?php echo esc_html($add_label); ?></button></p>
        </div>
        <?php
    }

    private function render_row_controls() {
        ?>
        <div class="mh-sg-row-controls">
            <button type="button" class="button button-small" data-mh-sg-move="up" aria-label="Yukarı taşı">&uarr;</button>
            <button type="button" class="button button-small" data-mh-sg-move="down" aria-label="Aşağı taşı">&darr;</button>
            <button type="button" class="button button-small button-link-delete" data-mh-sg-remove>Sil</button>
        </div>
        <?php
    }

    private function render_action_button_setting_row($index, $button) {
        $label = isset($button['label']) ? $button['label'] : '';
        $url = isset($button['url']) ? $button['url'] : '';
        $new_tab = !empty($button['new_tab']);
        $name = self::OPTION_ACTION_BUTTONS . '[' . $index . ']';
        ?>
        <div class="mh-sg-repeater-row is-button" data-mh-sg-row>
            <label>
                <span>Buton metni</span>
                <input type="text" name="<?php echo esc_attr($name . '[label]'); ?>" value="<?php echo esc_attr($label); ?>" placeholder="Hakkımızda" />
            </label>
            <label>
                <span>Bağlantı</span>
                <input type="text" name="<?php echo esc_attr($name . '[url]'); ?>" value="<?php echo esc_attr($url); ?>" placeholder="/hakkimizda/ veya tel:+90..." />
            </label>
            <label class="mh-sg-checkbox">
                <input type="checkbox" name="<?php echo esc_attr($name . '[new_tab]'); ?>" value="1" <?php checked($new_tab); ?> />
                <span>Yeni sekmede aç</span>
            </label>
            <?php $this->render_row_controls(); ?>
        </div>
        <?php
    }

    private function render_info_box_setting_row($index, $info_box) {
        $title = isset($info_box['title']) ? $info_box['title'] : '';
        $text = isset($info_box['text']) ? $info_box['text'] : '';
        $name = self::OPTION_INFO_BOXES . '[' . $index . ']';
        ?>
        <div class="mh-sg-repeater-row" data-mh-sg-row>
            <label>
                <span>Kutu başlığı</span>
                <input type="text" name="<?php echo esc_attr($name . '[title]'); ?>" value="<?php echo esc_attr($title); ?>" placeholder="Baro" />
            </label>
            <label>
                <span>Kutu metni</span>
                <input type="text" name="<?php echo esc_attr($name . '[text]'); ?>" value="<?php echo esc_attr($text); ?>" placeholder="Bursa Barosu" />
            </label>
            <?php $this->render_row_controls(); ?>
        </div>
        <?php
    }

    /* ------------------------------------------------------------------
     * Admin: post list column
     * ------------------------------------------------------------------ */

    public function register_admin_columns() {
        if (!current_user_can('edit_posts')) {
            return;
        }

        foreach ($this->get_auditable_post_types() as $post_type) {
            add_filter('manage_' . $post_type . '_posts_columns', array($this, 'add_freshness_column'));
            add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'render_freshness_column'), 10, 2);
            add_filter('manage_edit-' . $post_type . '_sortable_columns', array($this, 'add_sortable_freshness_column'));
        }
    }

    public function add_freshness_column($columns) {
        $columns['mh_sg_freshness'] = 'Son güncelleme';

        return $columns;
    }

    public function add_sortable_freshness_column($columns) {
        $columns['mh_sg_freshness'] = 'modified';

        return $columns;
    }

    public function render_freshness_column($column, $post_id) {
        if ($column !== 'mh_sg_freshness') {
            return;
        }

        $post = get_post($post_id);
        $modified = $post ? get_post_datetime($post, 'modified') : false;

        if (!$modified) {
            echo '&mdash;';
            return;
        }

        $settings = $this->get_settings();
        $age = time() - $modified->getTimestamp();
        $stale_seconds = $settings['stale_months'] * MONTH_IN_SECONDS;
        $state = $age >= $stale_seconds ? 'is-stale' : ($age >= $stale_seconds / 2 ? 'is-aging' : 'is-fresh');

        printf(
            '<span class="mh-sg-age %1$s"><span class="mh-sg-age-dot" aria-hidden="true"></span>%2$s</span><br /><span class="mh-sg-age-date">%3$s</span>',
            esc_attr($state),
            esc_html(self::format_age($age)),
            esc_html(self::format_date($modified, 'numeric'))
        );

        if ($post->post_status === 'publish' && !in_array($post->post_type, $settings['auto_insert'], true) && !$this->post_has_card($post)) {
            echo '<br /><span class="mh-sg-missing-tag">Kart yok</span>';
        }
    }

    /* ------------------------------------------------------------------
     * Admin: dashboard widget and reports
     * ------------------------------------------------------------------ */

    public function register_dashboard_widget() {
        if (!current_user_can('edit_posts')) {
            return;
        }

        wp_add_dashboard_widget(
            'mh_sg_date_audit_dashboard_widget',
            'Son Güncelleme Kontrolü',
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget() {
        if (!current_user_can('edit_posts')) {
            return;
        }

        $stale_months = $this->get_settings()['stale_months'];
        $thresholds = array_unique(array(0, 6, $stale_months, 24));
        sort($thresholds);
        ?>
        <div class="mh-sg-dashboard-audit">
            <p class="mh-sg-dashboard-intro">Yayın tarihleri özgün haliyle korunur. Bu araç, güncellenmesi gereken veya kartı eksik olan içerikleri bulmanıza yardımcı olur. Rapor yalnızca düğmeye bastığınızda hazırlanır.</p>

            <div class="mh-sg-dashboard-controls">
                <label>
                    <span class="screen-reader-text">Rapor türü</span>
                    <select id="mh-sg-report-type">
                        <option value="outdated">Güncel olmayan içerikler</option>
                        <option value="missing">Kartı eksik içerikler</option>
                    </select>
                </label>
                <label data-mh-sg-threshold-wrap>
                    <span class="screen-reader-text">Süre</span>
                    <select id="mh-sg-report-threshold">
                        <?php foreach ($thresholds as $months) : ?>
                            <option value="<?php echo esc_attr($months); ?>" <?php selected($months, $stale_months); ?>>
                                <?php echo esc_html($months === 0 ? 'Tümü (en eskiden yeniye)' : $months . ' aydan eski'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="button" class="button button-primary" id="mh-sg-check-outdated-posts">Kontrol et</button>
                <button type="button" class="button" id="mh-sg-clear-outdated-posts">Raporu sil</button>
                <span class="spinner" id="mh-sg-outdated-spinner"></span>
            </div>

            <div id="mh-sg-outdated-summary" class="mh-sg-summary" hidden></div>
            <div id="mh-sg-outdated-message" class="mh-sg-date-audit-message" aria-live="polite"></div>
            <div id="mh-sg-outdated-results" class="mh-sg-date-audit-results"></div>
        </div>
        <?php
    }

    public function ajax_check_outdated_posts() {
        $this->verify_dashboard_ajax_request();

        // phpcs:disable WordPress.Security.NonceVerification -- verified above.
        $report = isset($_POST['report']) && $_POST['report'] === 'missing' ? 'missing' : 'outdated';
        $months = isset($_POST['months']) ? min(120, absint($_POST['months'])) : 0;
        // phpcs:enable

        $post_types = $this->get_auditable_post_types();
        $result = $report === 'missing'
            ? $this->query_posts_without_card($post_types)
            : $this->query_outdated_posts($post_types, $months);

        $items = array();

        foreach ($result['posts'] as $post) {
            if (current_user_can('edit_post', $post->ID)) {
                $items[] = $this->build_outdated_item($post);
            }
        }

        wp_send_json_success(array(
            'report' => $report,
            'items' => $items,
            'count' => count($items),
            'total' => $result['total'],
            'limit' => self::REPORT_LIMIT,
            'summary' => $this->get_summary($post_types),
        ));
    }

    private function verify_dashboard_ajax_request() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Bu işlem için yetkiniz yok.'), 403);
        }

        if (!check_ajax_referer(self::DASHBOARD_NONCE_ACTION, 'nonce', false)) {
            wp_send_json_error(array('message' => 'Güvenlik doğrulaması başarısız oldu.'), 403);
        }
    }

    private function query_outdated_posts(array $post_types, $months) {
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => self::REPORT_LIMIT,
            'orderby' => 'modified',
            'order' => 'ASC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        );

        if ($months > 0) {
            $args['date_query'] = array(
                array(
                    'column' => 'post_modified',
                    'before' => $months . ' months ago',
                    'inclusive' => true,
                ),
            );
        }

        $query = new WP_Query($args);

        return array(
            'posts' => $query->posts,
            'total' => (int) $query->found_posts,
        );
    }

    private function query_posts_without_card(array $post_types) {
        global $wpdb;

        if (empty($post_types)) {
            return array('posts' => array(), 'total' => 0);
        }

        list($where, $params) = $this->get_missing_card_where($post_types);

        // phpcs:disable WordPress.DB.PreparedSQL -- placeholders are built above.
        $total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE {$where}", $params));
        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE {$where} ORDER BY post_modified ASC LIMIT %d",
            array_merge($params, array(self::REPORT_LIMIT))
        ));
        // phpcs:enable

        $posts = array();

        foreach ($ids as $id) {
            $post = get_post((int) $id);

            if ($post instanceof WP_Post) {
                $posts[] = $post;
            }
        }

        return array('posts' => $posts, 'total' => $total);
    }

    /**
     * Builds a WHERE clause for published posts whose content neither contains
     * the block nor references a synced pattern that contains it.
     */
    private function get_missing_card_where(array $post_types) {
        global $wpdb;

        $where = "post_status = 'publish' AND post_type IN (" . implode(',', array_fill(0, count($post_types), '%s')) . ') AND post_content NOT LIKE %s';
        $params = array_merge($post_types, array('%' . $wpdb->esc_like('<!-- wp:' . self::BLOCK_NAME) . '%'));

        $where .= ' AND post_content NOT LIKE %s';
        $params[] = '%' . $wpdb->esc_like('[' . self::SHORTCODE) . '%';

        foreach ($this->get_card_pattern_ids() as $pattern_id) {
            $where .= ' AND post_content NOT LIKE %s';
            $params[] = '%' . $wpdb->esc_like('"ref":' . $pattern_id . '}') . '%';
        }

        return array($where, $params);
    }

    private function get_summary(array $post_types) {
        global $wpdb;

        if (empty($post_types)) {
            return array();
        }

        $settings = $this->get_settings();
        $type_placeholders = implode(',', array_fill(0, count($post_types), '%s'));
        $threshold = gmdate('Y-m-d H:i:s', time() - $settings['stale_months'] * MONTH_IN_SECONDS);

        list($missing_where, $missing_params) = $this->get_missing_card_where($post_types);

        // phpcs:disable WordPress.DB.PreparedSQL -- placeholders are built above.
        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ({$type_placeholders})",
            $post_types
        ));
        $stale = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ({$type_placeholders}) AND post_modified_gmt < %s",
            array_merge($post_types, array($threshold))
        ));
        $missing = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE {$missing_where}", $missing_params));
        // phpcs:enable

        return array(
            'total' => $total,
            'stale' => $stale,
            'staleMonths' => $settings['stale_months'],
            'missing' => $missing,
            'autoInsert' => !empty($settings['auto_insert']),
        );
    }

    private function build_outdated_item($post) {
        $post_type_object = get_post_type_object($post->post_type);
        $title = get_the_title($post);
        $modified = get_post_datetime($post, 'modified');

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
            'age' => $modified ? self::format_age(time() - $modified->getTimestamp()) : '',
            'editUrl' => get_edit_post_link($post->ID, 'raw'),
            'viewUrl' => get_permalink($post),
        );
    }

    private function format_admin_datetime($mysql_date) {
        if (empty($mysql_date) || $mysql_date === '0000-00-00 00:00:00') {
            return '-';
        }

        return mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $mysql_date);
    }

    /* ------------------------------------------------------------------
     * Shared utilities
     * ------------------------------------------------------------------ */

    private function get_auditable_post_types() {
        $post_types = get_post_types(array(
            'public' => true,
            'show_ui' => true,
        ), 'names');

        return array_values(array_diff($post_types, array('attachment')));
    }

    /**
     * IDs of synced patterns (reusable blocks) that contain the card.
     *
     * @return int[]
     */
    private function get_card_pattern_ids() {
        global $wpdb;

        if ($this->pattern_ids === null) {
            $this->pattern_ids = array_map('intval', $wpdb->get_col($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wp_block' AND post_status = 'publish' AND post_content LIKE %s",
                '%' . $wpdb->esc_like('<!-- wp:' . self::BLOCK_NAME) . '%'
            )));
        }

        return $this->pattern_ids;
    }

    private function post_has_card($post) {
        $content = (string) $post->post_content;

        if (has_block(self::BLOCK_NAME, $content) || has_shortcode($content, self::SHORTCODE)) {
            return true;
        }

        foreach ($this->get_card_pattern_ids() as $pattern_id) {
            if (strpos($content, '"ref":' . $pattern_id . '}') !== false) {
                return true;
            }
        }

        return false;
    }

    private function asset_version($relative_path) {
        $path = plugin_dir_path(__FILE__) . $relative_path;

        return file_exists($path) ? self::VERSION . '.' . filemtime($path) : self::VERSION;
    }
}

register_activation_hook(__FILE__, array('Maya_Hukuk_Son_Guncelleme', 'activate'));

$GLOBALS['maya_hukuk_son_guncelleme'] = new Maya_Hukuk_Son_Guncelleme();
