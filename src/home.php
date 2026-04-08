<?php

use Symfony\Component\HttpFoundation\Response;

function renderHomePage(): Response
{
    $homeImageSrc = htmlspecialchars(getBasePath() . '/public/Copilot_20260402_230309.jpg', ENT_QUOTES, 'UTF-8');

    return renderLayout('Domov', 'home', '
        <section class="home-hero">
            <img class="home-hero-image" src="' . $homeImageSrc . '" alt="Copilot 20260402 230309">
        </section>
    ');
}
