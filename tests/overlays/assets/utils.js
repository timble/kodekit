function createOverlay(options, attribs){
    var options = $.extend(true, {}, options),
        attribs = $.extend(true, {'class': '-koowa-overlay'}, attribs);

    $('<div/>', attribs).appendTo('#qunit-fixture');
    return new Koowa.Overlay('#'+attribs.id, options);
};