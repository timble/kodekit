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
        notEqual(overlay1.element.find('article').css('position'), 'absolute', "evalStyles: false, <article> position: static, inline style tag not applied" );
        notEqual(overlay1.element.find('footer').css('position'), 'absolute', "evalStyles: false, <footer> position: static, external stylesheet not applied" );
        start();
    }, 500 );

    var overlay2 = createOverlay({selector: selector, url: 'samples/html5.html', evalStyles: true});
    setTimeout(function() {
        equal(overlay2.element.find('article').css('position'), 'absolute', "evalStyles: true, <article> position: absolute, inline style tag applied" );
        equal(overlay2.element.find('footer').css('position'), 'absolute', "evalStyles: true, <footer> position: absolute, external stylesheet applied" );
        start();
    }, 500 );

    var overlay3 = createOverlay({selector: selector, url: 'samples/html5.html', evalScripts: false});
    setTimeout(function() {
        notEqual(overlay3.element.find('#section header').css('position'), 'absolute', "evalScripts: false, #section <header> position: static, inline script tag did not run" );
        notEqual(overlay3.element.find('#section h2').css('position'), 'absolute', "evalScripts: false, #section <h2> position: static, external script did not run" );
        start();
    }, 500 );

    var overlay4 = createOverlay({selector: selector, url: 'samples/html5.html', evalScripts: true});
    setTimeout(function() {
        equal(overlay4.element.find('#section header').css('position'), 'absolute', "evalScripts: true, #section <header> position: absolute, inline script tag ran" );
        equal(overlay4.element.find('#section h2').css('position'), 'absolute', "evalScripts: true, #section <h2> position: absolute, external script ran" );
        start();
    }, 500 );
});
