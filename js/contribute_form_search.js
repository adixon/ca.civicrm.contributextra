/*
 * backend contribute search, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */

cj(function ($) {
  'use strict';

  $('#crm-main-content-wrapper').crmSnippet().on('crmLoad', function(e, data) {
    var backofficeLinks = (typeof CRM.vars.contributextra != 'undefined') ? CRM.vars.contributextra.backofficeLinks : CRM.contributextra.backofficeLinks;
    if (0 < backofficeLinks.length) {
      $.each(backofficeLinks, function(index, value) {
        // Only add button if it's not already added
        var siblings = $('form#Search').find('#help, .help').siblings('a.button.contributextra-btn');
        if( siblings.length == 0 ){
          $('form#Search').find('#help, .help').after(' <a style="color: #F88;" class="button contributextra-btn" href="'+value.url+'" data-page-id="'+value.id+'">'+value.title+'</a>');
        }
      });
    }
  });
});
