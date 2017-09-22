/*
 * backend summary page, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */

cj(function ($) {
  'use strict';
   var backofficeLinks = (typeof CRM.vars.contributextra != 'undefined') ? CRM.vars.contributextra.backofficeLinks : CRM.contributextra.backofficeLinks;
  if (0 < backofficeLinks.length) {
    $.each(backofficeLinks, function(index, value) {
      // Only add button if it's not already added
      if( $('#actions').find('a.button.contributextra-btn[data-page-id='+value.id+']').length == 0 ){
        $('#actions').append('<li><a style="color: #F88;" class="button contributextra-btn" href="'+value.url+'" data-page-id="'+value.id+'">'+value.title+'</a></li>');
      }
    });
  }
});
