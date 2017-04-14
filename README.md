# ThinkShout Development Challenge

A client has asked for a simple donation form on their Drupal 8 site. The client already uses Stripe for processing payments at their events and would like to use it for the donation form as well. The client needs to collect who is making the donation, how much it’s for and of course collect the credit card payment.

## Guidelines:
   
- Use GitHub, Bitbucket or similar to host your work.
- In a text document in the project or in issues in the project outline the specific requirements needed to implement the above request.
 - Use a simple Drupal 8 site as a starting point. Your site with the donation form should work with no more than two installation steps: 1) installing drupal 2) enabling one custom module containing your configuration and custom code.
 - You are encouraged to include your thoughts about why you took the approach you did, what was easy or hard, etc.

## Extra credit:
   
- Allow the user to select from a list of donation amounts or enter a custom value.
- Track the status of the transaction from Stripe in Drupal with a record of the donation.
- Use AJAX to validate the form inputs.

## Outline

- create drupal module (this repo)
- setup donation form
  - integrate with stripe checkout so token is submitted with form
    - easiest integration is stripe checkout
- add necessary strip credentials as setting requirement of module
  - or maybe import a stripe module to do this?
- submit form processes payment via. stripe integration
  - stored in database
    - stripe api results
    - who is making the donation
    - amount donated
- user is directed to thank you page or error page
  - if error page, user is given change to try again with same token?
  - thank you page is static

## My Thoughts

- took a long time to realize that I could render a script tag with type html_tag
- JS is very plain, but ideally I would have liked to use Vue.js