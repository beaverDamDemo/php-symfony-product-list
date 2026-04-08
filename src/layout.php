<?php

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
        ['label' => '<span class="nav-home-icon" aria-hidden="true">⌂</span> Domov', 'href' => $base . '/public',            'key' => 'home'],
        ['label' => 'O nas',      'href' => $base . '/public/o-nas',      'key' => 'about'],
        ['label' => 'Kontakt',    'href' => $base . '/public/kontakt',    'key' => 'contact'],
        ['label' => 'Pišite nam', 'href' => $base . '/public/pisite-nam', 'key' => 'write'],
        ['label' => 'IZDELKI',    'href' => $base . '/public/izdelki',    'key' => 'products'],
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
    <link rel="apple-touch-icon" sizes="180x180" href="{$base}/public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{$base}/public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{$base}/public/favicon_io/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="{$base}/public/favicon_io/favicon.ico">
    <link rel="manifest" href="{$base}/public/favicon_io/site.webmanifest">
    <link rel="stylesheet" href="{$base}/public/styles/app.css">
    <title>{$safeTitle}</title>
</head>
<body>
    <div class="site-shell">
        <div class="logo-row row-inner">
            <a class="brand-link" href="{$base}/public">
                <img class="logo" src="{$base}/public/tinified/logo.png" alt="Logo">
                <img class="logo-kabi" src="{$base}/public/tinified/kabi-test.png" alt="Kabi-Test">
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
