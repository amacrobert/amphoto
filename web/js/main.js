$(function() {
    var wall = new Freewall('#freewall');
    wall.reset({
        selector: '.item',
        animate: true,
        gutterX: 3,
        gutterY: 3,
        //fixSize: true,
        cellW: 20,
        cellH: 200,
        onResize: function() { wall.fitWidth(); }
    });
    wall.fitWidth();
    // for scroll bar appear;
    $(window).trigger("resize");
});
