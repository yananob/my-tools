<?php

declare(strict_types=1);

namespace yananob\MyTools;

class Test
{
    // @deprecated
    public static function invokePrivateMethod($object, string $methodName, ...$args): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($object, ...$args);
    }
}
