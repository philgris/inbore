import { initSearchSelect } from "./field-suggestions"
import { getSelectedCode } from "./forms"

$(() => {
  const $form = $("form[name='contact']")
  const $adress = $form.find("#contact_adressFk")

  initSearchSelect($adress, "adress_search")

})