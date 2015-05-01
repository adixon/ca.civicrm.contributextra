{literal}
<script type="text/javascript">
cj(function( ) {
  var backofficeLinks = cj('form#Search input[name=backofficeLinks]');
  if (0 < backofficeLinks.length) {
    var boLinks = cj.parseJSON(backofficeLinks.val());
    console.log(boLinks);
    backofficeLinks.remove();
    /* cj('form#Search #help').append('<div>Use these buttons to make contributions on behalf of this contact.</div>'); */
    cj.each(boLinks, function(index, value) {
      cj('form#Search .action-link').prepend(' <a style="color: #F88;" class="button" href="'+value.url+'"><span><div class="icon add-icon"></div> '+value.title+'</span></a>');
    });
    // cj('form#Search #help').append('<div class="clearfix"> </div>');
  }
});
</script>{/literal}
