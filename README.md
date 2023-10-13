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

# Add the following to your .env file:

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

### Add your callback URL to your company [here](https://developer.intuit.com/app/developer/qbo/docs/develop/authentication-and-authorization/set-redirect-uri)

### Replace your web.php with routes with your application routes.
