<div id="membership-implicit" style="margin: 10px 20px; padding: 10px 20px; background-color: #FFF; border: 1px solid grey; border-radius: 5px;">
<div class="membeship-implicit">{$form.membership_implicit.label}
<br />
{$form.membership_implicit.html}
<div class="description"> &nbsp; {ts}When selected, contributions of this type will automatically create or renew the specified membership type.{/ts}</div>
</div>
<div class="membership-financial-type-id">
{$form.membership_financial_type_id.label}
<br />
{$form.membership_financial_type_id.html}
<div class="description"> &nbsp; {ts}When selected, the portion of contributions that are used for membership creation/renewal will change to this type.{/ts}</div>
</div>
</div>
{literal}
<script type="text/javascript">
  cj('.form-layout').last().after(cj('#membership-implicit'));
</script>
{/literal}
