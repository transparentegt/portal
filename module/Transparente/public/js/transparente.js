/**
 * Funcionalidad para el portal transparente.gt
 */


$(function(){
    $('a[target!="_blank"]').filter(function() {
        if ($('img', this).length) return false;
        return this.hostname && this.hostname !== location.hostname;
    }).addClass('external')
    .attr('target', '_blank')
    .append(' <i class="fa fa-external-link"></i> ')
    ;
});