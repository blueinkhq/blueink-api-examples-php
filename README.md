# BlueInk API Examples in PHP

This project contains examples of working with the 
[BlueInk eSignature API](https://blueink.com/esignature-api/) using
the [blueink-client-php](https://github.com/blueinkhq/blueink-client-php) 
library, and the [blueink-embed-js](https://github.com/blueinkhq/blueink-client-php) 
library for embedded signings.

It is built using the Symfony Framework for basic web app functionality, 
but any PHP framework will work when using the blueink-client-php
library (or no framework at all).

The actual BlueInk API related code is in `src/Service/BlueInkHelper.php`.
Look there first for examples.

## Getting Started

This project is built to be run locally, or to be deployed to Heroku.
It can be run in other environments, but those are not documented.

### Download the Project and Run it Locally

First, clone the repo and install dependencies.
```bash
$ git clone git@github.com:blueinkhq/blueink-api-examples-php.git
$ cd blueink-api-examples/
$ composer install
```

Next, you need to setup some environment variables. Create the file
`.env.local` in your favorite editor, and then add these values. Fill in the
<...> placeholders with real values.

```
APP_SECRET=<app secret here>
BLUEINK_PUBLIC_API_KEY=<Your public API key, from your BlueInk Account>
BLUEINK_API_KEY=<A private API key, from your BlueInk Account>
```

You can generate an APP_SECRET on the command line, like so:
```bash
$ php -r 'echo bin2hex(random_bytes(16)) . "\n";'
```

The public API key and private API key are available in your BlueInk Account dashboard,
in the API section. The public API key always starts with `public_` is used in the frontend 
to identify your BlueInk Account. The private API key should always be kept secret. It is used
to make authenticated calls to the BlueInk API from the backend.

You are now ready to run the project locally using the built-in Symfony HTTP server.
(Note, this requires that you have installed the symfony CLI
so that the `symfony` command is available - see the 
[symfony CLI installation instructions]((https://symfony.com/download))).
```bash
$ symfony server:start
``` 

## Getting Support

For code issues or questions with this project, please create an issue in this repo. 

Or if you currently have a BlueInk account, you can
reach out to our API support team from within the BlueInk Dashboard.

## License

MIT