/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/*
---

description: Monkey patching the Form.Validator to alter its behavior and extend it into doing more

requires:
 - MooTools More

license: @TODO

...
*/

if(!Koowa) var Koowa = {};

(function($){
    
    Koowa.Validator = new Class({
    
        Extends: Form.Validator.Inline,
        
        options: {
            onShowAdvice: function(input, advice) {
                advice.addEvent('click', function(){
                    input.focus();
                });
            }
        }
    
    });

})(document.id);
