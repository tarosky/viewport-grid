<?php
/*
Plugin Name: Viewport Grid
Plugin URI: https://github.com/tarosky/viewport-grid
Description: 指定したURLを複数のビューポートサイズで同時に表示し、レスポンシブデザインを比較・検証できるWordPress管理者向けプラグイン。
Version: 1.0.0
Requires at least: 6.6
Tested up to: 6.8
Author: Tarosky INC.
License: GPLv2 or later
Text Domain: viewport-grid
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'VG_VERSION', '1.0.0' );

// 必要なファイルを読み込む
require_once VG_PLUGIN_DIR . 'includes/settings.php';
require_once VG_PLUGIN_DIR . 'includes/display.php';

// ルーティング追加
function vg_add_rewrite_rule() {
	add_rewrite_rule(
		'^multi-preview/?$',
		'index.php?vg_multi_preview=1',
		'top'
	);
}
add_action( 'init', 'vg_add_rewrite_rule' );

// クエリ変数追加
function vg_add_query_vars( $vars ) {
	$vars[] = 'vg_multi_preview';
	$vars[] = 'preview'; // プレビューURL用
	return $vars;
}
add_filter( 'query_vars', 'vg_add_query_vars' );

// テンプレート差し替え
function vg_template_include( $template ) {
	if ( get_query_var( 'vg_multi_preview' ) ) {
		return VG_PLUGIN_DIR . 'templates/preview-template.php';
	}
	return $template;
}
add_filter( 'template_include', 'vg_template_include' );

// admin_bar=0クエリがある場合は管理バーを非表示
function vg_maybe_hide_admin_bar( $show ) {
	if ( isset( $_GET['admin_bar'] ) && '0' === $_GET['admin_bar'] ) {
		return false;
	}
	return $show;
}
add_filter( 'show_admin_bar', 'vg_maybe_hide_admin_bar' );
