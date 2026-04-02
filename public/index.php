<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function renderLayout(string $title, string $activeKey, string $contentHtml): Response
{
    $navLinks = [
        ['label' => 'Domov', 'href' => '/', 'key' => 'home'],
        ['label' => 'O nas', 'href' => '/o-nas', 'key' => 'about'],
        ['label' => 'Kontakt', 'href' => '/kontakt', 'key' => 'contact'],
        ['label' => 'Pišite nam', 'href' => '/pisite-nam', 'key' => 'write'],
        ['label' => 'IZDELKI', 'href' => '/izdelki', 'key' => 'products'],
    ];

    $menuHtml = '';
    foreach ($navLinks as $link) {
        $isActive = $activeKey === $link['key'] ? ' active' : '';
        $menuHtml .= sprintf(
            '<a class="nav-link%s" href="%s">%s</a>',
            $isActive,
            htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8')
        );
    }

    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    $html = <<<HTML
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safeTitle}</title>
    <style>
        :root {
            --side-padding: clamp(14px, 4vw, 64px);
            --grid-bg: #f5f9fc;
            --grid-line: #d5e0ea;
            --header-start: #1565c0;
            --header-end: #2e7d32;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #11253a;
            background-color: var(--grid-bg);
            background-image:
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 34px 34px;
        }

        .site-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto auto 1fr;
        }

        .row-inner {
            padding-left: var(--side-padding);
            padding-right: var(--side-padding);
        }

        .logo-row {
            padding-top: 12px;
            padding-bottom: 12px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .logo {
            width: min(220px, 48vw);
            height: auto;
            display: block;
        }

        .header-row {
            background: linear-gradient(110deg, var(--header-start), var(--header-end));
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.14);
        }

        .header-nav {
            min-height: 56px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .nav-link {
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.02em;
            padding: 8px 10px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .content-row {
            padding-top: 22px;
            padding-bottom: 32px;
        }

        .placeholder-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(17, 37, 58, 0.14);
            border-radius: 10px;
            padding: 18px;
            max-width: 760px;
        }

        .placeholder-card h1 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: clamp(22px, 4vw, 34px);
        }

        .placeholder-card p {
            margin: 0;
            line-height: 1.55;
        }

        @media (max-width: 560px) {
            .header-nav {
                gap: 6px;
                align-items: stretch;
            }

            .nav-link {
                font-size: 14px;
                padding: 7px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <div class="logo-row row-inner">
            <img class="logo" src="/logo.png" alt="Logo">
            <img class="logo" src="/kabi-test.png" alt="Kabi-Test">
        </div>

        <header class="header-row">
            <nav class="header-nav row-inner" aria-label="Glavna navigacija">
                {$menuHtml}
            </nav>
        </header>

        <main class="content-row row-inner">
            {$contentHtml}
        </main>
    </div>
</body>
</html>
HTML;

    return new Response($html);
}

// Create routes
$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => static function () {
        return renderLayout('Domov', 'home', '
            <section class="placeholder-card">
                <h1>Vsebina bo dodana kmalu</h1>
                <p>Pripravljena je skupna struktura strani: mrežni CSS background, vrstica z logo PNG, gradient header in odzivna navigacija.</p>
            </section>
        ');
    },
]));

$routes->add('about', new Route('/o-nas', [
    '_controller' => static function () {
        return renderLayout('O nas', 'about', '
            <section class="placeholder-card">
                <h1>O nas</h1>
                <p>To je pripravljena podstran z enakim layoutom. Vsebino lahko dodaš kasneje.</p>
            </section>
        ');
    },
]));

$routes->add('contact', new Route('/kontakt', [
    '_controller' => static function () {
        return renderLayout('Kontakt', 'contact', '
            <section class="placeholder-card">
                <h1>Kontakt</h1>
                <p>Tudi ta podstran uporablja isti globalni layout za celotno spletno stran.</p>
            </section>
        ');
    },
]));

$routes->add('write', new Route('/pisite-nam', [
    '_controller' => static function () {
        return renderLayout('Pišite nam', 'write', '
            <section class="placeholder-card">
                <h1>Pišite nam</h1>
                <p>Prostor za bodoč obrazec ali kontaktne informacije.</p>
            </section>
        ');
    },
]));

$routes->add('products', new Route('/products', [
    '_controller' => static function () {
        return renderLayout('Izdelki', 'products', '
            <section class="placeholder-card">
                <h1>Izdelki</h1>
                <p>To je začasni vsebinski blok. Produktni del lahko dodaš v naslednjem koraku.</p>
            </section>
        ');
    },
]));

$routes->add('products_sl', new Route('/izdelki', [
    '_controller' => static function () {
        return renderLayout('Izdelki', 'products', '
            <section class="placeholder-card">
                <h1>Izdelki</h1>
                <p>To je začasni vsebinski blok. Produktni del lahko dodaš v naslednjem koraku.</p>
            </section>
        ');
    },
]));

// Handle request
$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controller = $parameters['_controller'];
    $response = \call_user_func($controller);
} catch (\Throwable $e) {
    $response = new Response('Not Found', 404);
}

$response->send();
