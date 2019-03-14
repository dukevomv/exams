
$('.search-wrap a').click(function(e){
	e.preventDefault();
  var value = $(this).closest('.search-wrap').find('input').val();

	let parsed = queryString.parse(location.search);
	if(value != '')	parsed.search = value;
	else						delete parsed.search
	delete parsed.page
	location.search = queryString.stringify(parsed)
})

$('.search-wrap input').keypress(function(e) {
  if(e.which == 13) {
  	$(this).closest('.search-wrap').find('a').click()
  }
});
