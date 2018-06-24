
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

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