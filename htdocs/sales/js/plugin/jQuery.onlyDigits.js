// JavaScript Document
jQuery.fn.onlyDigits = function() {
    var k;
    // little trick just in case you want use this:
    $('<span></span>').insertAfter(this);
    var $dText = $(this).next('span').hide();
    // Really cross-browser key event handler
    function Key(e) {
        if (!e.which && ((e.charCode ||
        e.charCode === 0) ? e.charCode: e.keyCode)) {
        e.which = e.charCode || e.keyCode;
        } return e.which; }
    return $(this).each(function() {
        $(this).keydown(function(e) {
            k = Key(e);
            return (
            // Allow CTRL+V , backspace, tab, delete, arrows,
            // numbers and keypad numbers ONLY
            ( k == 86 && e.ctrlKey ) || (k == 224 && e.metaKey) ||
            k == 8 || k == 9 || k == 46
              || (k >= 37 && k <= 40) || (k >= 48 && k <= 57)
              || (k >= 96 && k <= 105));
        }).keyup(function(e) {
            // Check if pasted content is Number
            if (isNaN(this.value)) {
                // re-add stored digits if CTRL+V have non digits chars
                $(this).val($dText.text());
            } else { // store digits only of easy access
                $dText.empty().append(this.value);
            }
        });
    })
};