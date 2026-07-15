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

			this.executeCommand(button);
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

	async executeCommand(button) {
		const confirmation = button.dataset.confirmation;

		if (confirmation && !confirm(confirmation)) {
			return;
		}

		const result_element =
			this._target.querySelector('.js-command-widget-result');
		const original_label = button.textContent;

		button.disabled = true;
		button.textContent = 'Executing...';
		button.setAttribute('aria-busy', 'true');

		this.setResult(result_element, 'pending', 'Executing command...');

		const url = new Curl('zabbix.php');
		url.setArgument('action', 'zabbix_command_widget.execute');

		const request = new URLSearchParams({
			hostid: button.dataset.hostid,
			scriptid: button.dataset.scriptid
		});

		if (button.dataset.manualinputEnabled === '1') {
			request.set(
				'manualinput',
				button.dataset.manualinput ?? ''
			);
		}

		try {
			const response = await fetch(url.getUrl(), {
				method: 'POST',
				headers: {
					'Content-Type':
						'application/x-www-form-urlencoded; charset=UTF-8'
				},
				body: request.toString()
			});

			const response_text = await response.text();
			let data;

			try {
				data = JSON.parse(response_text);
			}
			catch (error) {
				throw new Error(
					`Server returned a non-JSON response: ${response_text.slice(0, 200)}`
				);
			}

			if (!response.ok || data.success !== true) {
				throw new Error(
					data.error
					|| data.message
					|| 'Script execution failed.'
				);
			}

			this.setResult(
				result_element,
				'success',
				data.output !== ''
					? data.output
					: 'Script executed successfully.'
			);
		}
		catch (error) {
			console.error('Command Widget execution failed:', error);
			this.setResult(result_element, 'error', error.message);
		}
		finally {
			button.disabled = false;
			button.textContent = original_label;
			button.removeAttribute('aria-busy');
		}
	}

	setResult(element, state, message) {
		element.classList.remove(
			'zcw-result-pending',
			'zcw-result-success',
			'zcw-result-error'
		);
		element.classList.add(`zcw-result-${state}`);
		element.textContent = message;
	}
}
