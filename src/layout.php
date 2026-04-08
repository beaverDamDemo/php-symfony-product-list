<?php

use Symfony\Component\HttpFoundation\Response;

function getBasePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

    if (str_ends_with($scriptName, '/public/index.php')) {
        return rtrim(substr($scriptName, 0, -strlen('/public/index.php')), '/');
    }

    if (str_ends_with($scriptName, '/index.php')) {
        return rtrim(substr($scriptName, 0, -strlen('/index.php')), '/');
    }

    return '';
}

function appUrl(string $path = ''): string
{
    $base = getBasePath();

    if ($path === '') {
        return $base !== '' ? $base : '/';
    }

    return ($base !== '' ? $base : '') . '/' . ltrim($path, '/');
}

function isPublicDocumentRoot(): bool
{
    $documentRoot = str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));

    return str_ends_with(rtrim($documentRoot, '/'), '/public');
}

function assetBasePath(): string
{
    if (isPublicDocumentRoot()) {
        return appUrl();
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

    // XAMPP/root-docroot mode routes through /public/index.php.
    // Docker/public-docroot mode uses /index.php directly.
    if (str_ends_with($scriptName, '/public/index.php')) {
        return appUrl('/public');
    }

    return appUrl();
}

function routeBasePath(): string
{
    if (isPublicDocumentRoot()) {
        return appUrl();
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

    // When app is entered via /public/index.php (common in XAMPP setup),
    // keep route URLs under /public so navigation stays inside the app.
    if (str_ends_with($scriptName, '/public/index.php')) {
        return appUrl('/public');
    }

    return appUrl();
}

function routeUrl(string $path = ''): string
{
    $base = routeBasePath();

    if ($path === '') {
        return $base !== '' ? $base : '/';
    }

    return ($base !== '' ? $base : '') . '/' . ltrim($path, '/');
}

function assetUrl(string $path): string
{
    $normalized = preg_replace('#^/?public/#', '', ltrim($path, '/'));

    return rtrim(assetBasePath(), '/') . '/' . $normalized;
}

function renderLayout(string $title, string $activeKey, string $contentHtml): Response
{
    $base = getBasePath();
    $icon180 = htmlspecialchars(assetUrl('/tinified/logo.png'), ENT_QUOTES, 'UTF-8');
    $icon32 = htmlspecialchars(assetUrl('/tinified/logo.png'), ENT_QUOTES, 'UTF-8');
    $icon16 = htmlspecialchars(assetUrl('/tinified/logo.png'), ENT_QUOTES, 'UTF-8');
    $stylesheet = htmlspecialchars(assetUrl('/styles/app.css'), ENT_QUOTES, 'UTF-8');
    $logo = htmlspecialchars(assetUrl('/tinified/logo.png'), ENT_QUOTES, 'UTF-8');
    $logoKabi = htmlspecialchars(assetUrl('/tinified/kabi-test.png'), ENT_QUOTES, 'UTF-8');

    $navLinks = [
        ['label' => '<span class="nav-home-icon" aria-hidden="true">⌂</span> Domov', 'href' => routeUrl(),             'key' => 'home'],
        ['label' => 'O nas',      'href' => routeUrl('/o-nas'),      'key' => 'about'],
        ['label' => 'Kontakt',    'href' => routeUrl('/kontakt'),    'key' => 'contact'],
        ['label' => 'Pišite nam', 'href' => routeUrl('/pisite-nam'), 'key' => 'write'],
        ['label' => 'IZDELKI',    'href' => routeUrl('/izdelki'),    'key' => 'products'],
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
    $homeHref = htmlspecialchars(routeUrl(), ENT_QUOTES, 'UTF-8');

    $html = <<<HTML
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="{$icon180}">
    <link rel="icon" type="image/png" sizes="32x32" href="{$icon32}">
    <link rel="icon" type="image/png" sizes="16x16" href="{$icon16}">
    <link rel="stylesheet" href="{$stylesheet}">
    <title>{$safeTitle}</title>
</head>
<body>
    <div class="site-shell">
        <div class="logo-row row-inner">
            <a class="brand-link" href="{$homeHref}">
                <img class="logo" src="{$logo}" alt="Logo">
                <img class="logo-kabi" src="{$logoKabi}" alt="Kabi-Test">
            </a>
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

    <div class="temporary temporary--gold">
        <h1>Pričakujemo</h1>
        <ul>
            <li>osnovno poznavanje HTML, CSS in Bootrstrap</li>
            <li>osnovno poznavanje Javascript (jQuery, vanilla javascript)</li>
            <li>poznavanje logike CMS sistemov</li>
            <li>poznavanje programskega jezika PHP</li>
        </ul>
    </div>
    <div class="temporary temporary--navy">
        <h2>Zaželena znanja (ki niso pogoj)</h2>
        <ul>
            <li>poznavanje modernih smernic spletnega razvoja in odzivnega (responsive) dizajna</li>
            <li>poznavanje modernih tehnologij za frontend (CSS, SCSS, HTML, responsive design, Bootstrap, jQuery, React, Angular, VueJs) je prednost</li>
            <li>poznavanje SEO smernic je prednost</li>
        </ul>
    </div>
    <div class="temporary temporary--gold">
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
