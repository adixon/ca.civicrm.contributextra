# ca.civicrm.contributextra
Extra functionality for CiviContribute

1. Administration-only front-end contribution pages.
 
The backend contribution form is complicated and breaks when you have a ACH/EFT or other non-credit-card billing fields payment processor.

To solve that problem, and also provide simpler, more tailed administrative data input screens, this extension allows you to mark a contribution page as 'Admin-only'. This makes the page unavailable to the public, and auto-links it from the individual contact page contribution tab.

2. Auto-memberships
 
Memberships tied to recurring contributions have issues. Even non-recurring memberships are a bit unfriendly to manage. Most of the time, you would expect a contribution of type 'Membership Contribution' to auto-renew a membership, but it doesn't.

This extension allows you to map financial types to membership types. Every time a completed contribution of that type gets added to a contact, this extension will auto-renew the membership (or create a new one if it doesn't exist).

As an extra feature, you can configure the contribution type to switch type if an existing membership has already been paid up - allowing extra contributions to be deductible for example.
