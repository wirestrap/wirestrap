<?php

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;
use Rector\ValueObject\PhpVersion;

const ANALYSIS_PATHS = [
    __DIR__ . '/src/',
];

return RectorConfig::configure()
    ->withPaths(ANALYSIS_PATHS)
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withParallel()
    ->withSkip([
        SafeDeclareStrictTypesRector::class,
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true,
    );
