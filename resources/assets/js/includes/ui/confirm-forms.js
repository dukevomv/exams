
var formToSubmit = false;
var defaultTitle = 'Are you sure?';
var defaultAction = 'Yes';
$('form.confirm-form').submit(function(event) {
  event.preventDefault();
  formToSubmit = $(this);
  var title = $(this).attr('data-confirm-title') != undefined ? $(this).attr('data-confirm-title') : defaultTitle;
  var action = $(this).attr('data-confirm-action') != undefined ? $(this).attr('data-confirm-action') : defaultAction;
  $('#confirm-modal .modal-body').html(title);
  $('#confirm-modal .modal-footer .yes-confirm').html(action);
	$('#confirm-modal').modal('show');
});  

$('#confirm-modal button').on('click',function(event) {
	if($(this).hasClass('yes-confirm')){
		formToSubmit.off('submit');
		formToSubmit.submit();
	} else {
		formToSubmit = false;
	}
});  