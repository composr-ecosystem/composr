var timeout    = 500;
var closetimer = 0;
var ddmenuitem = 0;

function menuOpen() {
    menuCanceltimer();
    menuClose();
    ddmenuitem = $(this).find('ul').css('visibility', 'visible');
}

function menuClose() {
    if (ddmenuitem) {
        ddmenuitem.css('visibility', 'hidden');
    }
}

function menuTimer() {
    closetimer = window.setTimeout(menuClose, timeout);
}

function menuCanceltimer() {
    if (closetimer) {
        window.clearTimeout(closetimer);
        closetimer = null;
    }
}

$(document).ready(function() {
    $('#file-nav > li').bind('mouseover', menuOpen);
    $('#file-nav > li').bind('mouseout', menuTimer);
});

document.onclick = menuClose;