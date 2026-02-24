$(document).ready(function() {
	$('.image-thumbnail').on('click', function(e) {
		console.log('dadsfsdfsd');
		e.preventDefault();
		var imageUrl = $(this).attr('href');
		console.log(imageUrl);
		var modalContent = '<img src="' + imageUrl + '" class="img-fluid">';
		$('#imagePreviewModal .modal-body').html(modalContent);
		$('#imagePreviewModal').modal('show');
	});
});