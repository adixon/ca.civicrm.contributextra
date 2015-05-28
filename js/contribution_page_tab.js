/*
 * backend contribute search, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */

cj(function ($) {
  'use strict';
  $('#crm-main-content-wrapper .CRM_Contribute_Form_ContributionPage_Settings').crmSnippet().on('crmLoad', function(e, data) {
    if (0 == $('#is-admin-only').length) {
      var isAdminHtml = (typeof CRM.vars.contributextra != 'undefined') ? CRM.vars.contributextra.is_admin_html : CRM.contributextra.is_admin_html;
      $('.crm-submit-buttons').last().before(isAdminHtml);
    }
  });
});
