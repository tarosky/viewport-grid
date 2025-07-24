<?php
// iframeビューHTMLテンプレート
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once VG_PLUGIN_DIR . 'includes/display.php';
require_once VG_PLUGIN_DIR . 'includes/settings.php';

// ビューポート設定を取得
$viewports = vg_get_viewport_settings();

$preview_url = get_query_var( 'preview' );
$validated   = vg_validate_preview_url( $preview_url );
if ( is_wp_error( $validated ) ) {
	wp_die( $validated->get_error_message() );
}
$preview_url = add_query_arg( 'admin_bar', '0', $validated );
?><!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Viewport Grid Preview</title>
	<link rel="stylesheet" href="<?php echo esc_url( VG_PLUGIN_URL . 'assets/style.css' ); ?>">
	<script src="<?php echo includes_url('js/jquery/jquery.js'); ?>"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/packery/2.1.2/packery.pkgd.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/draggabilly/2.3.0/draggabilly.pkgd.min.js"></script>
</head>
<body>
	<div class="vg-preview-header">
		<strong>Preview URL:</strong> <?php echo esc_html( remove_query_arg( 'admin_bar', $preview_url ) ); ?>
		<button id="vg-all-reload" type="button">全体リロード</button>
	</div>
	<div class="vg-grid clearfix">
		<?php foreach ( $viewports as $vp ) :
			$style = sprintf( 'width: %dpx; height: %dpx;', $vp['width'], $vp['height'] + 24 );
			?>
			<div class="vg-viewport" style="<?php echo esc_attr( $style ); ?>">
				<div class="vg-label">
					<strong><?php echo esc_html( $vp['label'] ); ?></strong> (<?php echo $vp['width'] . '×' . $vp['height']; ?>)
					<button type="button" data-vg-reload="vg-frame-<?php echo $vp['width']; ?>">リロード</button>
				</div>
				<iframe
					id="vg-frame-<?php echo $vp['width']; ?>"
					src="<?php echo esc_url( $preview_url ); ?>"
					width="<?php echo $vp['width']; ?>"
					height="<?php echo $vp['height']; ?>"
					loading="lazy"
				></iframe>
			</div>
		<?php endforeach; ?>
	</div>
	<script src="<?php echo esc_url( VG_PLUGIN_URL . 'assets/script.js' ); ?>"></script>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var grid = document.querySelector('.vg-grid');
		if (grid && window.Packery && window.Draggabilly) {
			var pckry = new Packery(grid, {
				itemSelector: '.vg-viewport',
				gutter: 10
			});
			// 各タイルをDraggabillyでドラッグ可能に
			grid.querySelectorAll('.vg-viewport').forEach(function(item) {
				var draggie = new Draggabilly(item);
				pckry.bindDraggabillyEvents(draggie);
			});
		}
	});
	</script>
</body>
</html>
