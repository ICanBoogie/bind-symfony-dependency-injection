<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme;

class ServiceB
{
    public function __construct(ServiceA $a)
    {
    }
}
