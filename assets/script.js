// Viewport Grid プラグイン用JS

document.addEventListener('DOMContentLoaded', function () {
  // 各iframeリロード
  document.querySelectorAll('[data-vg-reload]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.getElementById(btn.dataset.vgReload);
      if (target && target.tagName === 'IFRAME') {
        target.contentWindow.location.reload();
      }
    });
  });
  // 全体リロード
  var allReload = document.getElementById('vg-all-reload');
  if (allReload) {
    allReload.addEventListener('click', function () {
      document.querySelectorAll('.vg-viewport iframe').forEach(function (iframe) {
        iframe.contentWindow.location.reload();
      });
    });
  }
} );
