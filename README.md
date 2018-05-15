---
currentMenu: home
---
Goteo The Open Source Crowdfunding Platform
===========================================

[![Build Status](https://travis-ci.org/GoteoFoundation/goteo.svg?branch=devel)](https://travis-ci.org/GoteoFoundation/goteo)

This is the source code of the [Crowdfunding platform Goteo](http://goteo.org)

> This is a web app that allows the receipt, review and publishing of collective campaigns for their collective funding and the receiving of collaborations as well as the dynamic visualization of the support received, classification of initiatives and campaign tracking. The system also permits secure and distributed communication with users and between users, administration of highlighted projects on the home page and the creation of periodical publications such as blogs, a FAQ section and static pages.

Current version: **3.3**

Although you can try to install it in order to create your own crowdfunding platform, this won't be a *Wordpress*-like installation (probably never will). This releases are mostly for developers and for those who want to collaborate with the code.

Full developers documentation is still work in progress!
Some folders may have its own README.md file with comments. However, we will try to compile all the documents here:

### DOCUMENTATION

- [Install](http://goteofoundation.github.io/goteo/docs/install.html)
- [Upgrade](http://goteofoundation.github.io/goteo/docs/upgrade.html)

> ### Docker quickstart:
> 
> ```bash
> cp config/docker-settings.yml config/local-docker-settings.yml
> docker-compose up
> ```
> 
> THen you can run commands inside the php container by using the wrapper `docker/exec`. If develping, we recommend to run this command in a separate terminal:
> 
> ```bash
> docker/exec grunt watch
> ```
> 
> [More info](http://goteofoundation.github.io/goteo/docs/developers/environment.html#docker)

### CONTRIBUTING

There's still a lot of documentation missing. We'll try to do our best completing it, however any help will be appreciated.

- [Developers](http://goteofoundation.github.io/goteo/docs/developers/environment.html)
- [Credits](http://goteofoundation.github.io/goteo/release_notes.html)

### TRANSLATORS

Since version 3.3 where are using [Crowdin](https://crowdin.com/) as a tool for collaborators translating the code. Feel free to join this awesome people who's contributed in traslating Goteo to many languages:

<translators>

<ul>
    <li>Dan Walenter (cas3v0n)</li>
    <li>Denver Moon (9003104)</li>
    <li>José Peralta (elpoliglota) (josedpg11)</li>
    <li>Chris_W</li>
    <li>helpPAM</li>
    <li>melsmacan</li>
    <li>kguanzon (Kimberly Guanzon) (kim0421)</li>
    <li>joao.cruz</li>
    <li>pep_laDeriva</li>
    <li>CataAz</li>
    <li>Clint Mark Cortes (cortesclintmark)</li>
    <li>zainalkhalid</li>
    <li>franzancot</li>
    <li>Udo Wierlemann (Udo.Wierlemann)</li>
    <li>Herii (HERII12)</li>
    <li>shaunmatthew</li>
    <li>Ceyda Mutlu (translator93)</li>
    <li>Júlia Petúlia Sol (juliaxsol)</li>
    <li>enolp</li>
    <li>elena-2018</li>
    <li>firewall (Onrkskn)</li>
</ul>
</translators>

You can join to the Crowdin translator team here: https://translate.goteo.org/


*This documentation is created using [Couscous](http://couscous.io).*

License
-------

The code licensed here under the **GNU Affero General Public License**, version 3 AGPL-3.0 has been developed by the Goteo team led by Platoniq and subsequently transferred to the Fundación Goteo, as detailed in http://www.goteo.org/about#info6

