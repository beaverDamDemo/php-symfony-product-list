<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Create routes
$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => function () {
        $html = '
        <!DOCTYPE html>
        <html lang="sl">
        <head>
            <meta charset="UTF-8">
            <title>Navodila</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
        background: linear-gradient(135deg, #eceff1, #fafafa);
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 28px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    h1 {
        font-size: 26px;
        margin-bottom: 16px;
        color: #1565c0;
        border-left: 5px solid #1565c0;
        padding-left: 10px;
    }
    h2 {
        font-size: 20px;
        margin-top: 28px;
        margin-bottom: 10px;
        color: #2e7d32;
        border-left: 4px solid #2e7d32;
        padding-left: 8px;
    }
    ul {
        margin: 0 0 16px 20px;
    }
    li {
        margin-bottom: 6px;
        line-height: 1.5;
    }
</style>

        </head>
        <body>
            <div class="container">
                <h1>Pričakujemo</h1>
                <ul>
                    <li>osnovno poznavanje HTML, CSS in Bootrstrap</li>
                    <li>osnovno poznavanje Javascript (jQuery, vanilla javascript)</li>
                    <li>poznavanje logike CMS sistemov</li>
                    <li>poznavanje programskega jezika PHP</li>
                    <li>osnove poznavanja objektnega programiranja</li>
                    <li>znanje angleškega jezika (pisno in pogovorno)</li>
                    <li>zanesljivost, doslednost in odgovornost</li>
                    <li>želja po iskanju novih rešitev in izboljšav</li>
                    <li>željo po učenju novih stvari</li>
                </ul>

                <h2>Zaželena znanja (ki niso pogoj)</h2>
                <ul>
                    <li>poznavanje modernih smernic spletnega razvoja in odzivnega (responsive) dizajna</li>
                    <li>poznavanje modernih tehnologij za frontend (CSS, SCSS, HTML, responsive design, Bootstrap, jQuery, React, Angular, VueJs) je prednost</li>
                    <li>poznavanje PHP frameworkov (npr. Symfony, Laravel, Zend) je prednost</li>
                    <li>uporaba version control sistemov, predvsem Git-a je prednost</li>
                    <li>poznavanje drugih programskih jezikov</li>
                    <li>poznavanje SEO smernic je prednost</li>
                </ul>

                <h2>Zahteve naloge</h2>
                <ul>
                    <li>V PHP-u (+CSS/SCSS, JS) narediti stran in podstran na podlagi dizajna</li>
                    <li>Stran s seznamom izdelkov naj vsebuje 5 izdelkov</li>
                    <li>Klik na gumb "več" pri izdelkih odpre podstran s prikazom enega izdelka</li>
                    <li>Uporabijo se lahko različni frameworki in knjižnice (PHP, CSS in JS)</li>
                    <li>Lahko se uporabi tudi kakšen template sistem</li>
                    <li>Stran naj bo responsive, prilagojen prikaz na mobitelih &lt;500px (na tablicah ni treba)</li>
                    <li>Stran mora delovati na Apache 2.4 (dovoljena uporaba .htaccess datotek) s PHP 8</li>
                </ul>
            </div>
        </body>
        </html>
        ';

        return new \Symfony\Component\HttpFoundation\Response($html);
    }
]));

$routes->add('products', new Route('/products', [
    '_controller' => [new \App\ProductController(), 'list']
]));

// Handle request
$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controller = $parameters['_controller'];
    $response = $controller();
} catch (Exception $e) {
    $response = new Response('Not Found', 404);
}

$response->send();
