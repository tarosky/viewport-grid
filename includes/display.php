<?php
// 表示用テンプレートロジック
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * プレビュー用URLのバリデーション
 * @param string $url
 * @return string|WP_Error
 */
function vg_validate_preview_url( $url ) {
	if ( empty( $url ) ) {
		return new WP_Error( 'empty_url', 'プレビューURLが指定されていません。' );
	}
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return new WP_Error( 'invalid_url', '有効なURLを入力してください。' );
	}
	$site_url      = get_site_url();
	$validated_url = wp_validate_redirect( $url, false );
	if ( strpos( $validated_url, $site_url ) !== 0 ) {
		return new WP_Error( 'external_url', 'このプレビュー機能は自サイト内のURLのみ利用できます。' );
	}
	return $validated_url;
}
