<?php

use Symfony\Component\HttpFoundation\Response;

function renderNotFoundPage(): Response
{
    $response = renderLayout('404 – Stran ni najdena', '', '
        <section style="text-align:center; padding: 48px 0;">
            <div style="font-size: clamp(80px, 16vw, 160px); font-weight: 900; line-height: 1;
                        background: linear-gradient(110deg, #5ea1e1, #6f87d7);
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
            <a class="detail-back-btn" href="' . getBasePath() . '/public" style="margin-top:0;">
                ← Nazaj na domačo stran
            </a>
        </section>
    ');
    $response->setStatusCode(404);
    return $response;
}
