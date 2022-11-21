// Import for the auto complete field ( see SearchableSelectType )
import { initSearchSelect } from "./field-suggestions"
import { getSelectedCode } from "./forms"
// NOTE ! this file has to be load in the /templates/entity-name/edit.html.twig and the webpack.config.js files

$(() => {
  // -- Initialization of the auto complete fields and load Ajax request for the route "linkedEntity_search" (the request)
  // initSearchSelect($("#entity-name_linkedEntityNameFk")), "linkedEntity_search")
  // -- End Initialization of  auto complete fields

  // Js Action on the field form 
  
      let EntityName = {

        init : function() {
            // ex. $( document ).on( 'change', '#entity-name_Field', EntityName.refresh );
            EntityName.refresh();
        },

        refresh : function() {

            // Js Action to do when a refresh is call 

        }

    };

    EntityName.init();

})