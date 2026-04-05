<?php

use Symfony\Component\HttpFoundation\Response;

function renderHomePage(): Response
{
    $homeImageSrc = htmlspecialchars(getBasePath() . '/public/Copilot_20260402_230309.jpg', ENT_QUOTES, 'UTF-8');

    return renderLayout('Domov', 'home', '
        <section style="text-align:center;">
            <img src="' . $homeImageSrc . '" alt="Copilot 20260402 230309"
                 style="display:block; width:min(100%, 900px); height:auto; margin:14px auto 0; border-radius:8px;">
        </section>
    ');
}
