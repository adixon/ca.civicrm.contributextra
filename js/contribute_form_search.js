/*
 * backend contribute search, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */
console.log('test');
cj(function ($) {
  'use strict';
  console.log('test2');
  $('#crm-main-content-wrapper').crmSnippet().on('crmLoad', function(e, data) {
    var backofficeLinks = (typeof CRM.vars.contributextra != 'undefined') ? CRM.vars.contributextra.backofficeLinks : CRM.contributextra.backofficeLinks;
    if (0 < backofficeLinks.length) {
      $.each(backofficeLinks, function(index, value) {
         $('form#Search #help').after(' <a style="color: #F88;" class="button" href="'+value.url+'">'+value.title+'</a>');
      });
    }
  });
});
