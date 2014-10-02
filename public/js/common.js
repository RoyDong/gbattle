$(document).foundation();

$(document).ready(function(){
    $('#series-droplist').change(function () {
        var id = $(this).val();
        var item = $('#item-droplist-' + id);
        var items = $('.item-droplist');
        items.attr('name', '');
        items.addClass('none');
        item.removeClass('none');
        item.attr('name', 'item');
    });
});