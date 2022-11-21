const FormTools = {

    // init: function () {
    // },

    /**
     * Clear all options of $selector
     * @param $selector (wrapped jquery select widget)
     */
    clear : function($selector) {
        let $placeholder = $selector.find('option[value=""]');
        $selector
            .find('option')
            .remove()
            .end()
        ;
        // re-append placeholder (with empty value)
        if ($placeholder.length===1) {
            $selector.append($placeholder);
        }
    },

    /**
     * Action when field changed :
     *      <select id="field" onchange="FormTools.change(this);">...</select>
     * Injected with the formType :
     *      ->add('<field>', EntityType::class, [
     *          ...
     *          'attr' => [
     *              'onchange'  => 'FormTools.change(this);',
     *              'data-change-url' => $this->urlGenerator->generate('<entity>_form', [], $this->urlGenerator::ABSOLUTE_URL)
     *          ]
     *      ])
     *
     * See example in behavior study > team day
     *
     * @param field
     */
    change : function(field) {
        let $field = $(field);
        // url passed in formType 'data-change-url'
        if (!$field.data('change-url')) {
            return;
        }
        $.get (
            $field.data('change-url'),
            {
                [$field.attr('id')] : $field.val()
            }
        )
            .done(
                // get json :
                // [
                //      field1 : [ {id:label}, ... , {id:label} ],
                //      ...
                //      fieldN : [ {id:label}, ... , {id:label} ]
                // ]
                function(response){
                    // for each field<i> refresh the associated select widget with the records received
                    $.each(Object.keys(response), function(index, field) {
                        let $selector = $('#'+field);
                        if ($selector.length===1) {
                            // clear all options
                            FormTools.clear($selector);
                            // append records
                            $.each(response[field], function(value, text) {
                                $selector.append(new Option(text, value));
                            });
                            // if only one record select it directly
                            $selector.val(
                                Object.keys(response[field]).length===1
                                    ? Object.keys(response[field])[0]
                                    : ''
                            );
                            $selector.selectpicker("refresh");
                        }
                    });
                }
            )
            .fail(
                function(response){
                    alert(response.responseText);
                }
            )
        ;
    }

};

export { FormTools };
window.FormTools = FormTools;