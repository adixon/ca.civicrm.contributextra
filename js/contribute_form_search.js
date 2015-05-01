/*
 * backend contribute search, individual
 *   
 */

/*jslint indent: 2 */
/*global CRM, ts */

console.log('test');

cj(function ($) {
  'use strict';
  var backofficeLinks = $('form#Search input[name=backofficeLinks]');
  if (0 < backofficeLinks.length) {
    var boLinks = $.parseJSON(backofficeLinks.val());
    backofficeLinks.remove();
    $('form#Search #help').append('<div>Use these buttons to make contributions on behalf of this contact.</div>');
    $.each(boLinks, function(index, value) {
       $('form#Search #help').append(' <a style="color: #F88;" class="button" href="'+value.url+'">'+value.title+'</a>');
    });
    $('form#Search #help').append('<div class="clearfix"> </div>');
  }
});
