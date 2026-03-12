<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/app',
            __DIR__ . '/lib',
         
        ]
    );

    $rectorConfig->skip(
        [
            ClassPropertyAssignToConstructorPromotionRector::class,
            \Rector\Php81\Rector\Property\ReadOnlyPropertyRector::class,
            \Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector::class,
            \Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector::class,
            \Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector::class,
            \Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector::class,
        ]
    );

    // do not add backslashes or use statements
    $rectorConfig->importNames(false);
    $rectorConfig->importShortClasses(false);

    // register a single rule
    $rectorConfig->rule(\Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector::class);

    // define sets of rules
    $rectorConfig->sets(
        [
            \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_85,
            \Rector\Set\ValueObject\SetList::DEAD_CODE,
            \Rector\Set\ValueObject\SetList::CODE_QUALITY,
            \Rector\Set\ValueObject\SetList::TYPE_DECLARATION,
        ]
    );
};
