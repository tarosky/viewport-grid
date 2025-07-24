# Viewport Grid 開発指示書

## 📝 プラグイン概要

**Viewport Grid** は、指定した URL を複数のビューポートサイズ（スマホ・タブレット・PCなど）で同時に表示し、**レスポンシブデザインを一画面で比較・検証**できる WordPress 管理者向けプラグインです。主にデザイナー・開発者がモバイル対応を確認する用途で使用します。

---

## 🔗 表示URLとクエリ構造

- プラグインは次のような構成で動作する：

/multi-preview/?preview=https://example.com/sample-page/

- `preview` クエリパラメータで読み込む対象URLを指定
- 管理画面で設定した **ビューポートサイズのリスト** に基づいて iframe を並べて表示

---

## 🔧 機能一覧

### フロント表示（`/multi-preview/`）

- `iframe` を複数表示
  - 管理画面で設定されたサイズごとに分割表示（例：375×667, 768×1024）
  - 各ブロックにサイズラベル付き（例：「Mobile (375×667)」）
- `?preview=` で表示するページを指定
- 全体リロードボタン付き（必要に応じて個別リロードも可能）

### 管理画面（設定ページ）

- メニューに「Viewport Grid」を追加
- iframeで表示する **ビューポートサイズの一覧を設定可能**
  - 設定は複数登録できる（サイズラベル・横幅・縦幅）
- JSON形式 or フォームリピートフィールドで設定
- `wp_options` に保存（例：`viewport_grid_settings`）

---

## 💻 技術的要件

### ルーティング

- `add_rewrite_rule()` で `/multi-preview/` をマッピング
- `template_include` にて専用テンプレートを差し替える

### フロント表示（iframeビュー）

- HTML + CSS + JavaScript による表示
- iframe による埋め込みレイアウト
- JavaScriptで各iframeをリロード可能に（ボタン制御）

### 管理画面

- `add_menu_page()` or `add_options_page()` で設定画面を実装
- 設定フィールド：
  - サイズラベル（例：「iPhone SE」）
  - 横幅（例：375）
  - 縦幅（例：667）

### データ保存形式（例）

```json
[
  {
    "label": "Mobile (375×667)",
    "width": 375,
    "height": 667
  },
  {
    "label": "Tablet (768×1024)",
    "width": 768,
    "height": 1024
  }
]


⸻

📁 ディレクトリ構成（例）

viewport-grid/
├── viewport-grid.php       // メインプラグインファイル
├── includes/
│   ├── settings.php        // 管理画面ロジック
│   └── display.php         // 表示用テンプレートロジック
├── templates/
│   └── preview-template.php // iframeビューHTML
├── assets/
│   ├── style.css
│   └── script.js


⸻

📌 UI仕様（プレビュー画面）
	•	ページ最上部に入力URL（preview）表示
	•	各ビューポートに以下を表示：
	•	ラベル（例：「iPhone SE」）
	•	サイズ（例：375×667）
	•	リロードボタン（各iframe or 全体共通）

⸻

🛡️ 注意事項
	•	iframeの読み込み対象に X-Frame-Options: DENY があると表示されない（例：他サイト）
	•	preview先URLには WordPress サイト上のページを想定
	•	管理画面は manage_options 権限で制限

⸻

✅ 拡張アイデア（将来的なオプション）
	•	同期スクロール機能
	•	縦スクロール固定フレーム付き比較
	•	メディアクエリに反応するハイライト
	•	比較対象に異なる URL を指定（A/B 比較）

⸻

🧪 開発メモ
	•	テスト環境：
	•	WordPress 6.x
	•	ローカル開発環境（Local / MAMP / Docker 等）
	•	スタイルは軽量な CSS（Tailwind 等なし）
	•	外部JSライブラリは基本的に使わない（Vanilla JS）

⸻

🏷 プラグインメタ情報
	•	プラグイン名：Viewport Grid
	•	スラッグ：viewport-grid
	•	バージョン：1.0.0
	•	作者：Tarosky INC.
	•	ライセンス：GPLv2 or later

---
