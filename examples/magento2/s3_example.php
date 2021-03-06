<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Deployer;

import('recipe/magento2.php');
import(__DIR__ . '/../../vendor/autoload.php'); // autoload your project dependencies...
import('recipe/artifact.php'); // ... include the base recipe
import('recipe/artifact_s3.php'); // ... deploy using S3

// Set the full URI to the S3 object that you want to download to the server
// (you could also supply this as an argument: dep deploy -o target=$(pwd)/artifacts/build.zip
set('target', 's3://my-bucket/my/object/key.zip');

set('deploy_path', '~/your-webroot');

// Redefine deploy procedure to avoid tasks we don't need from the magento2 recipe
task('deploy', [
    // Being [deploy:prepare] replacements
    //'artifact:info', // instead of deploy:info
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'deploy:artifact', // instead of deploy:update_code
    'deploy:shared',
    'deploy:writable',
    // End [deploy:prepare] replacements
    /*
     * The artifact is expected to already have compiled DI and Assets, so the following tasks are not usually needed:
     *   - deploy:vendors
     *   - deploy:clear_paths
     *   - deploy:magento
     *     - magento:build
     *       - magento:compile
     *       - magento:sync:content_version
     *       - magento:deploy:assets
     */
    // Being [deploy:magento] replacements
    'magento:config:import',
    'magento:upgrade:db',
    'magento:cache:flush',
    // End [deploy:magento] replacements
    'deploy:publish',
]);

after('deploy:symlink', 'magento:maintenance:disable');

localhost(); // define your hosts