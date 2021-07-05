---
currentMenu: release_notes
---
GOTEO VERSION 3
===============

This is source code of the open source [Goteo Crowdfunding Platform](http://goteo.org)

Please read the docs for more information.

http://goteofoundation.github.io/goteo

### What's new on Goteo V3:

This is a list of (still most work-in-progress) of the new features:

- Now we use composer for 3rd party libraries
- Introducing unit tests with phpunit
- Migrating routing, DI, Events, etc. by using Symfony components
- A full rewrite of the view subsystem using a template system (using Foil).
- Optimizations, bug fixes
- A development cycle based on github. Meaning that we will publish bug fixes and improvements regularly in github.
- A new logging system (using Monolog and Logstash format)
- Extendibility (custom functionality can added by external plugins)
- Using grunt for developing
- Better translations

#### Version 3.5.2

- New communication module for the admin.
- New user filters module, in order to create segmentations for the communication module
- New newsletter and default communication responsive
- Landing workshops and new admin module to manage it.
- Tip feature in the invest process: allow make a tip for the platform in the projects.
- New feature to make a direct donation to the platform organization.
- ODS integrated in the project form
- New responsive promotes module

#### Version 3.5

- User stories
- Improvements in Html/markdown editors (images can be dropped in)
- New responsive user profile
- Added SDGs tables
- Docker improvements (added mailhog and maxmind)
- Internal messages improvements
- Some new responsive admin modules (blog, categories)
- Bug fixes and several refactorizations

#### Version 3.4

- Added translations (thanks to the community)
- New documentation theme
- New responsive blog module
- Added Sass for css development
- New stats module for the new admin
- New admin framework (responsive) for admin
- Bug fixes and several refactorizations

#### Version 3.3

- New responsive Home
- Added automated tests in Travis
- Bug fixes, better translations & documentation

#### Version 3.2

- Includes a new full responsive dashboard for users
- PHP7 updated
- Added migrate CLI command to smooth databases migrations
- Improved install from the scratch
- Updated vagrant to use ubuntu 16.04
- Added Matcher model for custom matchfunding operations
- Plugin improvements
- Lot of fixes

## Credits

**On version 3**

[Ivan Vergés](http://github.com/microstudi), [Javier Carrillo](https://github.com/javicarrillo).

**On version 2:**

Development (conceptualization, information architecture, text, programming and interface design): Susana Noguero, Olivier Schulbaum, Enric Senabre, Diego Bustamante, Julian Canaves, Ivan Verges

Translation of interface and texts Catalan: Mireia Pui and Enric Senabre English: Liz Castro and Chris Pinchen French: Charlotte Rautureau, Julien Bellanger, Thomas Bernardi, Marie-Paule Uwase, Olivier Heinry, Christophe Moille, Olivier Schulbaum, Salah Malouli, Roland Kossigan Assilevi

Legal advice and data privacy: Jorge Campanillas and Alfonso Jorge Pacheco

Other code writers: Jaume Alemany, Philipp Keweloh, Susanna Kosic, Marc Hortelano, Pedro Medina
