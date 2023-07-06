(function ($) {
	"use strict";

	var ajax_url = civi_template_vars.ajax_url;

	$(document).ready(function () {
		//Jobs
		$('select[name="chart-date"]').change(function () {
			var jobs_id = $("#civi-dashboard_chart").data("jobs-id");
			var number_days = $(this).find("option:selected").val();
			$.ajax({
				type: "POST",
				url: ajax_url,
				dataType: "json",
				data: {
					action: "civi_chart_ajax",
					jobs_id: jobs_id,
					number_days: number_days,
				},
				beforeSend: function () {
					$(".civi-chart-warpper")
						.find(".civi-loading-effect")
						.addClass("loading")
						.fadeIn();
				},
				success: function (response) {
					var ctx = document
						.getElementById("civi-dashboard_chart")
						.getContext("2d");
					if (
						window.dashboardChart !== undefined &&
						window.dashboardChart !== null
					) {
						window.dashboardChart.destroy();
					}
					window.dashboardChart = new Chart(ctx, {
						type: "line",
						data: {
							labels: response.labels,
							datasets: [
								{
									label: response.label_view,
									data: response.values_view,
									backgroundColor: "rgb(0, 116, 86)",
									borderColor: "rgb(0, 116, 86)",
								},
								{
									label: response.label_apply,
									data: response.values_apply,
									backgroundColor: "rgb(237,0,6)",
									borderColor: "rgb(237,0,6)",
								},
							],
						},
						options: {
							tooltips: {
								enabled: true,
								mode: "x-axis",
								cornerRadius: 4,
							},
						},
					});
					$(".civi-chart-warpper")
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		});

		if ($("#civi-dashboard_chart").length) {
			var $this = $("#civi-dashboard_chart"),
				labels = $this.data("labels"),
				values_view = $this.data("values_view"),
				label_view = $this.data("label_view"),
				values_apply = $this.data("values_apply"),
				label_apply = $this.data("label_apply");

			var ctx = document
				.getElementById("civi-dashboard_chart")
				.getContext("2d");
			if (
				window.dashboardChart !== undefined &&
				window.dashboardChart !== null
			) {
				window.dashboardChart.destroy();
			}
			window.dashboardChart = new Chart(ctx, {
				type: "line",
				data: {
					labels: labels,
					datasets: [
						{
							label: label_view,
							data: values_view,
							backgroundColor: "rgb(0, 116, 86)",
							borderColor: "rgb(0, 116, 86)",
						},
						{
							label: label_apply,
							data: values_apply,
							backgroundColor: "rgb(237,0,6)",
							borderColor: "rgb(237,0,6)",
						},
					],
				},
				options: {
					tooltips: {
						enabled: true,
						mode: "x-axis",
						cornerRadius: 4,
					},
				},
			});
		}

		//Employer
		$('select[name="chart_employer"]').change(function () {
			var number_days = $(this).find("option:selected").val();
			$.ajax({
				type: "POST",
				url: ajax_url,
				dataType: "json",
				data: {
					action: "civi_chart_employer_ajax",
					number_days: number_days,
				},
				beforeSend: function () {
					$(".civi-chart-employer")
						.find(".civi-loading-effect")
						.addClass("loading")
						.fadeIn();
				},
				success: function (response) {
					var ctx = document
						.getElementById("civi-dashboard_employer")
						.getContext("2d");
					if (
						window.dashboardChart !== undefined &&
						window.dashboardChart !== null
					) {
						window.dashboardChart.destroy();
					}
					var chartEl = document.getElementById("civi-dashboard_employer");
					chartEl.height = 265;
					window.dashboardChart = new Chart(ctx, {
						type: "line",
						data: {
							labels: response.labels_view,
							datasets: [
								{
									label: response.label_view,
									data: response.values_view,
									backgroundColor: "rgb(0, 116, 86)",
									borderColor: "rgb(0, 116, 86)",
								},
							],
						},
						options: {
							tooltips: {
								enabled: true,
								mode: "x-axis",
								cornerRadius: 4,
							},
							plugins: {
								legend: {
									display: false,
								},
							},
						},
					});
					$(".civi-chart-employer")
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		});

		if ($("#civi-dashboard_employer").length) {
			var $this = $("#civi-dashboard_employer"),
				labels = $this.data("labels"),
				values = $this.data("values"),
				label = $this.data("label");

			var ctx = document
				.getElementById("civi-dashboard_employer")
				.getContext("2d");
			if (
				window.dashboardChart !== undefined &&
				window.dashboardChart !== null
			) {
				window.dashboardChart.destroy();
			}
			var chartEl = document.getElementById("civi-dashboard_employer");
			chartEl.height = 265;
			window.dashboardChart = new Chart(ctx, {
				type: "line",
				data: {
					labels: labels,
					datasets: [
						{
							label: label,
							data: values,
							backgroundColor: "rgb(0, 116, 86)",
							borderColor: "rgb(0, 116, 86)",
						},
					],
				},
				options: {
					tooltips: {
						enabled: true,
						mode: "x-axis",
						cornerRadius: 4,
					},
					plugins: {
						legend: {
							display: false,
						},
					},
				},
			});
		}

		//Candidate
		$('select[name="chart_candidate"]').change(function () {
			var number_days = $(this).find("option:selected").val();
			$.ajax({
				type: "POST",
				url: ajax_url,
				dataType: "json",
				data: {
					action: "civi_chart_candidate_ajax",
					number_days: number_days,
				},
				beforeSend: function () {
					$(".civi-chart-candidate")
						.find(".civi-loading-effect")
						.addClass("loading")
						.fadeIn();
				},
				success: function (response) {
					var ctx = document
						.getElementById("civi-dashboard_candidate")
						.getContext("2d");
					if (
						window.dashboardChart !== undefined &&
						window.dashboardChart !== null
					) {
						window.dashboardChart.destroy();
					}
					var chartEl = document.getElementById("civi-dashboard_candidate");
					chartEl.height = 280;
					window.dashboardChart = new Chart(ctx, {
						type: "line",
						data: {
							labels: response.labels_view,
							datasets: [
								{
									label: response.label_view,
									data: response.values_view,
									backgroundColor: "rgb(0, 116, 86)",
									borderColor: "rgb(0, 116, 86)",
								},
							],
						},
						options: {
							tooltips: {
								enabled: true,
								mode: "x-axis",
								cornerRadius: 4,
							},
							plugins: {
								legend: {
									display: false,
								},
							},
						},
					});
					$(".civi-chart-candidate")
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		});
		if ($("#civi-dashboard_candidate").length) {
			var $this = $("#civi-dashboard_candidate"),
				labels = $this.data("labels"),
				values = $this.data("values"),
				label = $this.data("label");

			var ctx = document
				.getElementById("civi-dashboard_candidate")
				.getContext("2d");
			if (
				window.dashboardChart !== undefined &&
				window.dashboardChart !== null
			) {
				window.dashboardChart.destroy();
			}
			var chartEl = document.getElementById("civi-dashboard_candidate");
			chartEl.height = 280;
			window.dashboardChart = new Chart(ctx, {
				type: "line",
				data: {
					labels: labels,
					datasets: [
						{
							label: label,
							data: values,
							backgroundColor: "rgb(0, 116, 86)",
							borderColor: "rgb(0, 116, 86)",
						},
					],
				},
				options: {
					tooltips: {
						enabled: true,
						mode: "x-axis",
						cornerRadius: 4,
					},
					plugins: {
						legend: {
							display: false,
						},
					},
				},
			});
		}
	});
})(jQuery);
