/*
 ---

 description: Enables Select2 to work with MooTools Forms validation

 authors:
 - Stian Didriksen

 requires:
 - MooTools.More/Form.Validator

 license: @TODO

 ...
 */

(function($){
    if(this.Form && Form.Validator) {
        Form.Validator.add('select2-container', {
            errorMsg: function(){
                return Form.Validator.getMsg('required');
            },
            test: function(element){
                var select = element.getParent().getElement('select');

                if (select.hasClass('required')) {
                    var value = jQuery(select).select2('val');

                    return value && value != 0;
                } else {
                	return true;
                }
            }
        });
    }
})(document.id);