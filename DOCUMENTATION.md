## Installation guide

### Pre-Installing requirements

Before installing please follow this steps:

1) Go to the dashboard page `/dashboard/system/api/settings` and enable API access on your site.
2) Go to the dashboard page `/dashboard/system/registration/open` and allow visitors to sign up as site members. 
3) Configure the concrete5 mail service (if you don't already have)
    - Setup the SMTP server. If you want to add mail services like Mailgun, Sendgrid etc. you can install the [Mail Service Integration](https://www.concrete5.org/marketplace/addons/mail-service-integration) add-on from Justin978.
    - Optional: Configure [the sender address](https://documentation.concrete5.org/developers/framework/sending-mail/configure-email-sender-addresses). If you don't want to edit the configuration files manually you can take use of the [Handyman](https://www.concrete5.org/marketplace/addons/handyman) add-on from mlocati.
4) You need to have PHP 7.0 at least and concrete5 Version 8.0
5) Your theme should based on Bootstrap 3. If you using another framework you need to create custom templates for the block types.

### Bonus: Customizing the login, register and account pages

This step is not required but it will gain user experience and improve your site quality. 
 
If your active theme doesn't have custom views for the login page, the register page and all account pages built-in you may want to customize it for a better user experience. 

For this you need to

1) [Override the core single pages](https://documentation.concrete5.org/tutorials/override-almost-any-core-file-in-5-7) in your custom theme's directory.
2) [Applying your custom theme](https://documentation.concrete5.org/developers/pages-themes/designing-for-concrete5/applying-your-theme-to-single-pages-with-theme-paths) to the override core pages.

If you have a custom developed theme and this is beyond your scope you can ask your developer or create a job offer post in the [concrete5 job board](https://www.concrete5.org/community/forums) for further support.
 
If you have purchased the theme in the marketplace ask the theme developer. Maybe he is willing to customize these pages.

If you are creating a project from scratch take a look at our Bitter Theme. This theme contains customized versions for all these pages.

### Ready for install

Congratulations. Now you are able to install this add-on. Just enable Community Connect if you not already have, purchase this add-on from the marketplace, assign a license to your site and click on install. If you have troubles with purchasing or installing read [this guide](https://www.concrete5.org/marketplace/how_to_install_add_ons_and_themes_).

### Setting up the block types

By default the installation will install the sample pages correctly with all block types containing.

However. If you want to setup the block types manually you need to create this by bottom up.

That means you need to setup up the pages with the block types in the following order:

1) Create a page with the Ticket Details Block Type
2) Create a page with the Create Ticket Block Type
3) Create a page with the Ticket List Type
4) Create a page with the Project List Block Type

The reason for why you need to have this order is because you need to setup the associated pages within the block type settings. 

## Using the API

The API is useful if you want to remote tickets from a remote site or project.

### List Projects

You can call this method to retrieve all projects id's.

*Method:*

`GET`

*Endpoint:* 

`/index.php/api/v1/project/list`

*Result:*

```
[
  {
    id: 1,
    name: "Test",
    handle: "test"
  },
  {
    id: 2,
    name: "Test 2",
    handle: "test-2"
  }
]
```

### Create Ticket

You can call this method to create a new ticket.

*Method:*

`POST`

*Endpoint:*

`/index.php/api/v1/ticket/create`

*Parameters:* 

`projectId` The project id

`title` A valid title for the ticket

`content` The ticket description

`ticketType` A valid ticket type (allowed values are `bug` | `enhancement`  | `proposal`  | `task`)

`ticketPriority`A valid ticket priority (allowed values are `trivial` | `minor`  | `major` | `critical` | `blocker`)

`email` The email address

`ticketAttachment` attachment of files

If you are signed in with OAuth you don't need to provide the email address. Then the mail address of the signed in user will be used instead.

Result:

```
{
  "error": true,
  "errors": 
  [
    "You need to select a project.",
    "You need to enter a title.",
    "You need to enter a content.",
    "You need to select a type.",
    "You need to select a priority.",
    "You need to enter a valid email address."
  ],
  "time": "2020-11-22 15:20:24",
  "message": null,
  "title": null,
  "redirectURL": ""
}
```

If there an error occurs the `error` property is set to `true` and within the `errors` property you can find the specific error messages.

If the ticket creation was successfully you `error` is set to `false`.

## Events

There are some events in this package that you can hook into.

To learn more about hooking into application events click [here](https://documentation.concrete5.org/developers/framework/application-events/hooking-application-events).

These are the available application events:

- on\_create\_ticket
- on\_create\_ticket\_comment
- on\_ticket\_state\_change

## Customizing the mail templates

If you want to customize the mail templates you can do so by overriding the mail templates.

Copy all php files within `packages/simple_support_system/mail` to `application/mail/` and perform your changes in the php files in your application directory.

## Custom Templates

If you want to customize the markup of the block types you can do so by adding custom templates.

Click [here](https://documentation.concrete5.org/developers/working-with-blocks/working-with-existing-block-types/creating-additional-custom-view-templates) to learn more about creating custom templates.

## Road Map

* Adding pagination to frontend block types for tickets and projects
* Make tickets and projects block type in frontend sortable and searchable
