$(document).ready(function () {
    $('div#flashMessage').click(function (){
        $(this).fadeToggle();
    });

    $('div#flashMessage').css('cursor', 'pointer');

    setTimeout("$('div#flashMessage').fadeOut();", 15000);
});