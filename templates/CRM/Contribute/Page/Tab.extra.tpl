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
});
</script>{/literal}
