---
currentMenu: events
---
Event in Goteo
==================

The Goteo framework is based in Symfony, and uses the [Event Dispatcher](https://symfony.com/doc/2.8/components/event_dispatcher.html) to throw events that can be used to alter the default behaviour of many aspects of Goteo, like the payment flow, project creation, route flow, etc.

Kernel events
----------

These events are thrown by the Kernel of Symfony Components. For a full specification please check the [official documentation](https://symfony.com/doc/2.8/components/http_kernel.html).

A part from the `Symfony/Component/HttpKernel/HttpKernel.php` itself, the 2 most important classes used in the Symfony's Http workflow are the classes `Symfony/Component/HttpFoundation/Request.php` and `Symfony/Component/HttpFoundation/Response.php`

They cover the 5 events carried out by the Symfony Kernel when handling an http petition:

![Payment workflow](assets/http-workflow.svg)

1. **REQUEST** Used to know about the http request before doing anything else. Several Event Listeners in `src/Goteo/Application/EventListener/*.php` classes use this event.

2. **EXCEPTION** Can be used to intercept any (non fatal) exception occurring during normal operation. The `src/Goteo/Application/EventListener/ExceptionListener.php` uses this event to convert different types of exceptions to appropriate http responses (404, 500, etc).

3. **CONTROLLER** This event allows you to change or manipulate the controller *before* the original specified in the routing system is executed.<br><br>As an example, check the plugin `custom-domains`, file `extend/custom-domains/src/CustomDomains/EventListener/DomainListener.php`, which uses this event to find the appropriate controller for a custom domain/subdomain route.

4. **VIEW** Although this event is available, it is not currently used internally by Goteo. This event is thrown after executing the controller **only if it doesn't return any Response object**.

5. **RESPONSE** Once controller returns a Response object, this event is thrown, allowing to manipulate the response if needed.<br><br>As an example, check the file `extend/goteo-dev/src/Goteodev/Profiler/EventListener/ProfilerListener.php` of the plugin `goteo-dev` that adds some custom html (a debug bottom bar) content to generated pages.

6. **TERMINATE** This event is thrown after the response has been sent to the browser, so it allows you to perform some "heavy" operations without making the user wait for it. We use this basically to send mails in some cases.

7. **FINISH_REQUEST** We don't use this event. It is thrown after a Response has been generated for a Request.


Application events
----------

These are custom events thrown during the normal execution of an http workflow in Goteo. They allow the listeners to perform extra operations (or change the default behavior) on many specific Goteo tasks. Such as creating a project, a new payment is done, a user has logged, a post has been created, etc.

The `file src/Goteo/Application/AppEvents.php` has a full list of these events. Some of them are:

1. **VIEW_RENDER** Thrown when a Foil template view is about to be rendered. These allows (for example), to add some custom vars that can be used when a template is overwritten by a plugin.

2. **LOGIN_SUCCEEDED** As you probably guess, this event happens after a successful user's login action. Other similar events are provided for failed logins, logout and user registration. Please consult the `AppEvents.php` source file for more info.<br><br>As an example, check the file `src/Goteo/Application/EventListener/AuthListener.php` that uses this event to establish a "remember-me" cookie on login.

3. **RESET_PASSWORD** Thrown when a user reset his password. This event is used in the same `AuthListener` class to add a log entry in the system when this happens.

4. **INVEST_INIT** This, and all the other `INVEST_*` events manages the payment workflow in Goteo. You can have more information about that in the [payments section](payments.html).<br><br>The `src/Goteo/Application/EventListener/InvestListener.php` class uses these events intensively in order to process the invest entry status.

5. **PROJECT_CREATED** This event is thrown when a project is created by the controller `ProjectController`. It allows you to perform operations on the project when it's created. <br><br>For example, the `src/Goteo/Application/EventListener/ProjectChannelListener.php` class uses this event in combination with the Symfony's `KernelEvent::REQUEST` to change the `node` property of the created project if it's created by the controller ChannelController instead of the ProjectController (note that ChannelController forwards the creation of the project to the original ProjectController so the event PROJECT_CREATED is thrown anyway).

6. **PROJECT_PUBLISH** Thrown when a project is published. The `src/Goteo/Application/EventListener/ProjectListener.php` uses this event to create appropriate feed/log entries.

7. **PROJECT_READY** This is thrown when the project passes from *editing* to *reviewing*, this is the status before publishing when it's supposed to be checked by the consultants.<br><br>In the `ProjectListener` class we use this event to (among other things) send emails to the consultants in order to review the project before publishing.

8. **PROJECT_VALIDATION** This event is thrown every time the function `->getValidation()` from an object Goteo\Model\Project is called. This allows to change or substitute the default project validation logic if needed.

9. **PROJECT_POST** Event thrown when a project's owner creates a post. Internally it's used by the class `src/Goteo/Application/ProjectPostListener.php` to create a project milestone when that happens.

10. **MESSAGE_CREATED** Thrown when a message communication between users is created.

11. **MESSAGE_UPDATED** Sames as before, when a message is updated.

12. **MATCHER_PROJECT** Matchers are the matchfunding operation system in Goteo. They allow to perform automatic invest operations according to users invests actions (or any other event). This event is thrown when a project is added to a matcher (currently must be specifically dispatched by a plugin or using the internal user dashboard AJAX api, url `/dashboard/ajax/matchers/{matcher-id}/{action}/{project-id}`).<br><br>We are currently working in this area to provide a better support for it.

Console events
--------------

Console events are thrown the the Goteo Cron operation, they are unrelated to users/web actions, mostly, involve operations like return invests when a project fails, a project is automatically published, changes round or a massive mailing is sent.

Check the `src/Goteo/Console/ConsoleEvents.php` file for a full description of the available events.

Internally Goteo uses theses events in different event listeners attached by console commands (executed by the cron). Check the folder `src/Goteo/Console/EventListener` for examples.

Model events
------------

Model events are events thrown before and after a CRUD database operation is done by any Goteo\Model\* instance. As they are low level operation there are in the Core namespace. The file `src/Goteo/Core/ModelEvents.php` has the full list and description.

They are meant to allow plugins a high flexibility to perform very specific custom actions when certain database table entry changes.

<a name="plugin"></a>
Using events in plugins
-----------------------

To use the events in plugins you can use the service container to  attach new actions for the purpose of the plugin.

Typically, the `start.php` file must be used to add custom event listeners to any event.

Let's say we want to built a plugin that performs a custom action when a Project is published (for example, publish a twit, send an email, or something similar). Specifically we will create a twit in this example.

First, we create the new plugin structure in the folder extend:

File `extend/project-twit/start.php` (entry point for the plugin):

```php
<?php 

// Empty file to start
```

File `extend/project-twit/manifest.yml` (meta-information about the plugin):

```yaml
name: ProjectTwitter
version: 1.0
```

Now we need to create an application in https://apps.twitter.com, grab the consumer key and the consumer secret, generate an access token and a  access token secret and copy everything in our goteo settings:

In the `config/settings.yml` we add these lines in order to activate the plugin:

```yaml
plugins:
    project-twit:
        active: true
        # Some custom vars, we will use
        # Generated in https://apps.twitter.com/
        oauth_access_token: YOUR_OAUTH_ACCESS_TOKEN
        oauth_access_token_secret: YOUR_OAUTH_ACCESS_TOKEN_SECRET
        consumer_key: YOUR_CONSUMER_KEY
        consumer_secret: YOUR_CONSUMER_SECRET
```

Great, the plugin is created and already working, however it does nothing yet. 

We want to keep track of projects in order to automatically create a twit every time is published.

To do that, we must create the event listener class:

File `extend/project-twit/ProjectTwitter/TwitterListener.php`:

```php
<?php
namespace ProjectTwitter;

use Goteo\Application\EventListener\AbstractListener;

use Goteo\Application\AppEvents;
use Goteo\Application\Message;

use Goteo\Application\Event\FilterProjectEvent;

class TwitterListener extends AbstractListener {
    public function onProjectPublished(FilterProjectEvent $event) {
        $project = $event->getProject();

        Message::info("I've notice that the project {$project->name} has been published");

    }

    public static function getSubscribedEvents() {
        return array(
            AppEvents::PROJECT_PUBLISH => 'onProjectPublished'
        );
    }
}
```

This is a very basic listener, for the moment it only will write a flash notification in the web page when a project is published by an admin.

But, to get it to work, we need to subscribe this event in the `start.php` file in order to Goteo to use it, we add these lines to the file `start.php`:

```php
<?php

// Not empty anymore

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\DependencyInjection\Reference;

// Autoload additional Classes in this plugin
Config::addAutoloadDir(__DIR__ );

// Get the service container to add our custom services in it:
$sc = App::getServiceContainer();

// Register a custom reference with our EventListener class
$sc->register('project-twit.twitter_listener', 'ProjectTwitter\TwitterListener')
   ->setArguments(array(new Reference('logger'))); // 'logger' is the default logger defined in the main container.php file, because our event listener inherits from the standard AbstractListener used in Goteo that needs a Logger class as argument's constructor


// Add the subscriber to the service container
$sc->getDefinition('dispatcher')
   ->addMethodCall('addSubscriber', array(new Reference('project-twit.twitter_listener')))
;

```

We've added the code to tell Goteo about our event listener, now every time the event `AppEvent::PROJECT_PUBLISH` is thrown, our code in the function `onProjectPublished` will be executed.

From here, the only thing to do is to improve the code on that function to actually perform the desired action (ie: publish a twit).

But, before to do that, let's notice that there are 2 different situations in which a project can be published. One is when and admin (in the admin web interface), manually publishes the project. The other possibility is when a project is published automatically (because and admin has programed the project to do that on certain day). The automatic publishing is done by the cron and fires a different Event, in this case whe need to subscribe to `ConsoleEvent::PROJECT_PUBLISH` as well.

Let's modify the file `extend/project-twit/ProjectTwitter/TwitterListener.php` file accordingly:

```php
<?php
namespace ProjectTwitter;

use Goteo\Application\EventListener\AbstractListener;

use Goteo\Application\AppEvents;
use Goteo\Console\ConsoleEvents;

use Goteo\Application\Event\FilterProjectEvent;
// we use the Console\EventFilterProjectEvent because is compatible with either the AppEvents::PROJECT_PUBLISH or the ConsoleEvents::PROJECT_PUBLISH. This way we can use the same function for both events
use Goteo\Console\Event\FilterProjectEvent as ConsoleProjectEvent;;

class TwitterListener extends AbstractListener {
    public function onProjectPublished(ConsoleProjectEvent $event) {
        $project = $event->getProject();

        Message::info("I've notice that the project {$project->name} has been published");

    }

    public static function getSubscribedEvents() {
        return array(
            AppEvents::PROJECT_PUBLISH => 'onProjectPublished',
            ConsoleEvents::PROJECT_PUBLISH => 'onProjectPublished'
        );
    }
}
```

Now we are not going to miss any project publication event. And we're ready to complete the code of the `onProjectPublished` function:

```php
<?php

// We are going to use settings.yml values
use Goteo\Application\Config;
// This allows to create info/errors messages in the web
use Goteo\Application\Message;
// A simple class to interact with twitter
// https://github.com/J7mbo/twitter-api-php
use TwitterAPIExchange;

... // <- same coded as before here

   public function onProjectPublished(ConsoleProjectEvent $event) {
        $project = $event->getProject();
        $domain = Config::getMainUrl();

        $name = trim($project->name ? $project->name : $project->id);
        // TODO: make this text configurable from settings
        $twit = "The new project \"name\" has been published. Check it out here in $domain/project/{$project->id} #crowdfunding #opensource";

        // publish the twit:
        $resource = 'https://api.twitter.com/1.1/statuses/update.json';
        // Get the auth settings from the config settings
        $settings = array(
            'oauth_access_token' => Config::get('plugins.project-twit.oauth_access_token'),
            'oauth_access_token_secret' => Config::get('plugins.project-twit.oauth_access_token_secret'),
            'consumer_key' => Config::get('plugins.project-twit.consumer_key'),
            'consumer_secret' => Config::get('plugins.project-twit.consumer_secret')
        );

        $postfields = [
            'status' => $twit
        ];
        $twitter = new TwitterAPIExchange($settings);
        $result = $twitter->buildOauth($resource, 'POST')
            ->setPostfields($postfields)
            ->performRequest();

        // Add a flash Message if the event it's created in the website
        if($event instanceOf FilterProjectEvent) {
            if($result->errors) {
                $reason = $result->errors[0]->message;
                Message::error("Couldn't create a new twit: <strong>$reason</strong>");
            } else {
                $url = $result->entities->urls[0]->url;
                Message::info("A new twit has been created: <a href=\"$url\">$url</a>");
            }
        }

        // TODO: perform some loggging or other operation depending on the twitter post operation
   }

```

Ok, now the code is complete, but we've used an external component to publish our twit, the `TwitterAPIExchange` class, so we need to include it in our plugin, otherwise Goteo will not now where to find it and it will throw a fatal error.

The easiest way to include it is by using composer: in a terminal execute (we are going to use the class [TwitterAPIExchange](https://github.com/J7mbo/twitter-api-php)):

```bash
cd extend/project-twit
composer require j7mbo/twitter-api-php -d ./
```

Now we will have a new `vendor` folder in our plugin, we need to include that in Goteo's autoload class system, let's add some lines in the `start.php` file:

```php
<?php

// Not empty anymore
use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\DependencyInjection\Reference;

// Autoload additional Classes in this plugin
Config::addAutoloadDir(__DIR__ );
// Include our custom composer vendor file
Config::addComposerAutoload(__DIR__  . '/vendor/autoload.php');

... // rest of the code as before
```

That's it! You can check the whole source code of this plugin in this repository: 

https://github.com/microstudi/goteo-project-twitter-plugin
