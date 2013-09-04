module('Requests', {
    setup: function(){},
    teardown: function(){}
});

asyncTest('Simple [id=?] requests', function(){
    expect(1);

    var selector = '[id=main]', overlay = createOverlay({selector: selector, url: 'samples/Home.html'}, {id: 'test'});

    setTimeout(function() {
        ok(overlay.element.children(selector).length, "Overlay contains div#main element from request." );
        start();
    }, 500 );
});
