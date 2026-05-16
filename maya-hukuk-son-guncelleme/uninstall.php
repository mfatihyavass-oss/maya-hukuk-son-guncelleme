<?php
/**
 * Cleanup plugin options on uninstall.
 *
 * @package maya-hukuk-son-guncelleme
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('mh_sg_author_name');
delete_option('mh_sg_text_color');
delete_option('mh_sg_gradient_start');
delete_option('mh_sg_gradient_end');
