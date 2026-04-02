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
        ['label' => '<span style="font-size:1.4em;line-height:1;position:relative;top:1px;">⌂</span> Domov',    'href' => $base . '/public',            'key' => 'home'],
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

        .product-desc-accordion {
            display: none;
            margin: 0 0 12px;
        }

        .product-desc-summary {
            cursor: pointer;
            font-weight: 700;
            color: #455a70;
            font-size: 15px;
            padding: 6px 0;
            user-select: none;
            list-style: none;
        }

        .product-desc-summary::-webkit-details-marker {
            display: none;
        }

        .product-desc-summary::before {
            content: '';
            display: inline-block;
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-left: 8px solid #455a70;
            margin-right: 6px;
            vertical-align: middle;
            transition: transform 0.2s ease;
        }

        details[open] .product-desc-summary::before {
            transform: rotate(90deg);
        }

        @media (max-width: 480px) {
            .product-card {
                min-height: auto;
            }

            .product-desc-full {
                display: none;
            }

            .product-desc-accordion {
                display: block;
            }

            .product-more-btn {
                margin-top: 10px;
            }
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
            .logo-row {
                padding-left: 12px;
                padding-right: 12px;
            }

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
            <a href="{$base}/public" style="display:flex;align-items:center;gap:12px;cursor:pointer;text-decoration:none;">
                <img class="logo" src="{$base}/public/logo.png" alt="Logo">
                <img class="logo-kabi" src="{$base}/public/kabi-test.png" alt="Kabi-Test">
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
