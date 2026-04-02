<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function getBasePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/public/index.php';
    return rtrim(str_replace('/public/index.php', '', $scriptName), '/');
}

function renderLayout(string $title, string $activeKey, string $contentHtml): Response
{
    $base = getBasePath();

    $navLinks = [
        ['label' => '<span style="font-size:1.4em;line-height:1;position:relative;top:1px;">⌂</span> Domov',    'href' => $base . '/public',           'key' => 'home'],
        ['label' => 'O nas',      'href' => $base . '/public/o-nas',     'key' => 'about'],
        ['label' => 'Kontakt',    'href' => $base . '/public/kontakt',   'key' => 'contact'],
        ['label' => 'Pišite nam', 'href' => $base . '/public/pisite-nam', 'key' => 'write'],
        ['label' => 'IZDELKI',    'href' => $base . '/public/izdelki',   'key' => 'products'],
    ];

    $menuHtml = '';
    foreach ($navLinks as $link) {
        $isActive = $activeKey === $link['key'] ? ' active' : '';
        $menuHtml .= sprintf(
            '<a class="nav-link%s" href="%s">%s</a>',
            $isActive,
            htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8'),
            $link['label']
        );
    }

    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    $html = <<<HTML
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="./favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon_io/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="./favicon_io/favicon.ico">
    <link rel="manifest" href="./favicon_io/site.webmanifest">
    <title>{$safeTitle}</title>
    <style>
        :root {
            --side-padding: clamp(14px, 4vw, 64px);
            --grid-bg: #f5f9fc;
            --grid-line: #d5e0ea;
            --header-start: #47c5ef;
            --header-end: #6f87d7;
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
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.08);
        }

        .row-inner {
            padding-left: var(--side-padding);
            padding-right: var(--side-padding);
        }

        .logo-row {
            padding-top: 24px;
            padding-bottom: 24px;
            padding-left: 0;
            padding-right: 0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 77px;
            height: 77px;
            display: block;
            object-fit: contain;
        }

        .logo-kabi {
            height: 67px;
            width: auto;
            display: block;
            padding-top: 5px;
            padding-bottom: 5px;
            object-fit: contain;
        }

        .header-row {
            background: linear-gradient(to bottom, var(--header-start), var(--header-end));
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.14);
            border-radius: 8px;
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

        .nav-link + .nav-link {
            position: relative;
            margin-left: 16px;
        }

        .nav-link + .nav-link::before {
            content: "|";
            position: absolute;
            left: -14px;
            top: 50%;
            transform: translateY(-50%);
            color: #c4ccd6;
            font-weight: 700;
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
            margin-left: auto;
            margin-right: auto;
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

        .products-wrap {
            max-width: 980px;
            margin: 0 auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        @media (max-width: 720px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }

        .product-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(17, 37, 58, 0.14);
            border-radius: 10px;
            overflow: hidden;
            padding: 0;
            min-height: 560px;
            display: flex;
            flex-direction: column;
        }

        .product-image {
            width: 100%;
            height: auto;
            display: block;
        }

        .product-body {
            padding: 32px 32px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-name {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .product-subtitle {
            margin: 0 0 18px;
            font-weight: 700;
            color: #455a70;
            font-size: 17px;
            letter-spacing: 0.01em;
        }

        .product-description {
            margin: 0 0 12px;
            color: #455a70;
            line-height: 1.5;
        }

        .product-more-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: flex-start;
            width: auto;
            max-width: 100%;
            background: transparent;
            border: 1px solid #5ea1e1;
            color: #5ea1e1;
            border-radius: 8px;
            padding: 14px 20px;
            font-weight: 700;
            text-decoration: none;
            margin-top: auto;
            line-height: 1;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .product-more-btn:hover {
            background-color: #5ea1e1;
            color: #ffffff;
        }

        /* ── Product detail page ── */
        .detail-wrap {
            display: flex;
            flex-direction: row;
            gap: 32px;
            align-items: flex-start;
            max-width: 980px;
            margin: 0 auto;
        }

        .detail-image {
            flex: 0 0 40%;
            max-width: 40%;
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
            object-fit: cover;
        }

        .detail-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .detail-name {
            margin: 0 0 8px;
            font-size: clamp(22px, 4vw, 32px);
            color: #11253a;
        }

        .detail-subtitle {
            margin: 0 0 18px;
            font-size: 17px;
            font-weight: 700;
            color: #455a70;
        }

        .detail-description {
            margin: 0 0 14px;
            color: #455a70;
            line-height: 1.6;
            flex: 1;
        }

        .detail-back-btn {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            gap: 6px;
            margin-top: 28px;
            background: transparent;
            border: 1px solid #5ea1e1;
            color: #5ea1e1;
            border-radius: 8px;
            padding: 11px 20px;
            font-weight: 700;
            text-decoration: none;
            line-height: 1;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .detail-back-btn:hover {
            background-color: #5ea1e1;
            color: #ffffff;
        }

        @media (max-width: 700px) {
            .detail-wrap {
                flex-direction: column;
            }

            .detail-image {
                flex: none;
                max-width: 100%;
                width: 100%;
            }
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
            <img class="logo" src="./logo.png" alt="Logo">
            <img class="logo-kabi" src="./kabi-test.png" alt="Kabi-Test">
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





            <div class="temporary" style="background: gold; color: navy; padding: 18px; border-radius: 8px; max-width: 760px; margin: 32px auto;">
                <h1>Pričakujemo</h1>
                <ul>
                    <li>osnovno poznavanje HTML, CSS in Bootrstrap</li>
                    <li>osnovno poznavanje Javascript (jQuery, vanilla javascript)</li>
                    <li>poznavanje logike CMS sistemov</li>
                    <li>poznavanje programskega jezika PHP</li>
                </ul>
</div>
<div class="temporary" style="background: navy; color: gold; padding: 18px; border-radius: 8px; max-width: 760px; margin: 32px auto;">
                <h2>Zaželena znanja (ki niso pogoj)</h2>
                <ul>
                    <li>poznavanje modernih smernic spletnega razvoja in odzivnega (responsive) dizajna</li>
                    <li>poznavanje modernih tehnologij za frontend (CSS, SCSS, HTML, responsive design, Bootstrap, jQuery, React, Angular, VueJs) je prednost</li>
                    <li>poznavanje SEO smernic je prednost</li>
                </ul>
</div>
<div class="temporary" style="background: gold; color: navy; padding: 18px; border-radius: 8px; max-width: 760px; margin: 32px auto;">
                <h2>Zahteve naloge</h2>
                <ul>
                    <li>V PHP-u (+CSS/SCSS, JS) narediti stran in podstran na podlagi dizajna</li>
                    <li>Stran s seznamom izdelkov naj vsebuje 5 izdelkov</li>
                    <li>Klik na gumb "več" pri izdelkih odpre podstran s prikazom enega izdelka</li>
                    <li>Uporabijo se lahko različni frameworki in knjižnice (PHP, CSS in JS)</li>
                    <li>Lahko se uporabi tudi kakšen template sistem</li>
                    <li>Stran naj bo responsive, prilagojen prikaz na mobitelih &lt;500px (na tablicah ni treba)</li>
                </ul>
            </div>
</body>
</html>
HTML;

    return new Response($html);
}

function loadProductsFromJson(string $jsonPath): array
{
    if (!is_file($jsonPath) || !is_readable($jsonPath)) {
        return [];
    }

    $json = file_get_contents($jsonPath);
    if ($json === false) {
        return [];
    }

    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        return [];
    }

    $products = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) {
            continue;
        }

        $rawDesc = $item['description'] ?? '';
        $paragraphs = is_array($rawDesc)
            ? array_values(array_filter($rawDesc, 'is_string'))
            : [(string) $rawDesc];

        $products[] = [
            'name'        => (string) ($item['name'] ?? 'Izdelek'),
            'subtitle'    => (string) ($item['subtitle'] ?? ''),
            'description' => $paragraphs,
            'image'       => (string) ($item['image'] ?? ''),
        ];
    }

    return array_slice($products, 0, 5);
}

function renderProductsContent(array $products): string
{
    if ($products === []) {
        return '
            <section class="placeholder-card">
                <h1>Izdelki</h1>
                <p>Podatki o izdelkih niso na voljo.</p>
            </section>
        ';
    }

    $cards = '';
    $basePath = getBasePath();
    foreach ($products as $index => $product) {
        $serialNumber = $index + 1;
        $rawImage = $product['image'] !== ''
            ? $product['image']
            : $basePath . '/public/izdelek-' . $serialNumber . '.png';
        $imageSrc = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
        $name        = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
        $subtitle    = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
        $descHtml    = '';
        foreach ($product['description'] as $para) {
            $descHtml .= '<p class="product-description">' . htmlspecialchars($para, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $detailHref = htmlspecialchars($basePath . '/public/izdelek/' . $serialNumber, ENT_QUOTES, 'UTF-8');

        $cards .= '
            <article class="product-card">
                <img class="product-image" src="' . $imageSrc . '" alt="Izdelek ' . $serialNumber . '">
                <div class="product-body">
                    <h2 class="product-name">' . $name . '</h2>
                    <h3 class="product-subtitle">' . $subtitle . '</h3>
                    ' . $descHtml . '
                    <a class="product-more-btn" href="' . $detailHref . '">+ VEČ O IZDELKU ' . $serialNumber . '</a>
                </div>
            </article>
        ';
    }

    return '
        <section class="products-wrap">
            <div class="products-grid">' . $cards . '</div>
        </section>
    ';
}

function renderProductDetailContent(array $product, int $serialNumber): string
{
    $basePath = getBasePath();
    $rawImage = $product['image'] !== ''
        ? $product['image']
        : $basePath . '/public/izdelek-' . $serialNumber . '.png';

    $imageSrc  = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
    $name      = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
    $subtitle  = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
    $backHref  = htmlspecialchars($basePath . '/public/izdelki', ENT_QUOTES, 'UTF-8');

    $descHtml = '';
    foreach ($product['description'] as $para) {
        $descHtml .= '<p class="detail-description">' . htmlspecialchars($para, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    return '
        <div class="detail-wrap">
            <img class="detail-image" src="' . $imageSrc . '" alt="' . $name . '">
            <div class="detail-body">
                <h1 class="detail-name">' . $name . '</h1>
                <h2 class="detail-subtitle">' . $subtitle . '</h2>
                ' . $descHtml . '
                <a class="detail-back-btn" href="' . $backHref . '">&#8592; NAZAJ NA SEZNAM</a>
            </div>
        </div>
    ';
}

// Create routes
$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => static function () {
        $homeImageSrc = htmlspecialchars(getBasePath() . '/public/Copilot_20260402_230309.png', ENT_QUOTES, 'UTF-8');

        return renderLayout('Domov', 'home', '
            <section class="" style="text-align:center;">
                <img src="' . $homeImageSrc . '" alt="Copilot 20260402 230309" style="display:block; width:min(100%, 900px); height:auto; margin:14px auto 0; border-radius:8px;">
            </section>
        ');
    },
]));



$routes->add('products', new Route('/products', [
    '_controller' => static function () {
        $products = loadProductsFromJson(__DIR__ . '/../data/products.json');
        return renderLayout('Izdelki', 'products', renderProductsContent($products));
    },
]));

$routes->add('products_sl', new Route('/izdelki', [
    '_controller' => static function () {
        $products = loadProductsFromJson(__DIR__ . '/../data/products.json');
        return renderLayout('Izdelki', 'products', renderProductsContent($products));
    },
]));

$detailRoute = new Route('/izdelek/{id}', [
    '_controller' => static function (string $id) {
        $id = (int) $id;
        $products = loadProductsFromJson(__DIR__ . '/../data/products.json');
        $index = $id - 1;
        if ($index < 0 || $index >= count($products)) {
            return renderLayout('404 – Izdelek ni najden', 'products', '
                <section style="text-align:center; padding: 48px 0;">
                    <h1>Izdelek ni bil najden</h1>
                    <a href="' . htmlspecialchars(getBasePath() . '/public/izdelki', ENT_QUOTES, 'UTF-8') . '" style="color:#5ea1e1; font-weight:700;">← Nazaj na seznam</a>
                </section>
            ');
        }
        $product = $products[$index];
        return renderLayout(
            htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
            'products',
            renderProductDetailContent($product, $id)
        );
    },
    'id' => 1,
]);
$detailRoute->setRequirements(['id' => '\d+']);
$routes->add('product_detail', $detailRoute);

// Handle request
$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controller = $parameters['_controller'];
    $routeParams = array_filter(
        $parameters,
        static fn($key) => $key !== '_controller' && $key !== '_route',
        ARRAY_FILTER_USE_KEY
    );
    $response = \call_user_func($controller, ...(empty($routeParams) ? [] : array_values($routeParams)));
} catch (\Throwable $e) {
    $response = renderLayout('404 – Stran ni najdena', '', '
        <section style="text-align:center; padding: 48px 0;">
            <div style="font-size: clamp(80px, 16vw, 160px); font-weight: 900; line-height: 1;
                        background: linear-gradient(110deg, #1565c0, #2e7d32);
                        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
                        background-clip: text;">
                404
            </div>
            <h1 style="margin: 16px 0 8px; font-size: clamp(20px, 4vw, 32px); color: #11253a;">
                Stran ni bila najdena
            </h1>
            <p style="color: #556; margin: 0 0 28px; font-size: 16px;">
                Naslov, ki ste ga vnesli, ne obstaja ali je bil premaknjen.
            </p>
            <a href="' . getBasePath() . '/public" style="
                display: inline-block;
                background: linear-gradient(110deg, #1565c0, #2e7d32);
                color: #fff;
                font-weight: 700;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 8px;
                font-size: 15px;
                letter-spacing: 0.03em;
                transition: opacity 0.2s;
            " onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">
                ← Nazaj na domačo stran
            </a>
        </section>
    ');
    $response->setStatusCode(404);
}

$response->send();
