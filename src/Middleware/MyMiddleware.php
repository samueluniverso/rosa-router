<?php

namespace Rockberpro\RestRouter\Middleware;

class MyMiddleware
{
    public function handle()
    {
        print('MyMiddleware');
    }
}