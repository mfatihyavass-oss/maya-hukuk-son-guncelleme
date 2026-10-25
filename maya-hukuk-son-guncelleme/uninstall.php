<?php
/**
 * Cleanup plugin options on uninstall.
 *
 * @package maya-hukuk-son-guncelleme
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$mh_sg_options = array(
    'mh_sg_author_name',
    'mh_sg_author_title',
    'mh_sg_author_url',
    'mh_sg_avatar_id',
    'mh_sg_bio_text',
    'mh_sg_disclaimer',
    'mh_sg_action_buttons',
    'mh_sg_info_boxes',
    'mh_sg_theme',
    'mh_sg_date_style',
    'mh_sg_width',
    'mh_sg_features',
    'mh_sg_text_color',
    'mh_sg_gradient_start',
    'mh_sg_gradient_end',
    'mh_sg_accent_color',
    'mh_sg_button_text_color',
    'mh_sg_button2_text_color',
    'mh_sg_card_bg_color',
    'mh_sg_card_border_color',
    'mh_sg_name_color',
    'mh_sg_body_color',
    'mh_sg_badge_bg_color',
    'mh_sg_badge_text_color',
    'mh_sg_box_bg_color',
    'mh_sg_box_border_color',
    'mh_sg_box_stripe_color',
    'mh_sg_box_title_color',
    'mh_sg_box_text_color',
    'mh_sg_button_bg_color',
    'mh_sg_button2_bg_color',
    'mh_sg_auto_insert',
    'mh_sg_stale_months',
);

foreach ($mh_sg_options as $mh_sg_option) {
    delete_option($mh_sg_option);
}
