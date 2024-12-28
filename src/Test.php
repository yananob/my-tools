<?php

declare(strict_types=1);

namespace yananob\MyTools;

final class Test
{
    public static function invokePrivateMethod($object, string $methodName, ...$args): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($object, ...$args);
    }
}
