/**
 * Mesour DataGrid - styles.js
 *
 * @author Matous Nemec (mesour.com)
 */
(function($) {
    mesour.on.ready('grid-style', function() {
        $('.tree-grid').find('.grid-header').find('.separator:visible:last').hide();
    });

    mesour.on.live('grid-style', function() {
        var grid = $('.data-grid');
        grid.find('.button-component > div').each(function(){
            var count = 0, i = 0;
            $(this).find('> a, > .dropdown').each(function(){
                count += $(this).outerWidth()+5;
                i++;
            });
            $(this).width(count+(12));
        });
        grid.find('.button-component').each(function(){
            var $this = $(this),
                td = $this.closest('td');
            if(td.is('*')) {
                td.width($this.width()+($this.find('a').length*4));
            }
        });
        grid.find('td.button-component').each(function(){
            var $this = $(this);
            $this.width($this.find('> div').width()+($this.find('a').length*4));
        });
    });
})(jQuery);