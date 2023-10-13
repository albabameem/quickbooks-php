# Keep your Quickbooks OAuth token alive forever.

> Note: This is a quick and simple solution just to give you an idea how this can be achieved. This repo was created within a few hours so don't except the best coding practices.

## Requirements

1. PHP 5.6 or greater
2. cURL >= 7.19.7

---

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
```

### Add dependency

```bash
composer require quickbooks/v3-php-sdk
```

### Or, add in your existing project's composer.json

```json
{
   "require": {
      "quickbooks/v3-php-sdk": ">=4.0.1"
   }
}
```

### Setup `.env` file

* Setup your developer account [here](https://developer.intuit.com/app/developer/qbo/docs/get-started/get-client-id-and-client-secret) and obtain `Client ID` and `Client Secret`
* baseURL for sandbox and testing environments: `sandbox-quickbooks.api.intuit.com`
* baseURL for production apps: `quickbooks.api.intuit.com`
* Create a sandbox company to get company ID/realmId: [Click Here](https://developer.intuit.com/app/developer/qbo/docs/develop/sandboxes/manage-your-sandboxes)
* scope for [QuickBooks Online Accounting API](https://developer.intuit.com/app/developer/qbo/docs/learn/scopes#current-scopes) : `com.intuit.quickbooks.accounting`
* scope for [QuickBooks Payments API](https://developer.intuit.com/app/developer/qbo/docs/learn/scopes#current-scopes) : `com.intuit.quickbooks.payment`

### Add the following to your .env file:

```env
clientId="client id that you got above"
clientSecret="client secret that you got above"
redirectUrl="https://yourhostlink.com/callback"
baseUrl="baseURL from above"
scope="com.intuit.quickbooks.accounting"
state="cmrs"
realmId="company id from above"
accessToken="enter anything for now"
refreshToken="enter anything for now"
```

* Add your callback URL to your company [here](https://developer.intuit.com/app/developer/qbo/docs/develop/authentication-and-authorization/set-redirect-uri)

### Add the relevant routes from web.php file or to test run, use web.php file as it is.

### Run initial setup
* Make sure to do this in order.
* Need to be done only once.
* Visit `https://yourhostlink.com/init`
* Login to connect your company app then select your company and press next.
* You should see `Success!` message if your token was successfully generated.
* Try the test `https://yourhostlink.com/customer` to see if it works.
* Visit `https://yourhostlink.com/refresh` to manually refresh your token.
* Note: Your .env file should now have accessToken and refreshToken set.

# MOST IMPORTANT

To keep the token alive, we will create a cron job that will keep hitting `https://yourhostlink.com/refresh` every 15 minutes which will then swap the old access token from your .env file to the new one. Since this happens every 15 minutes, your token will stay alive FOREVER!

* Either go to your cron job manager or run `crontab -e` in your terminal to edit the cron jobs.
* Create the following cron job by pasting this at the end of your file.
```bash
*/15 * * * * wget --quiet -O /dev/null https://yourhostlink.com/refresh
```

# You're all set!
