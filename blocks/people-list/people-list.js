/**
 * People List block — open/close bio dialogs for multi-person grid tiles.
 */
(function () {
	'use strict';

	document.addEventListener('click', function (e) {
		var openBtn = e.target.closest('[data-people-list-dialog-open]');
		if (openBtn) {
			var id = openBtn.getAttribute('aria-controls');
			var dlg = id ? document.getElementById(id) : null;
			if (dlg && typeof dlg.showModal === 'function') {
				dlg.showModal();
			}
			return;
		}

		var openDialog = e.target.closest('.c-peopleList__dialog');
		if (!openDialog || !openDialog.open || typeof openDialog.close !== 'function') {
			return;
		}

		if (e.target.closest('[data-people-list-dialog-close]')) {
			openDialog.close();
			return;
		}

		/* Keep mailto / social usable without dismissing first. */
		if (e.target.closest('a[href]')) {
			return;
		}

		openDialog.close();
	});
})();
