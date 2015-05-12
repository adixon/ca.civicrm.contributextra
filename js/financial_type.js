/*
 * backend contribute search, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */
cj(function ($) {
  'use strict';
  if (0 == $('#membership-implicit').length) {
    var myHtml = (typeof CRM.vars.contributextra != 'undefined') ? CRM.vars.contributextra.membership_implicit_html : CRM.contributextra.membership_implicit_html;
    $('table.form-layout').last().after(myHtml);
  }
});
