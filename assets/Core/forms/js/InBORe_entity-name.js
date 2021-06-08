// Import for the auto complete field ( see SearchableSelectType )
import { initSearchSelect } from "./field-suggestions"
import { getSelectedCode } from "./forms"
// NOTE ! this file has to be load in the /templates/entity-name/edit.html.twig and the webpack.config.js files

$(() => {
  // Initialize the auto complete field and load Ajax request for the route "entity-name_search" (the request)
  const $form = $("form[name='entity-name']")
  const $adress = $form.find("#entity-name_linked-entity-nameFk")
  initSearchSelect($adress, "entity-name_search")
  //

})