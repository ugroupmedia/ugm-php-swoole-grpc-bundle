<?php

declare(strict_types=1);

interface ServiceInterface
{
}
interface FooServiceInterface extends ServiceInterface
{
    public function GetFoo();
    public function ListFoo();
}
interface BarServiceInterface extends ServiceInterface
{
    public function GetBar();
    public function ListBar();
}
abstract class AbstractFooService implements FooServiceInterface, BarServiceInterface
{
    public function GetFoo()
    {
        // TODO: Implement GetFoo() method.
    }

    public function ListFoo()
    {
        // TODO: Implement ListFoo() method.
    }

    public function GetBar()
    {
        // TODO: Implement GetBar() method.
    }

    public function ListBar()
    {
        // TODO: Implement ListBar() method.
    }
}
class FooService extends AbstractFooService
{

}

$reflection = new ReflectionClass(FooService::class);
foreach ($reflection->getInterfaces() as $interface) {
    if ($interface->implementsInterface(ServiceInterface::class)) {
        foreach ($interface->getMethods() as $method) {
            var_dump($method->getName());
        }
    }
    echo "Implements interface?\n";
    var_dump($interface->implementsInterface(ServiceInterface::class));
    echo "Interfaces?\n";
    var_dump($interface->getInterfaces());
}