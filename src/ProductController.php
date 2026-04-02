<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

class ProductController
{
    public function list()
    {
        return new Response('Product list will be here');
    }
}
