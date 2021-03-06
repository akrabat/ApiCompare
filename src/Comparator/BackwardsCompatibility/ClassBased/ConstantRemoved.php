<?php

declare(strict_types=1);

namespace Roave\ApiCompare\Comparator\BackwardsCompatibility\ClassBased;

use Assert\Assert;
use Roave\ApiCompare\Change;
use Roave\ApiCompare\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class ConstantRemoved implements ClassBased
{
    public function compare(ReflectionClass $fromClass, ReflectionClass $toClass) : Changes
    {
        Assert::that($fromClass->getName())->same($toClass->getName());

        $removedConstants = array_diff_key(
            $this->accessibleConstants($fromClass),
            $this->accessibleConstants($toClass)
        );

        return Changes::fromArray(array_values(array_map(function (ReflectionClassConstant $constant) use ($fromClass) : Change {
            return Change::removed(
                sprintf('Constant %s::%s was removed', $fromClass->getName(), $constant->getName()),
                true
            );
        }, $removedConstants)));
    }

    /** @return ReflectionClassConstant[] */
    private function accessibleConstants(ReflectionClass $class) : array
    {
        return array_filter($class->getReflectionConstants(), function (ReflectionClassConstant $constant) : bool {
            return $constant->isPublic() || $constant->isProtected();
        });
    }
}
