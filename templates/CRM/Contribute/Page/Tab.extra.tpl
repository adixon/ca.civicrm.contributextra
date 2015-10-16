{literal}
<script type="text/javascript">
/*
 * backend contribute search, individual
 *   
 */
/*jslint indent: 2 */
/*global CRM, ts */

cj(function ($) {
  'use strict';
  var boLinks = $('form#Search input[name=extra_backoffice_links]');
  if (0 < boLinks.length) {
    var backofficeLinks = cj.parseJSON(boLinks.val());
    $.each(backofficeLinks, function(index, value) {
       $('form#Search #help').after(' <a style="color: #F88;" class="button" href="'+value.url+'">'+value.title+'</a>');
    });
  }
  var recurVars = ('contributionrecur' in CRM) ? CRM.contributionrecur : (('vars' in CRM) ? CRM.vars.contributionrecur : null);
  if ('undefined' !== typeof recurVars.recur_edit_url) {
    $("table td:contains('Cancelled')").each(function() {
      if ($(this).html() == 'Cancelled') {
        var myId = $(this).parent()[0].id;
        if ('row_' == myId.substring(0,4)) {
          myId = myId.substr(4);
          $(this).append(' | <a href="'+decodeURI(recurVars.recur_edit_url) + myId + '">Edit</a>');
          console.log(decodeURI(recurVars.recur_edit_url) + myId);
        }
      }
    });
  }
});
</script>{/literal}
