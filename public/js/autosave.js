/*
 jQuery autoSave v1.0.0 - 2013-04-05
 (c) 2013 Yang Zhao - geniuscarrier.com
 license: http://www.opensource.org/licenses/mit-license.php
 */
(function($) {
    $.fn.autoSave = function(start,finish) {
        return this.each(function() {
            let timer = 0, 
                $this = $(this),
                key = '#'+$(this).attr('id'),
                delay = 500;
            if(localStorage) {
                if($this.attr('type') == 'radio'){
                    key = '@'+$(this).attr('name')
                    if(localStorage.getItem("autoSave"+key) && $this.attr('id') == localStorage.getItem("autoSave"+key))
                        $this.prop('checked',true);
                } else if(localStorage.getItem("autoSave"+key)){
                    if($this.attr('type') == 'checkbox')
                        $this.prop('checked',localStorage.getItem("autoSave"+key));
                    else
                        $this.val(localStorage.getItem("autoSave"+key));
                }
            }
            $this.keyup(function() {
                start();
                clearTimeout(timer);
                var $context = $this.val();
                if(localStorage) {
                    if(!$context)
                        localStorage.removeItem("autoSave"+key)
                    else
                        localStorage.setItem("autoSave"+key, $context);
                }
                timer = setTimeout(function() {
                    finish();
                }, delay);
            });
            $this.change(function() {
                start();
                clearTimeout(timer);
                let $context = false;
                if($this.attr('type') == 'checkbox')
                    $context = $this[0].checked;
                else if($this.attr('type') == 'radio')
                    $context = $this.attr('id');
                if(localStorage) {
                    if(!$context)
                        localStorage.removeItem("autoSave"+key)
                    else
                        localStorage.setItem("autoSave"+key, $context);
                }
                timer = setTimeout(function() {
                    finish();
                }, delay);
            });
        });
    };
})(jQuery);
