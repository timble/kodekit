var kodekitUI = typeof kodekitUI !== 'undefined' ? kodekitUI : {}; // global variable

// Create cookie
kodekitUI.createCookie = function(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name+"="+value+expires+"; path=/";
};

// Read cookie
kodekitUI.readCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return null;
};

// Erase cookie
kodekitUI.eraseCookie = function(name) {
    kodekitUI.createCookie(name,"",-1);
};

// Set CSS
kodekitUI.setCSS = function(css) {
    // Get style element
    var style = document.querySelector('[data-type]', 'kodekitStyles');

    // Add CSS to style element
    if (style.styleSheet){
        style.styleSheet.cssText += css;
    } else {
        style.innerHTML += css;
    }
};


// Run initialize code
(function () {

    // Add js-enabled class to html element
    var el = document.documentElement;
    var cl = "k-js-enabled";
    if (el.classList) {
        el.classList.add(cl);
    } else {
        el.className += " " + cl;
    }

    // Set width of list area
    var head = document.head || document.getElementsByTagName('head')[0];
    var style = document.createElement('style');
    var middlepaneWidthCookieValue = kodekitUI.readCookie("kodekitUI.middlepanewidth");
    var galleryWidthCookieValue = kodekitUI.readCookie("kodekitUI.gallerywidth");

    // Add style element
    style.type = 'text/css';
    style.setAttribute('data-type', 'kodekitStyles');
    head.appendChild(style);

    // If a cookie is set for middlepane
    if (middlepaneWidthCookieValue !== null) {
        kodekitUI.setCSS(
            '@media screen and (min-width: 1024px) {' +
            '.k-ui-container .k-content-area .k-content:not(:last-child) {' +
            'min-width:'+middlepaneWidthCookieValue+'px;' +
            'width:'+middlepaneWidthCookieValue+'px;' +
            'max-width:'+middlepaneWidthCookieValue+'px;' +
            '}' +
            '}'
        );
    }

})();
