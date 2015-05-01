<div id="is-admin-only" style="margin: 10px 20px; padding: 10px 20px; background-color: #FFF; border: 1px solid grey; border-radius: 5px;">
{$form.is_admin_only.html}
{$form.is_admin_only.label}
<div class="description"> &nbsp; {ts}When this is checked, the contribution page will be deactivated for public access and linked to from contacts' Contribution tab for easy administrative use.{/ts}</div>
</div>
{literal}
<script type="text/javascript">
  cj('#mainTabContainer').on( 'tabsload', function( event, ui ) {
    cj('.crm-submit-buttons').last().before(cj('#is-admin-only'));
  } );
</script>
{/literal}
