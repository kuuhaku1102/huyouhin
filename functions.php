<?php
/**
 * My Simple Theme functions.
 */

// テーマセットアップ
function mytheme_setup() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form', 'gallery', 'caption', 'script', 'style']);

  register_nav_menus([
    'global' => 'グローバルメニュー',
  ]);
}
add_action('after_setup_theme', 'mytheme_setup');

// CSS / JS 読み込み
function mytheme_assets() {
  wp_enqueue_style(
    'mytheme-style',
    get_stylesheet_uri(),
    [],
    filemtime(get_template_directory() . '/style.css')
  );
}
add_action('wp_enqueue_scripts', 'mytheme_assets');
