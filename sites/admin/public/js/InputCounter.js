function updateCharsAll() {
    $.each($('.inputCounter'), function(index, value) {
        $(value).parent().find('span#numChars').text($(value).val().length);
    });
}

function updateChars(e) {
    if ( e == null )
        e = {'target': $('.inputCounter')[0]};

    var counter = $(e.target).parent().find('span#numChars');
    var len = $(e.target).val().length;
    counter.text(len);
    if ( len > counter.attr('rel') && counter.attr('rel') != 0 )
        counter.css({color:'#ff0000'});
    else
        counter.css({color:'#5A5A5A'});
}

$(document).ready(function() {
    updateCharsAll();

    $('.inputCounter').bind('keyup', updateChars);
});
