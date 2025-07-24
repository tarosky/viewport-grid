<?php
// 管理画面ロジック
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// サブメニュー追加
function vg_add_tools_submenu() {
	add_submenu_page(
		'tools.php',
		'Viewport Grid',
		'Viewport Grid',
		'manage_options',
		'viewport-grid',
		'vg_render_settings_page'
	);
}
add_action( 'admin_menu', 'vg_add_tools_submenu' );

// 設定保存
function vg_save_settings() {
	if ( isset( $_POST['vg_settings_nonce'] ) && wp_verify_nonce( $_POST['vg_settings_nonce'], 'vg_save_settings' ) ) {
		$viewports = [];
		if ( isset( $_POST['vg_label'] ) && is_array( $_POST['vg_label'] ) ) {
			$labels  = $_POST['vg_label'];
			$widths  = $_POST['vg_width'];
			$heights = $_POST['vg_height'];
			foreach ( $labels as $i => $label ) {
				$label  = sanitize_text_field( $label );
				$width  = intval( $widths[ $i ] );
				$height = intval( $heights[ $i ] );
				if ( $label && $width && $height ) {
					$viewports[] = [
						'label'  => $label,
						'width'  => $width,
						'height' => $height,
					];
				}
			}
		}
		update_option( 'viewport_grid_settings', $viewports );
		echo '<div class="updated"><p>設定を保存しました。</p></div>';
	}
}

/**
 * ビューポート設定を取得
 * @return array
 */
function vg_get_viewport_settings() {
	$defaults  = [
		[ 'label' => 'Mobile S', 'width' => 375, 'height' => 667 ],
		[ 'label' => 'Mobile L', 'width' => 412, 'height' => 915 ],
		[ 'label' => 'Tablet Vertical', 'width' => 768, 'height' => 1024 ],
		[ 'label' => 'Tablet Horizontal', 'width' => 1024, 'height' => 768 ],
		[ 'label' => 'LapTop', 'width' => 1440, 'height' => 900 ],
	];
	$viewports = get_option( 'viewport_grid_settings', $defaults );
	if ( ! is_array( $viewports ) || empty( $viewports ) ) {
		$viewports = $defaults;
	}
	return apply_filters( 'viewport_grid_viewports', $viewports );
}

// 設定画面描画
function vg_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	vg_save_settings();
	$viewports = get_option( 'viewport_grid_settings', [] );
	?>
	<div class="wrap">
		<h1>Viewport Grid 設定</h1>
		<h2>ビューポートサイズ設定</h2>
		<form method="post">
		<?php wp_nonce_field( 'vg_save_settings', 'vg_settings_nonce' ); ?>
		<table class="form-table" id="vg-viewport-table">
			<thead>
			<tr><th>名前</th><th>横サイズ(px)</th><th>縦サイズ(px)</th><th></th></tr>
			</thead>
			<tbody id="vg-viewport-table-body">
			<?php
			if ( $viewports ) :
				foreach ( $viewports as $i => $vp ) :
					?>
				<tr>
				<td><input type="text" name="vg_label[]" value="<?php echo esc_attr( $vp['label'] ); ?>" required></td>
				<td><input type="number" name="vg_width[]" value="<?php echo esc_attr( $vp['width'] ); ?>" required></td>
				<td><input type="number" name="vg_height[]" value="<?php echo esc_attr( $vp['height'] ); ?>" required></td>
				<td><button type="button" onclick="this.closest('tr').remove()">削除</button></td>
				</tr>
							<?php
			endforeach;
endif;
			?>
			</tbody>
		</table>
		<p><button type="button" onclick="vgAddRow()">＋行を追加</button></p>
		<p><input type="submit" class="button-primary" value="保存"></p>
		</form>
		<hr>
		<h2>プレビュー用画面</h2>
		<form method="get" action="<?php echo esc_url( home_url( '/multi-preview/' ) ); ?>" target="_blank" onsubmit="return vgValidateUrl(this)">
		<input type="hidden" name="page" value="viewport-grid">
		<label>プレビューしたいURL: <input type="url" name="preview" style="width: 400px;" required></label>
		<input type="submit" class="button" value="プレビューを開く">
		</form>
	</div>
	<?php
}

// 管理画面用スクリプトの読み込み
function vg_admin_enqueue_scripts( $hook ) {
	if ( $hook !== 'tools_page_viewport-grid' ) {
		return;
	}
	wp_enqueue_script( 'jquery-ui-sortable' );
	$inline_js = <<<EOD
function vgAddRow() {
  var table = document.getElementById('vg-viewport-table').getElementsByTagName('tbody')[0];
  var row = table.insertRow();
  row.innerHTML = '<td><input type="text" name="vg_label[]" required></td>' +
    '<td><input type="number" name="vg_width[]" required></td>' +
    '<td><input type="number" name="vg_height[]" required></td>' +
    '<td><button type="button" onclick="this.closest(\'tr\').remove()">削除</button></td>';
}
function vgValidateUrl(form) {
  var url = form.preview.value.trim();
  try {
    new URL(url);
  } catch(e) {
    alert('有効なURLを入力してください。');
    return false;
  }
  return true;
}
jQuery(function($){
  $('#vg-viewport-table-body').sortable({
    axis: 'y',
    cursor: 'move',
    handle: 'td',
    helper: function(e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    },
    placeholder: 'ui-state-highlight'
  });
});
EOD;
	wp_add_inline_script( 'jquery-ui-sortable', $inline_js );
}
add_action( 'admin_enqueue_scripts', 'vg_admin_enqueue_scripts' );
