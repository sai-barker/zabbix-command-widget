class WidgetLessonGaugeChart extends CWidget {

	static UNIT_AUTO = 0;
	static UNIT_STATIC = 1;

	onInitialize() {
		this._refresh_frame = null;
		this._chart_container = null;
		this._canvas = null;
		this._chart_color = null;
		this._min = null;
		this._max = null;
		this._value = null;
		this._last_value = null;
		this._units = '';
	}

	processUpdateResponse(response) {
		if (response.history === null) {
			this._value = null;
			this._units = '';
		}
		else {
			this._value = Number(response.history.value);
			this._units = response.fields_values.value_units == WidgetLessonGaugeChart.UNIT_AUTO
				? response.history.units
				: response.fields_values.value_static_units;
		}

		this._chart_color = response.fields_values.chart_color;
		this._min = Number(response.fields_values.value_min);
		this._max = Number(response.fields_values.value_max);

		super.processUpdateResponse(response);
	}

	setContents(response) {
		if (this._canvas === null) {
			super.setContents(response);

			this._chart_container = this._body.querySelector('.chart');
			this._chart_container.style.height =
				`${this._getContentsSize().height - this._body.querySelector('.description').clientHeight}px`;
			this._canvas = document.createElement('canvas');

			this._chart_container.appendChild(this._canvas);

			this._resizeChart();
		}

		this._updatedChart();
	}

	resize() {
		super.onResize();

		if (this.getState() === WIDGET_STATE_ACTIVE) {
			this._resizeChart();
			this._updatedChart();
		}
	}

	_resizeChart() {
		const ctx = this._canvas.getContext('2d');
		const dpr = window.devicePixelRatio;

		this._canvas.style.display = 'none';
		const size = Math.min(this._chart_container.offsetWidth, this._chart_container.offsetHeight);
		this._canvas.style.display = '';

		this._canvas.width = size * dpr;
		this._canvas.height = size * dpr;

		ctx.scale(dpr, dpr);

		this._canvas.style.width = `${size}px`;
		this._canvas.style.height = `${size}px`;

		this._refresh_frame = null;
	}

	_updatedChart() {
		if (this._last_value === null) {
			this._last_value = this._min;
		}

		const start_time = Date.now();
		const end_time = start_time + 400;

		const animate = () => {
			const time = Date.now();

			if (time <= end_time) {
				const progress = (time - start_time) / (end_time - start_time);
				const smooth_progress = 0.5 + Math.sin(Math.PI * (progress - 0.5)) / 2;
				let value = this._value !== null ? this._value : this._min;
				value = (this._last_value + (value - this._last_value) * smooth_progress - this._min) / (this._max - this._min);

				const ctx = this._canvas.getContext('2d');
				const size = this._canvas.width;
				const char_weight = size / 12;
				const char_shadow = 3;
				const char_x = size / 2;
				const char_y = size / 2;
				const char_radius = (size - char_weight) / 2 - char_shadow;

				const font_ratio = 32 / 100;

				ctx.clearRect(0, 0, size, size);

				ctx.beginPath();
				ctx.shadowBlur = char_shadow;
				ctx.shadowColor = '#bbb';
				ctx.strokeStyle = '#eee';
				ctx.lineWidth = char_weight;
				ctx.lineCap = 'round';
				ctx.arc(char_x, char_y, char_radius, Math.PI * 0.749, Math.PI * 2.251, false);
				ctx.stroke();

				ctx.beginPath();
				ctx.strokeStyle = `#${this._chart_color}`;
				ctx.lineWidth = char_weight - 2;
				ctx.lineCap = 'round';
				ctx.arc(char_x, char_y, char_radius, Math.PI * 0.75,
					Math.PI * (0.75 + (1.5 * Math.min(1, Math.max(0, value)))), false
				);
				ctx.stroke();

				ctx.shadowBlur = 2;
				ctx.fillStyle = '#1f2c33';
				ctx.font = `${(char_radius * font_ratio)|0}px Arial`;
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.fillText(`${this._value !== null ? this._value : t('No data')}${this._units}`,
					char_x, char_y, size - char_shadow * 4 - char_weight * 2
				);

				ctx.fillStyle = '#768d99';
				ctx.font = `${(char_radius * font_ratio * .5)|0}px Arial`;
				ctx.textBaseline = 'top';

				ctx.textAlign = 'left';
				ctx.fillText(`${this._min}${this._min != '' ? this._units : ''}`,
					char_weight * .75, size - char_weight * 1.25, size / 2 - char_weight
				);

				ctx.textAlign = 'right';
				ctx.fillText(`${this._max}${this._max != '' ? this._units : ''}`,
					size - char_weight * .75, size - char_weight * 1.25, size / 2 - char_weight
				);

				requestAnimationFrame(animate);
			}
			else {
				this._last_value = this._value;
			}
		};

		requestAnimationFrame(animate);
	}
}
