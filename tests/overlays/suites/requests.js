module('Requests', {
    setup: function(){},
    teardown: function(){}
});

asyncTest('Simple [id=?] requests', function(){
    expect(2);

    var selector = '[id=main]', overlay = createOverlay({selector: selector, url: 'samples/Home.html'}, {id: 'test'});

    setTimeout(function() {
        ok(overlay.element.children(selector).length, "Overlay contains div#main element from request." );
        start();
    }, 500 );



    var selector = 'footer', overlay = createOverlay({selector: selector, url: 'samples/html5.html'}, {id: 'test2'});

    setTimeout(function() {
        ok(overlay.element.children(selector).length, "Overlay contains footer element from request." );
        start();
    }, 500 );
});
