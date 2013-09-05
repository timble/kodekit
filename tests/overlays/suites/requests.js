module('Requests', {
    setup: function(){},
    teardown: function(){}
});

asyncTest('Simple selector requests', function(){
    expect(2);
    stop();

    var selector1 = '[id=main]', overlay1 = createOverlay({selector: selector1, url: 'samples/Home.html'}, {id: 'test1'});

    setTimeout(function() {
        ok(overlay1.element.children(selector1).length, "Overlay contains div#main element from request." );
        start();
    }, 500 );

    var selector2 = 'footer', overlay2 = createOverlay({selector: selector2, url: 'samples/html5.html'}, {id: 'test2'});

    setTimeout(function() {
        ok(overlay2.element.children(selector2).length, "Overlay contains footer element from request." );
        start();
    }, 500 );
});

asyncTest('evalStyles: false, evalScripts: false', function(){
    expect(4);

    var selector = '#section', overlay = createOverlay({selector: selector, url: 'samples/html5.html', evalStyles: false, evalScripts: false}, {id: 'test3'});
    setTimeout(function() {
        notEqual(overlay.element.find('article').css('position'), 'absolute', "evalStyles: false, <article> position: static, inline style tag not applied" );
        notEqual(overlay.element.find('footer').css('position'), 'absolute', "evalStyles: false, <footer> position: static, external stylesheet not applied" );
        notEqual(overlay.element.find('#section header').css('position'), 'absolute', "evalScripts: false, #section <header> position: static, inline script tag did not run" );
        notEqual(overlay.element.find('#section h2').css('position'), 'absolute', "evalScripts: false, #section <h2> position: static, external script did not run" );
        start();
    }, 500 );
});

asyncTest('evalStyles: true, evalScripts: true', function(){
    expect(4);

    var selector = '#section2';

    var overlay = createOverlay({selector: selector, url: 'samples/html5alt.html', evalStyles: true, evalScripts: true}, {id: 'test4'});
    setTimeout(function() {
        equal(overlay.element.find('article > p').css('position'), 'absolute', "evalStyles: true, <article> > <p> position: absolute, inline style tag applied" );
        equal(overlay.element.find('header > p').css('position'), 'absolute', "evalStyles: true, <header> > <p> position: absolute, external stylesheet applied" );
        equal(overlay.element.find('#section time').css('position'), 'absolute', "evalScripts: true, #section <time> position: absolute, inline script tag ran" );
        equal(overlay.element.find('#section a').css('position'), 'absolute', "evalScripts: true, #section <a> position: absolute, external script ran" );
        start();
    }, 500 );
});
