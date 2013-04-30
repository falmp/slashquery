slashquery /?
=============

A multitenant (barebones) PHP MVC framework

SlashQuery "/?" consist on a single core, that can host multiple sites (aka virtual hosts) where each of the sites is totally isolated and has no relation with other sites sharing the same core.

Each site needs its proper configuration (config.php per site), you need to configure database, sessions, smtp, etc;

The only thing shareds among all the sites are the physical resources of the machine.  SlashQuery takes advantage off all the resources available and use them within all the sites that it hosts.

Only one default virtual host (catch all) configuration in nginx/apache is needed to properly serve multiple sites within SlashQuery.

Since always, the goals have been speed with simplicity, keeping in mind security and performance.

SlashQuery internals, basically consist on a single Dependency Injector and a Dispatcher that extends the  Observer Pattern.

Documentation / improvements still in progress ...

Install
=======
git clone https://github.com/nbari/slashquery.git slashquery

cd slashquery

git submodule init
