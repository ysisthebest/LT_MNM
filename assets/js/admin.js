(function ($) {
	'use strict';

	function updateTongLuong() {
		var hs = parseFloat($('#HeSoLuong').val()) || 0;
		var lb = parseFloat($('#LuongCoBan').val()) || 0;
		var tong = Math.round(hs * lb);
		var formatted = tong.toLocaleString('vi-VN');

		$('#quanlicb-tong-luong-preview').text(formatted);
		$('#quanlicb-aside-tong-luong').text(formatted + ' d');
	}

	function updateSummaryField($input) {
		var targets = $input.data('summary-target');
		var value = ($input.val() || '').toString().trim();

		if (!targets) {
			return;
		}

		$(targets).text(value || '---');
	}

	$(document).on('input change', '.quanlicb-calc-input', updateTongLuong);
	$(document).on('input change', '.quanlicb-summary-input', function () {
		updateSummaryField($(this));
	});

	$('.quanlicb-summary-input').each(function () {
		updateSummaryField($(this));
	});

	function closeAllSelectArrows() {
		$('.quanlicb-select-wrap').removeClass('is-open');
	}

	$(document)
		.on('click', '.quanlicb-select-wrap select', function () {
			var $wrap = $(this).closest('.quanlicb-select-wrap');

			if ($wrap.hasClass('is-open')) {
				$wrap.removeClass('is-open');
				return;
			}

			closeAllSelectArrows();
			$wrap.addClass('is-open');
		})
		.on('keydown', '.quanlicb-select-wrap select', function (e) {
			if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
				closeAllSelectArrows();
				$(this).closest('.quanlicb-select-wrap').addClass('is-open');
			}
			if (e.key === 'Escape' || e.key === 'Tab') {
				$(this).closest('.quanlicb-select-wrap').removeClass('is-open');
			}
		})
		.on('change blur', '.quanlicb-select-wrap select', function () {
			$(this).closest('.quanlicb-select-wrap').removeClass('is-open');
		})
		.on('mousedown', function (e) {
			if (!$(e.target).closest('.quanlicb-select-wrap').length) {
				closeAllSelectArrows();
			}
		});

	var mediaFrame;
	$('#quanlicb-upload-btn').on('click', function (e) {
		e.preventDefault();
		if (mediaFrame) {
			mediaFrame.open();
			return;
		}

		mediaFrame = wp.media({
			title: 'Chon anh dai dien can bo',
			button: { text: 'Chon anh' },
			multiple: false,
			library: { type: 'image' }
		});

		mediaFrame.on('select', function () {
			var attachment = mediaFrame.state().get('selection').first().toJSON();
			var url = attachment.sizes && attachment.sizes.thumbnail
				? attachment.sizes.thumbnail.url
				: attachment.url;

			$('#AnhDaiDien').val(attachment.id);
			$('#quanlicb-preview').html('<img src="' + url + '" alt="" />');
			$('#quanlicb-remove-img').show();
		});

		mediaFrame.open();
	});

	$('#quanlicb-remove-img').on('click', function (e) {
		e.preventDefault();
		$('#AnhDaiDien').val('');
		$('#quanlicb-preview').empty();
		$(this).hide();
	});

	if (typeof Chart === 'undefined') {
		return;
	}

	var chartFont = {
		family: 'Be Vietnam Pro, Segoe UI, sans-serif',
		size: 12,
		weight: '700'
	};
	var palette = ['#0f4c81', '#18a999', '#f59e0b', '#ef4444', '#7c3aed', '#14b8a6'];

	Chart.defaults.color = '#617182';
	Chart.defaults.font.family = chartFont.family;

	function initBarChart(canvasId, labels, data, datasetLabel, integerYAxis) {
		if (!labels || !labels.length) {
			return;
		}
		var canvas = document.getElementById(canvasId);
		if (!canvas) {
			return;
		}

		datasetLabel = datasetLabel || 'Số lượng';
		integerYAxis = integerYAxis !== false;

		var yTicks = { font: chartFont };
		if (integerYAxis) {
			yTicks.stepSize = 1;
		}

		new Chart(canvas, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: datasetLabel,
					data: data,
					backgroundColor: palette,
					borderRadius: 14,
					borderSkipped: false,
					maxBarThickness: 42
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						backgroundColor: '#132634',
						titleColor: '#ffffff',
						bodyColor: '#e5edf4',
						titleFont: chartFont,
						bodyFont: chartFont,
						padding: 12,
						displayColors: false
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: yTicks,
						grid: { color: 'rgba(19, 38, 52, 0.08)', drawBorder: false }
					},
					x: {
						ticks: { font: chartFont, maxRotation: 0 },
						grid: { display: false }
					}
				}
			}
		});
	}

	function initDoughnutChart(canvasId, labels, data) {
		if (!labels || !labels.length) {
			return;
		}
		var canvas = document.getElementById(canvasId);
		if (!canvas) {
			return;
		}

		new Chart(canvas, {
			type: 'doughnut',
			data: {
				labels: labels,
				datasets: [{
					data: data,
					backgroundColor: palette,
					borderWidth: 4,
					borderColor: '#ffffff',
					hoverOffset: 4
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				cutout: '68%',
				plugins: {
					legend: {
						position: 'bottom',
						labels: {
							font: chartFont,
							color: '#617182',
							boxWidth: 12,
							padding: 16,
							usePointStyle: true,
							pointStyle: 'circle'
						}
					},
					tooltip: {
						backgroundColor: '#132634',
						titleColor: '#ffffff',
						bodyColor: '#e5edf4',
						titleFont: chartFont,
						bodyFont: chartFont,
						padding: 12
					}
				},
				layout: { padding: 8 }
			}
		});
	}

	var dataEl = document.getElementById('quanlicb-chart-data');
	if (dataEl) {
		var chartData = JSON.parse(dataEl.textContent);
		if (chartData.phongBan) {
			initBarChart('chartPhongBan', chartData.phongBan.labels, chartData.phongBan.data, 'Số lượng', true);
		}
		if (chartData.gioiTinh) {
			initDoughnutChart('chartGioiTinh', chartData.gioiTinh.labels, chartData.gioiTinh.data);
		}
		if (chartData.reportPhongBan) {
			initBarChart('chartReportPhongBan', chartData.reportPhongBan.labels, chartData.reportPhongBan.data, 'Tổng lương', false);
		}
	}

	var dashboardEl = document.getElementById('quanlicb-dashboard-chart-data');
	if (dashboardEl) {
		var dashboardData = JSON.parse(dashboardEl.textContent);
		if (dashboardData.phongBan) {
			initBarChart('chartDashboardPhongBan', dashboardData.phongBan.labels, dashboardData.phongBan.data, 'Số lượng', true);
		}
		if (dashboardData.chucVu) {
			initDoughnutChart('chartDashboardChucVu', dashboardData.chucVu.labels, dashboardData.chucVu.data);
		}
	}

})(jQuery);
