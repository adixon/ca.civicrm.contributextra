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
       $('#actions').append('<li><a style="color: #F88;" class="button" href="'+value.url+'">'+value.title+'</a></li>');
    });
  }
});
