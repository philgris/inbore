// Navbar events and animations

import Cookies from "js-cookie";

function close_sub_menus() {
  $(".nav-left .menu-entry.active")
    .removeClass("active")
    .find('ul:first')
    .slideUp("fast")
}

$(".nav-left .menu-entry>a").click(event => {
  let target = $(event.currentTarget)
  let entry_elt = target.parent('li.menu-entry');
  
  if (target.attr("href") === "#") {
    event.preventDefault()
  }

  if (entry_elt.hasClass('active')) {
    entry_elt.removeClass('active active-sm')
    $('ul:first', entry_elt).slideUp()
  } else {
    close_sub_menus()
    entry_elt.addClass("active")
    $('ul:first', entry_elt).slideDown("fast")
  }
})


$('#menu-toggle').click(event => {
  $('.nav-left').toggleClass("nav-sm")
  if ($('.nav-left').hasClass("nav-sm"))
    Cookies.set("inbore-menu-layout", "small",  { sameSite: 'lax' })
  else
    Cookies.set("inbore-menu-layout", undefined,  { sameSite: 'lax' })
})

// update main menu state
$(document).ready(function () {
    let url = window.location.toString();
    $(".nav-left .menu-entry a.nav-link").each(function() {
        let $link = $(this);
        if (url.includes($link.attr('href'))) {
            close_sub_menus();
            let $parent = $link.parents('li.menu-entry');
            $parent.addClass('current-page', 'active');
            $link.parents('li.menu-sub-entry').addClass('current-page');
            if (!$(".nav-left").hasClass('nav-sm')) {
                $('ul:first', $parent).show();
            }
        }
    })
});
