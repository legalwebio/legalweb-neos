# LegalWeb.GdprTools

With this plugin, you can use the [LegalWeb Cloud](https://legalweb.io) service with your [Neos CMS](https://neos.io)
installation. It connects to the legalweb.io web API and fetches your legal texts, terms & conditions and integrations
to insert it in your Neos Page.

| Version | Neos      | Maintained |
| ------- | --------- | :--------: |
| 1.\*    | 4.3 - 5.3 |     ✗      |
| 2.\*    | 7.3 - 7.3 |     ✓      |

## All in one solution

### As a user of legalweb cloud you have two simple tasks

- Selection of the services used
- Filling of a few input fields

## Legalweb cloud does everything else

- Creation of the cookie popup
- Creation of the cookie notice
- Control of services
- Control of embeddings
- Generation of the data protection information
- Creation of the imprint

## Top priority

Our top priority is compliance with the laws and the regulations of courts and of data protection authorities.
We do not offer options that are not legally or legally highly controversial.

## Lawyer created

All legal texts were created by the law firm [Marketingrecht.eu](https://marketingrecht.eu/), which specializes in IT,
internet and data protection law.

## Features

- Provides a public endpoint that can be called by legalweb.io to trigger an update.
  - The URL for the endpoint is `legalweb-gdprtools-update`. A GET parameter `token` is required and must be set to the configured `callbackToken`.
- Provides a backend module to allow users to see when the last update occurred and to manually trigger an update.
  - Messages sent by legalweb.io are also shown in the backend module.
- Provides a CLI command to trigger an update if the last update happened more than one week ago.
  - The CLI command is `./flow legalweb:update`.
    This will only fetch data from the API if the latest retrieved dataset is older than one week.
    To fetch a new dataset no matter how old the latest local dataset is, use `./flow legalweb:update --force`
- optimized for Germany & Austria
- can also be used in other EU countries and third countries
- Tag Manager compatible
- Operability with keyboard
- Accessible popup according to WCAG 2.1 AA
- 4 display types for the cookie selection popup/window (popup, as sidebar, as bottom bar, minimalist-centered popup)
- Time of display can be selected: when loading the page, by timeout, when scrolling for the first time, user-defined event on the document, manually (it must be displayed by method)
- Configurable colors, custom CSS, custom Logo, …
- JS client side API

## Imprint

- Automatic creation
- Integration via content Node Type

## Cookie popup / cookie notice / privacy info

- Our feature list gets longer and longer every month.
- Opt-in / consent management
- Opt-out / cancellation management
- definable validity of consent
- definable waiting time until the new request for consent
- correct grouping of services
- Display of all mandatory information
- No illegal nudging (obtaining consent through psychotricks)

## Privacy policy

- Automatic creation accoring to your settings
- Integration via page selection or shortcode

## Support & FAQ

We are here for you!

- Free webinars accordint to our schedule
- [legalweb.io/support/](legalweb.io/support/)

## Team

Our team is small but nice! Lawyer, programmer, marketing – everything is there 🙂

> This plugin only supports you in fulfilling the guidelines for compliance with the GDPR. Installation is not enough – correct configuration of this plugin by a website administrator is required to achieve conformity. A 100% conformity only through the plugin cannot be guaranteed, as this depends on several other aspects.

## API Documentation

[LegalWeb API](https://legalweb.io/support/dokumentation-dsgvo-api/)

## Installation

```bash
composer require legalwebio/legalweb-neos
```

This puts the dependency inside the outer composer.json/lock (usually in your repo root or `/app`). Important: In case,
you want to declare CookiePunch settings *inside your Flow packages* you need to add the composer dependency to your
composer.json inside the package as well to ensure a correct flow package and configuration loading order.

DistributionPackages/Your.SitePackage/package.json

```json
    "require": {
        "legalwebio/legalweb-neos": "^2.0"
    }
```

And run the migrations:

```bash
./flow doctrine:migrate
```

## Configuration

```yaml
LegalWeb:
  GdprTools:
    # If there are multiple sites, use the site root node name as the key instead of `default`
    default:
      # Set `apiUrl` to the endpoint provided by legal web in their API documentation.
      # For example: https://legalweb.io/api
      apiUrl: ''
      # Set `apiKey` to the GUID provided by legal web.
      # For example: d2ed9078-1ddd-426f-88d4-46a7cded8c88
      apiKey: ''
      # Set `callbackUrl` to the URL that legal web should call to trigger a dataset update.
      # Use `{token}` as a placeholder for the configured `callbackToken`.
      # For example: https://www.example.com/legalweb-gdprtools-update?token={token}
      callbackUrl: ''
      # Set `callbackToken` to a randomly generated URL-safe string.
      # For example: 50.cp5q8nxZW_0YFGkTt1QfU3R~USkyBqwKasCjZrB-wbENoxbeFuirCJTRGuoC
      callbackToken: ''
      # Set `services` to an array of strings, each item corresponding to a key that is expected in the
      # `services` section of the API response.
      # Make sure to only configure services here which are also enabled in the legal web dashboard for this project.
      # The available services are:
      # * imprint ("Impressum")
      # * contractterms ("AGB")
      # * dpstatement ("Datenschutzerklärung")
      # * dppopup, dppopupconfig, dppopupcss, dppopupjs ("Cookie-Dialog", use all four or none)
      # For example: ['imprint', 'contractterms', 'dpstatement', 'dppopup', 'dppopupconfig', 'dppopupcss', 'dppopupjs']
      services: []
      # The language to use for imprint, data protection popup etc. if no language is passed to the eel helper
      # or if the language passed to the eel helper does not exist in the dataset.
      # For example: de
      fallbackLanguage: ''
```

Set up a cronjob that executes `./flow legalweb:update`.

Open the legalweb.io backend module and click "Daten jetzt aktualisieren".
This will download the current dataset from the legalweb.io API.

Ensure that the `LegalWeb.GdprTools:Component.DataProtectionPopup` fusion component is included in every page at the
beginning of the `body` tag. The component must be included at the top because it loads JS which will automatically
block embedded contents like videos. If you include the JS at the bottom of the body tag, it will not be able to block
video loading.

Replace the content of your imprint, contract terms and data protection pages with the nodes
`LegalWeb.GdprTools:Imprint`, `LegalWeb.GdprTools:ContractTerms` and `LegalWeb.GdprTools:DataProtectionStatement`
respectively.

![Screenshot of node creation dialog](https://user-images.githubusercontent.com/4510166/90875089-e9806600-e3a0-11ea-8873-5ba934cf72bc.png)

## Coding Style and Tests

Use [PSR-12](https://www.php-fig.org/psr/psr-12/) and check your changes with `composer check` before committing. This will run [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) and [phpstan](https://github.com/phpstan/phpstan).

To execute the tests, run `composer test`.
