jQuery.ajax({
	url: 'https://www.citymody.com/wp-content/themes/civi-child/js/iller.json',
	dataType: 'json',
	type: 'GET',
	data: "",
	success: function (data) {
			for (row in data) {
					jQuery('#il').append(jQuery('<option></option>').attr('value', data[row].YerAdi).text(data[row].YerAdi).attr('id', data[row].YerID));
			}
	},
	error: function (jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
	}
});

jQuery.ajax({
	url: 'https://www.citymody.com/wp-content/themes/civi-child/js/iller.json',
	dataType: 'json',
	type: 'GET',
	data: "",
	success: function (data) {
			for (row in data) {
					jQuery('#project_city').append(jQuery('<option></option>').attr('value', data[row].YerAdi).text(data[row].YerAdi).attr('id', data[row].YerID));
			}
	},
	error: function (jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
	}
});


jQuery("#il").change(function () {
console.log()
	jQuery("#ilce").attr("disabled", false).html("<option value=''>İlçe Seçiniz..</option>");
	jQuery.ajax({
			url: 'https://www.citymody.com/wp-content/themes/civi-child/js/ilceler.json',
			dataType: 'json',
			type: 'GET',
			data: "",
			success: function (data) {
					for (row in data) {
							if (data[row].SehirID == jQuery("#il").children(":selected").attr("id")) {
									jQuery('#ilce').append(jQuery('<option></option>').attr('value', data[row].YerAdi).text(data[row].YerAdi));
							}
					}
			},
			error: function (jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
			}
	});
})