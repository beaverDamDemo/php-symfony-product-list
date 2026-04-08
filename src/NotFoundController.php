<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Response;

final class NotFoundController
{
    public function index(): Response
    {
        $response = renderLayout('404 – Stran ni najdena', '', '
            <section class="not-found-page">
                <div class="not-found-code">
                    404
                </div>
                <h1 class="not-found-title">
                    Stran ni bila najdena
                </h1>
                <p class="not-found-text">
                    Naslov, ki ste ga vnesli, ne obstaja ali je bil premaknjen.
                </p>
                <a class="detail-back-btn not-found-link" href="' . getBasePath() . '/public">
                    ← Nazaj na domačo stran
                </a>
            </section>
        ');
        $response->setStatusCode(404);

        return $response;
    }
}
