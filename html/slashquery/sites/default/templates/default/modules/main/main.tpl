<div class="centered sqMain">
  <h1>SlashQuery /?</h1>

  <p>A multitenant (barebones) PHP HMVC framework</p>
</div>

<div class="clear spacer"></div>

<div class="container_24">
  <div class="grid_24">
    <h1>Hello World!</h1>

    <p>SlashQuery "/?" consist on a single core, that can host multiple sites (aka virtual hosts) where each of the sites is totally isolated and has no relation with other sites sharing the same core.</p>

    <p>Each site needs its proper configuration (config.php per site), you need to configure database, sessions, smtp, etc;</p>

    <p>The only things shared among all the sites are the physical resources of the machine. SlashQuery takes advantage off all the resources available and use them within all the sites that it hosts, coupling perfectly in cloud environments and scaling without hassle.</p>

    <p>To properly scale and support heavy &amp; busy sites, SlashQuery rely on <a href="http://www.nginx.com">NGINX</a> webserver and <a href="http://www.freebsd.org">FreeBSD</a> operating system.</p>
  </div>

  <div class="clear spacer"></div>

  <div class="grid_12">
    <h1>What it is &#10004;</h1>

    <ul>
      <li>A "<a href="http://en.wikipedia.org/wiki/Multitenancy">multitenancy</a>" model/logic for creating PHP applications and websites.</li>

      <li>A Front End Controller.</li>

      <li>A HMVC (Hierarchical model–view–controller) Architecture.</li>

      <li>A group of classes handled by a Dependecy Injector.</li>

      <li>A 'Barebone' cPanel with Access Control List (ACL).</li>

      <li>An extensible Object Oriented PHP Framework.</li>
    </ul>
  </div>

  <div class="grid_12">
    <h1>What it is NOT &#10007;</h1>

    <ul>
      <li>A point and click sofware, there is no installer.</li>

      <li>A blogging system.</li>

      <li>A template engine.</li>

      <li>A zero configuration framework.</li>

      <li>A "full stack" framework.</li>

      <li>An effortless framework, nothing has been made, you have to build your stuff.</li>
    </ul>
  </div>

  <div class="clear spacer"></div>

  <div class="grid_24">
    <h1>Is "SlashQuery /?" for you?</h1>

    <p>Thinking of SlashQuery as a logic pattern rather than a Framework, can give many posibilities to almost everyone to build secure and eficient applications, either you want to build a simple static site, a dinamic site using your desired database backend or even a complex "Portuguese Architecture" model.</p>

    <p>The main core, as a very small footprint, doesn't require a database for working and the model/control is tottaly decoupled from the view, this give an exceptional performance when working with AJAX or API's that don't need to parse the view, normally the html code.</p>

    <p>PHP by its genetics, can couple perfectly with HTML language, therefore there is not need to overload or put another layer of complexity to the code, you can configure/create your view without need to learn a template language.</p>

    <p>SlashQuery it is not a point and click software, you will have to properly configure your <a href="http://www.nginx.com">NGINX</a> or webserver of choice, as you must have php compiled with some modules depending of your applications.</p>

    <p>By default, multiple-sites are supported but each site need a dedicated configuration and setup, think of it like creating virtualhost on a webserver.</p>

    <p>Setting up SlashQuery up and running, could be very easy and without hassle, but as always that is relative, either if you want to build a MVC object oriented website that requires access control lists (<a href="/cpanel">cPanel</a>) or a simple blog, SlashQuery can be a great tool for that, so you can focus more on your application and let the design to another team, coupling both parts at the end very easy.</p>

    <p>Keep in mind that SlashQuery is only a group of classes that follows a logic pattern, therefore you have to create your own code the way you want without any restrictions. The <a href="/cpanel">cPanel</a> is the only "module" already made and is common to all the sites, is an area where you can create users, enable modules, etc, something like a backoffice for your sites that can be extended as you required.

    <p>If you require a multihosting platform with exceptional performance, a minimalistic base designed with security and simplicity in mind, have a perfectionist spirit and enjoy doing things the right way avoiding complexity, SlashQuery is for you.</p>

  </div>
</div>

<div class="clear"></div>
