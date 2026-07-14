class CWidgetZabbixCommandWidget extends CWidget {

	onInitialize() {
		super.onInitialize();

		this._on_click = null;
	}

	onActivate() {
		super.onActivate();

		this._on_click = (event) => {
			const button = event.target.closest('.js-command-widget-execute');

			if (button === null || !this._target.contains(button)) {
				return;
			}

			console.log('Execute button clicked');
			alert('Execute button works!');
		};

		this._target.addEventListener('click', this._on_click);
	}

	onDeactivate() {
		if (this._on_click !== null) {
			this._target.removeEventListener('click', this._on_click);
			this._on_click = null;
		}

		super.onDeactivate();
	}
}