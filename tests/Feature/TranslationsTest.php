<?php

use Mozex\Modules\Scouts\TranslationsScout;

it('can load translations', function () {
    $loader = app('translator')->getLoader();

    TranslationsScout::create()->collect()
        ->each(function (array $asset) use ($loader) {
            expect($loader->namespaces())->toHaveKey($asset['module'])->toContain($asset['path'])
                ->and($loader->jsonPaths())->toContain($asset['path']);
        });
});
