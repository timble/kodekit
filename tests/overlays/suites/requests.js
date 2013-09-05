module('Requests', {
    setup: function(){},
    teardown: function(){}
});

asyncTest('Simple selector requests', function(){
    expect(2);
    stop();

    var selector1 = '[id=main]', overlay1 = createOverlay({selector: selector1, url: 'samples/Home.html'});

    setTimeout(function() {
        ok(overlay1.element.children(selector1).length, "Overlay contains div#main element from request." );
        start();
    }, 500 );

    var selector2 = 'footer', overlay2 = createOverlay({selector: selector2, url: 'samples/html5.html'});

    setTimeout(function() {
        ok(overlay2.element.children(selector2).length, "Overlay contains footer element from request." );
        start();
    }, 500 );
});

asyncTest('eval Style and Script options', function(){
    expect(8);
    stop(3);

    var selector = '#section', overlay1 = createOverlay({selector: selector, url: 'samples/html5.html', evalStyles: false});
    setTimeout(function() {
        ok(overlay1.element.find('article').css('position') != 'absolute', "<article> position: absolute" );
        ok(overlay1.element.find('footer').css('position') != 'absolute', "<footer> position: absolute" );
        start();
    }, 500 );

    var overlay2 = createOverlay({selector: selector, url: 'samples/html5.html', evalStyles: true});
    setTimeout(function() {
        ok(overlay2.element.find('article').css('position') != 'absolute', "<article> position: absolute" );
        ok(overlay2.element.find('footer').css('position') != 'absolute', "<footer> position: absolute" );
        start();
    }, 500 );

    var overlay3 = createOverlay({selector: selector, url: 'samples/html5.html', evalScripts: false});
    setTimeout(function() {
        ok(overlay3.element.children(selector).length, "Overlay contains footer element from request." );
        start();
    }, 500 );

    var overlay4 = createOverlay({selector: selector, url: 'samples/html5.html', evalScripts: true});
    setTimeout(function() {
        ok(overlay4.element.children(selector).length, "Overlay contains footer element from request." );
        start();
    }, 500 );
});
