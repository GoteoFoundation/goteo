---
currentMenu: home
---
Goteo The Open Source Crowdfunding Platform
===========================================

[![[CI] Test](https://github.com/GoteoFoundation/goteo/actions/workflows/test.yml/badge.svg)](https://github.com/GoteoFoundation/goteo/actions/workflows/test.yml)

This is the source code of the [Crowdfunding platform Goteo](http://goteo.org).

The Goteo platform is a recognized, award-winning creator of an open standard for crowdfunding for social impact and generating new digital commons, which has been founded and designed by [Platoniq](http://platoniq.net). Platoniq is also the founding board member of the Goteo Foundation which is the responsible for the maintenance of this code.

[![Backed by Goteo Foundation](docs/developers/assets/foundation-logo.png)](https://foundation.goteo.org) &nbsp; [![Created by  Platoniq Sistema Cultural](docs/developers/assets/platoniq-logo.png)](http://platoniq.net)

### Features

This is a web app that allows the receipt, review and publishing of collective campaigns for their collective funding and the receiving of collaborations as well as the dynamic visualization of the support received, classification of initiatives and campaign tracking. The system also permits secure and distributed communication with users and between users, administration of highlighted projects on the home page and the creation of periodical publications such as blogs, a FAQ section and static pages.

### INSTALL

Current version: **3.5**

Although you can try to install it in order to create your own crowdfunding platform, this won't be a *Wordpress*-like installation (probably never will). These releases are mostly for developers and for those who want to collaborate with the code.

Full developers documentation is still work in progress!
Some folders may have its own README.md file with comments. However, we will try to compile all the documents here:

### DOCUMENTATION

- [Install](http://goteofoundation.github.io/goteo/docs/install.html)
- [Upgrade](http://goteofoundation.github.io/goteo/docs/upgrade.html)

> ### Docker quickstart:
>
>  First ensure you have `docker-compose` properly installed, then create a config file and use the wrapper `docker/up`:
>
> ```bash
> cp config/docker-settings.yml config/local-docker-settings.yml
> docker/up
> ```
>
> Then you can run commands inside the php container by using the wrapper `docker/exec`. If develping, we recommend to run this command in a separate terminal:
>
> ```bash
> docker/exec grunt watch
> ```
>
> [More info](http://goteofoundation.github.io/goteo/docs/developers/docker.html)

### CONTRIBUTING

There's still a lot of missing documentation. We'll try to do our best completing it, however any help will be appreciated.

- [Developers](http://goteofoundation.github.io/goteo/docs/developers/environment.html)
- [Credits](http://goteofoundation.github.io/goteo/release_notes.html)

### TRANSLATORS

Since version 3.3 where are using [Crowdin](https://crowdin.com/) as a tool for collaborators translating the code. Feel free to join these awesome people who's contributed in traslating Goteo to many languages:

<translators>

<ul>
	<li>Dan Walenter (cas3v0n)</li>
	<li>m19951996</li>
	<li>X (owenthe4th)</li>
	<li>mrfinearts</li>
	<li>Cyberience</li>
	<li>Denver Moon (9003104)</li>
	<li>Wilmer Alzate (waar19)</li>
	<li>Pierre Cardin (reportreport)</li>
	<li>elpoliglota (josedpg11)</li>
	<li>erinbash</li>
	<li>ErikaInsalata</li>
	<li>Chris_W</li>
	<li>David (dabeig)</li>
	<li>Ismaila (iandiaye)</li>
	<li>Joy Lohmann (j.lohmann)</li>
	<li>gracine</li>
	<li>Alessandro Ravanetti (aleravanetti)</li>
	<li>CataAz</li>
	<li>jmontane</li>
	<li>fundycharity</li>
	<li>Ahmad Ainul Rizki (netfr13nd)</li>
	<li>Ahmad Wafa Mansur (wafa.mansur21)</li>
	<li>Tiago Santos (tisantos)</li>
	<li>pep_laDeriva</li>
	<li>detotty</li>
	<li>Ruwan Egodawatte (ruwanego)</li>
	<li>susinho_pantani</li>
	<li>Iulian Mongescu (iulian.mongescu)</li>
	<li>Udo Wierlemann (Udo.Wierlemann)</li>
	<li>Xurxo Guerra (xguerrap)</li>
	<li>melsmacan</li>
	<li>enolp</li>
	<li>Júlia Petúlia Sol (juliaxsol)</li>
	<li>Harun Demirel (translator93)</li>
	<li>firewall (Onrkskn)</li>
	<li>shaunmatthew</li>
	<li>Herii (HERII12)</li>
	<li>franzancot</li>
	<li>zainalkhalid</li>
	<li>elena-2018</li>
	<li>Clint Mark Cortes (cortesclintmark)</li>
	<li>Alexander Stellmach (helpPAM)</li>
	<li>joao.cruz</li>
	<li>kguanzon (Kimberly Guanzon) (kim0421)</li>
	<li>Ferran Reyes (ferranreyesgoteo)</li>
	<li>Vanessa.Montes</li>
	<li>ismaeljoseph</li>
	<li>Carine_cha</li>
	<li>arkimessi</li>
</ul>
</translators>

You can join the Crowdin translator team here: https://translate.goteo.org/


*This documentation is created using [Couscous](http://couscous.io).*

License
-------

The code licensed here under the **GNU Affero General Public License**, version 3 AGPL-3.0 has been developed by the Goteo team led by Platoniq and subsequently transferred to the Fundación Goteo, as detailed in http://www.goteo.org/about#info6

