module('Requests', {
    setup: function(){},
    teardown: function(){}
});

test('Simple requests', function(){
    var overlay = createOverlay({selector: '[id=main]', url: 'samples/Home.html'}, {id: 'test'});
    
    ok(1, 'OK');
});
