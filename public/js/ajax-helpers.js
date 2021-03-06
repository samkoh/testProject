(function(){

	var submitAjaxRequest = function(e) {

		var form = $(this);
		var method = form.find('input[name="_method"]').val() || 'POST';

		$.ajax({
			type: method,
			url: form.prop('action'),
			data: form.serialize(),
			success: function() {
				$.publish('form.submitted', form);
			}
		})

		e.prevenDefault();
	};

	//Forms marked with the "data-remote" attribute will submit, via AJAX.
	$('form[data-remote]').on('submit', submitAjaxRequest);
})();