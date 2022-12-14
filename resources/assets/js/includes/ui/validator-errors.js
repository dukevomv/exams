window.showValidatorErrors = function(data){
  if(!data.responseJSON || data.responseJSON.length == 0)
    return 1;
  $('.wrap-for-banners .ajax-errors').remove()
  var errors = '<div class="alert alert-danger ajax-errors"><ul>'
  const allErrors = data.responseJSON.errors ? data.responseJSON.errors : data.responseJSON;
  $.each(allErrors,function(key,val){
    let content = Array.isArray(val) ? val[0] : val;
    errors += '<li>'+content+'</li>'
  });
  errors += '</ul></div>'
  $('.wrap-for-banners').append(errors)
}