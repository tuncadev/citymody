/**
 * Define class field
 */
var GLF_SorterClass = function($container) {
	this.$container = $container;
};

(function($) {
	"use strict";

	/**
	 * Define class field prototype
	 */
	GLF_SorterClass.prototype = {
		init: function() {
            var self = this,
                $items = this.$container.find('.glf-field-sorter-group');
            $items.sortable({
                placeholder: 'glf-sorter-sortable-placeholder',
                items: '.glf-field-sorter-item',
                connectWith: $('.glf-field-sorter-group', this.$container),
                update: function (event, ui) {
                    var $group = $(event.target),
                        groupName = $group.data('group');
                    /**
                     * Update input name
                     *
                     */
                    $group.find('[data-field-control]').each(function () {
                        var $this = $(this),
                            name = $this.attr('name');
                        name = name.replace(/^(.*)(\[)([^\]]*)(\])(\[)([^\]]*)(\])*$/g,function(m,p1,p2,p3,p4,p5,p6,p7) {return p1 + p2 + groupName + p4 + p5 + p6 + p7;});;
                        $this.prop('name', name);
                    });

                    var $field = $group.closest('.glf-field'),
                        value = GLFFieldsConfig.fields.getValue($field);
                    GLFFieldsConfig.required.checkRequired($field, value);
                }
            });
		},
	};

    var GLF_SorterObject = {
        init: function() {
            /**
             * Init Fields after make clone template
             */
            var $configWrapper = $('.glf-meta-config-wrapper');
            $configWrapper = $configWrapper.length ? $configWrapper : $('body');

            $configWrapper.on('glf_make_template_done', function() {
                $('.glf-field-sorter-inner').each(function () {
                    var field = new GLF_SorterClass($(this));
                    field.init();
                });
            });

            /**
			 *
             * Init Clone Field after field cloned
             */
            $('.glf-field.glf-field-sorter').on('glf_add_clone_field', function(event){
                var $items = $(event.target).find('.glf-field-sorter-inner');
                if ($items.length) {
                    var field = new GLF_SorterClass($items);
                    field.init();
                }
            });
        }
    };

    /**
	 *
     * Init Field when document ready
     */
    $(document).ready(function() {
        GLF_SorterObject.init();
        GLFFieldsConfig.fieldInstance.push(GLF_SorterObject);
    });
})(jQuery);