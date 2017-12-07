/*!
 * modernizr v3.3.1
 * Build http://modernizr.com/download?-appearance-boxshadow-checked-cssanimations-eventlistener-flexbox-flexboxlegacy-flexboxtweener-flexwrap-touchevents-prefixedcss-setclasses-shiv-dontmin-cssclassprefix:k-
 *
 * Copyright (c)
 *  Faruk Ates
 *  Paul Irish
 *  Alex Sexton
 *  Ryan Seddon
 *  Patrick Kettner
 *  Stu Cox
 *  Richard Herrera

 * MIT License
 */
!function(a,b,c){function d(a,b){return typeof a===b}function e(){var a,b,c,e,f,g,h;for(var i in s)if(s.hasOwnProperty(i)){if(a=[],b=s[i],b.name&&(a.push(b.name.toLowerCase()),b.options&&b.options.aliases&&b.options.aliases.length))for(c=0;c<b.options.aliases.length;c++)a.push(b.options.aliases[c].toLowerCase());for(e=d(b.fn,"function")?b.fn():b.fn,f=0;f<a.length;f++)g=a[f],h=g.split("."),1===h.length?u[h[0]]=e:(!u[h[0]]||u[h[0]]instanceof Boolean||(u[h[0]]=new Boolean(u[h[0]])),u[h[0]][h[1]]=e),v.push((e?"":"no-")+h.join("-"))}}function f(a){var b=w.className,c=u._config.classPrefix||"";if(x&&(b=b.baseVal),u._config.enableJSClass){var d=new RegExp("(^|\\s)"+c+"no-js(\\s|$)");b=b.replace(d,"$1"+c+"js$2")}u._config.enableClasses&&(b+=" "+c+a.join(" "+c),x?w.className.baseVal=b:w.className=b)}function g(a,b){return!!~(""+a).indexOf(b)}function h(){return"function"!=typeof b.createElement?b.createElement(arguments[0]):x?b.createElementNS.call(b,"http://www.w3.org/2000/svg",arguments[0]):b.createElement.apply(b,arguments)}function i(){var a=b.body;return a||(a=h(x?"svg":"body"),a.fake=!0),a}function j(a,c,d,e){var f,g,j,k,l="modernizr",m=h("div"),n=i();if(parseInt(d,10))for(;d--;)j=h("div"),j.id=e?e[d]:l+(d+1),m.appendChild(j);return f=h("style"),f.type="text/css",f.id="s"+l,(n.fake?n:m).appendChild(f),n.appendChild(m),f.styleSheet?f.styleSheet.cssText=a:f.appendChild(b.createTextNode(a)),m.id=l,n.fake&&(n.style.background="",n.style.overflow="hidden",k=w.style.overflow,w.style.overflow="hidden",w.appendChild(n)),g=c(m,a),n.fake?(n.parentNode.removeChild(n),w.style.overflow=k,w.offsetHeight):m.parentNode.removeChild(m),!!g}function k(a){return a.replace(/([A-Z])/g,function(a,b){return"-"+b.toLowerCase()}).replace(/^ms-/,"-ms-")}function l(b,d){var e=b.length;if("CSS"in a&&"supports"in a.CSS){for(;e--;)if(a.CSS.supports(k(b[e]),d))return!0;return!1}if("CSSSupportsRule"in a){for(var f=[];e--;)f.push("("+k(b[e])+":"+d+")");return f=f.join(" or "),j("@supports ("+f+") { #modernizr { position: absolute; } }",function(a){return"absolute"==getComputedStyle(a,null).position})}return c}function m(a){return a.replace(/([a-z])-([a-z])/g,function(a,b,c){return b+c.toUpperCase()}).replace(/^-/,"")}function n(a,b,e,f){function i(){k&&(delete B.style,delete B.modElem)}if(f=!d(f,"undefined")&&f,!d(e,"undefined")){var j=l(a,e);if(!d(j,"undefined"))return j}for(var k,n,o,p,q,r=["modernizr","tspan"];!B.style;)k=!0,B.modElem=h(r.shift()),B.style=B.modElem.style;for(o=a.length,n=0;n<o;n++)if(p=a[n],q=B.style[p],g(p,"-")&&(p=m(p)),B.style[p]!==c){if(f||d(e,"undefined"))return i(),"pfx"!=b||p;try{B.style[p]=e}catch(a){}if(B.style[p]!=q)return i(),"pfx"!=b||p}return i(),!1}function o(a,b){return function(){return a.apply(b,arguments)}}function p(a,b,c){var e;for(var f in a)if(a[f]in b)return c===!1?a[f]:(e=b[a[f]],d(e,"function")?o(e,c||b):e);return!1}function q(a,b,c,e,f){var g=a.charAt(0).toUpperCase()+a.slice(1),h=(a+" "+z.join(g+" ")+g).split(" ");return d(b,"string")||d(b,"undefined")?n(h,b,e,f):(h=(a+" "+C.join(g+" ")+g).split(" "),p(h,b,c))}function r(a,b,d){return q(a,c,c,b,d)}var s=[],t={_version:"3.3.1",_config:{classPrefix:"k-",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(a,b){var c=this;setTimeout(function(){b(c[a])},0)},addTest:function(a,b,c){s.push({name:a,fn:b,options:c})},addAsyncTest:function(a){s.push({name:null,fn:a})}},u=function(){};u.prototype=t,u=new u;var v=[],w=b.documentElement,x="svg"===w.nodeName.toLowerCase();x||!function(a,b){function c(a,b){var c=a.createElement("p"),d=a.getElementsByTagName("head")[0]||a.documentElement;return c.innerHTML="x<style>"+b+"</style>",d.insertBefore(c.lastChild,d.firstChild)}function d(){var a=t.elements;return"string"==typeof a?a.split(" "):a}function e(a,b){var c=t.elements;"string"!=typeof c&&(c=c.join(" ")),"string"!=typeof a&&(a=a.join(" ")),t.elements=c+" "+a,j(b)}function f(a){var b=s[a[q]];return b||(b={},r++,a[q]=r,s[r]=b),b}function g(a,c,d){if(c||(c=b),l)return c.createElement(a);d||(d=f(c));var e;return e=d.cache[a]?d.cache[a].cloneNode():p.test(a)?(d.cache[a]=d.createElem(a)).cloneNode():d.createElem(a),!e.canHaveChildren||o.test(a)||e.tagUrn?e:d.frag.appendChild(e)}function h(a,c){if(a||(a=b),l)return a.createDocumentFragment();c=c||f(a);for(var e=c.frag.cloneNode(),g=0,h=d(),i=h.length;g<i;g++)e.createElement(h[g]);return e}function i(a,b){b.cache||(b.cache={},b.createElem=a.createElement,b.createFrag=a.createDocumentFragment,b.frag=b.createFrag()),a.createElement=function(c){return t.shivMethods?g(c,a,b):b.createElem(c)},a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+d().join().replace(/[\w\-:]+/g,function(a){return b.createElem(a),b.frag.createElement(a),'c("'+a+'")'})+");return n}")(t,b.frag)}function j(a){a||(a=b);var d=f(a);return!t.shivCSS||k||d.hasCSS||(d.hasCSS=!!c(a,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),l||i(a,d),a}var k,l,m="3.7.3",n=a.html5||{},o=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,p=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,q="_html5shiv",r=0,s={};!function(){try{var a=b.createElement("a");a.innerHTML="<xyz></xyz>",k="hidden"in a,l=1==a.childNodes.length||function(){b.createElement("a");var a=b.createDocumentFragment();return"undefined"==typeof a.cloneNode||"undefined"==typeof a.createDocumentFragment||"undefined"==typeof a.createElement}()}catch(a){k=!0,l=!0}}();var t={elements:n.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video",version:m,shivCSS:n.shivCSS!==!1,supportsUnknownElements:l,shivMethods:n.shivMethods!==!1,type:"default",shivDocument:j,createElement:g,createDocumentFragment:h,addElements:e};a.html5=t,j(b),"object"==typeof module&&module.exports&&(module.exports=t)}("undefined"!=typeof a?a:this,b);var y="Moz O ms Webkit",z=t._config.usePrefixes?y.split(" "):[];t._cssomPrefixes=z;var A={elem:h("modernizr")};u._q.push(function(){delete A.elem});var B={style:A.elem.style};u._q.unshift(function(){delete B.style});var C=t._config.usePrefixes?y.toLowerCase().split(" "):[];t._domPrefixes=C,t.testAllProps=q;var D=function(b){var d,e=G.length,f=a.CSSRule;if("undefined"==typeof f)return c;if(!b)return!1;if(b=b.replace(/^@/,""),d=b.replace(/-/g,"_").toUpperCase()+"_RULE",d in f)return"@"+b;for(var g=0;g<e;g++){var h=G[g],i=h.toUpperCase()+"_"+d;if(i in f)return"@-"+h.toLowerCase()+"-"+b}return!1};t.atRule=D;var E=t.prefixed=function(a,b,c){return 0===a.indexOf("@")?D(a):(a.indexOf("-")!=-1&&(a=m(a)),b?q(a,b,c):q(a,"pfx"))};t.prefixedCSS=function(a){var b=E(a);return b&&k(b)};/*!
{
  "name": "Event Listener",
  "property": "eventlistener",
  "authors": ["Andrew Betts (@triblondon)"],
  "notes": [{
    "name": "W3C Spec",
    "href": "https://www.w3.org/TR/DOM-Level-2-Events/events.html#Events-Registration-interfaces"
  }],
  "polyfills": ["eventlistener"]
}
!*/
u.addTest("eventlistener","addEventListener"in a),t.testAllProps=r,/*!
{
  "name": "Appearance",
  "property": "appearance",
  "caniuse": "css-appearance",
  "tags": ["css"],
  "notes": [{
    "name": "MDN documentation",
    "href": "https://developer.mozilla.org/en-US/docs/Web/CSS/-moz-appearance"
  },{
    "name": "CSS-Tricks CSS Almanac: appearance",
    "href": "https://css-tricks.com/almanac/properties/a/appearance/"
  }]
}
!*/
u.addTest("appearance",r("appearance")),/*!
{
  "name": "Box Shadow",
  "property": "boxshadow",
  "caniuse": "css-boxshadow",
  "tags": ["css"],
  "knownBugs": [
    "WebOS false positives on this test.",
    "The Kindle Silk browser false positives"
  ]
}
!*/
u.addTest("boxshadow",r("boxShadow","1px 1px",!0));var F=t.testStyles=j;/*!
{
  "name": "CSS :checked pseudo-selector",
  "caniuse": "css-sel3",
  "property": "checked",
  "tags": ["css"],
  "notes": [{
    "name": "Related Github Issue",
    "href": "https://github.com/Modernizr/Modernizr/pull/879"
  }]
}
!*/
u.addTest("checked",function(){return F("#modernizr {position:absolute} #modernizr input {margin-left:10px} #modernizr :checked {margin-left:20px;display:block}",function(a){var b=h("input");return b.setAttribute("type","checkbox"),b.setAttribute("checked","checked"),a.appendChild(b),20===b.offsetLeft})}),/*!
{
  "name": "CSS Animations",
  "property": "cssanimations",
  "caniuse": "css-animation",
  "polyfills": ["transformie", "csssandpaper"],
  "tags": ["css"],
  "warnings": ["Android < 4 will pass this test, but can only animate a single property at a time"],
  "notes": [{
    "name" : "Article: 'Dispelling the Android CSS animation myths'",
    "href": "https://goo.gl/OGw5Gm"
  }]
}
!*/
u.addTest("cssanimations",r("animationName","a",!0)),/*!
{
  "name": "Flexbox",
  "property": "flexbox",
  "caniuse": "flexbox",
  "tags": ["css"],
  "notes": [{
    "name": "The _new_ flexbox",
    "href": "http://dev.w3.org/csswg/css3-flexbox"
  }],
  "warnings": [
    "A `true` result for this detect does not imply that the `flex-wrap` property is supported; see the `flexwrap` detect."
  ]
}
!*/
u.addTest("flexbox",r("flexBasis","1px",!0)),/*!
{
  "name": "Flexbox (legacy)",
  "property": "flexboxlegacy",
  "tags": ["css"],
  "polyfills": ["flexie"],
  "notes": [{
    "name": "The _old_ flexbox",
    "href": "https://www.w3.org/TR/2009/WD-css3-flexbox-20090723/"
  }]
}
!*/
u.addTest("flexboxlegacy",r("boxDirection","reverse",!0)),/*!
{
  "name": "Flexbox (tweener)",
  "property": "flexboxtweener",
  "tags": ["css"],
  "polyfills": ["flexie"],
  "notes": [{
    "name": "The _inbetween_ flexbox",
    "href": "https://www.w3.org/TR/2011/WD-css3-flexbox-20111129/"
  }],
  "warnings": ["This represents an old syntax, not the latest standard syntax."]
}
!*/
u.addTest("flexboxtweener",r("flexAlign","end",!0)),/*!
{
  "name": "Flex Line Wrapping",
  "property": "flexwrap",
  "tags": ["css", "flexbox"],
  "notes": [{
    "name": "W3C Flexible Box Layout spec",
    "href": "http://dev.w3.org/csswg/css3-flexbox"
  }],
  "warnings": [
    "Does not imply a modern implementation – see documentation."
  ]
}
!*/
u.addTest("flexwrap",r("flexWrap","wrap",!0));var G=t._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):[];t._prefixes=G,/*!
{
  "name": "Touch Events",
  "property": "touchevents",
  "caniuse" : "touch",
  "tags": ["media", "attribute"],
  "notes": [{
    "name": "Touch Events spec",
    "href": "https://www.w3.org/TR/2013/WD-touch-events-20130124/"
  }],
  "warnings": [
    "Indicates if the browser supports the Touch Events spec, and does not necessarily reflect a touchscreen device"
  ],
  "knownBugs": [
    "False-positive on some configurations of Nokia N900",
    "False-positive on some BlackBerry 6.0 builds – https://github.com/Modernizr/Modernizr/issues/372#issuecomment-3112695"
  ]
}
!*/
u.addTest("touchevents",function(){var c;if("ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch)c=!0;else{var d=["@media (",G.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");F(d,function(a){c=9===a.offsetTop})}return c}),e(),f(v),delete t.addTest,delete t.addAsyncTest;for(var H=0;H<u._q.length;H++)u._q[H]();a.Modernizr=u}(window,document);