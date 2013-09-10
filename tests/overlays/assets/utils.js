function createOverlay(options, attribs){
    var options = jQuery.extend(true, {}, options),
        attribs = jQuery.extend(true, {'class': '-koowa-overlay', id: 'test'+(new Date).valueOf()}, attribs);

    jQuery('<div/>', attribs).appendTo('#qunit-fixture');
    return new Koowa.Overlay('#'+attribs.id, options);
};