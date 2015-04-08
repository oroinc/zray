<?php

$zre = new \ZRayExtension("Oro");

$zre->setMetadata(array(
	'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'logo.png',
));

$zre->setEnabledAfter('Oro\Bundle\DistributionBundle\OroKernel::registerBundles');

$zre->traceFunction("Symfony\Component\HttpKernel\Kernel::terminate", function(){}, function($context, &$storage) {
    $applicationContext = $context['this'];
    $container = $applicationContext->getContainer();
    $cm                    = $container->get('oro_config.global');
    $settingsFromAppConfig = $container->get('oro_config.config_definition_bag');
    $isInstalled = $container->hasParameter('installed') && $container->getParameter('installed');

    $currentConfiguration = [];

    if ($isInstalled) {
        foreach ($settingsFromAppConfig->all() as $bundleAlias => $settings) {
            foreach (array_keys($settings) as $settingName) {
                $key = implode('.', [$bundleAlias, $settingName]);
                $currentConfiguration[$key] = $cm->get($key);
            }
        }
    } else {
        $currentConfiguration['installed'] = false;
    }

    $storage['OroConfiuration'][] = $currentConfiguration;
});