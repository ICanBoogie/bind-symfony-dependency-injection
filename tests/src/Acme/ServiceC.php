<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme;

class ServiceC
{
    public function __construct(ServiceA $a, ServiceB $b)
    {
    }
}
