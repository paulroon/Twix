<?php

namespace Twix\Interfaces;

interface Router
{
    public function dispatch(Request $request): Response;
}
