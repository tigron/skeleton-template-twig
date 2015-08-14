# skeleton-template-twig

## Description

Twig templating for Skeleton.

## Installation

Installation via composer:

    composer require tigron/skeleton-template-twig

## Howto

Load the library, point it to your template directory, assign a variable if you
need to and go!

    <?php
    $renderer = new \Skeleton\Template\Twig\Twig();
    $renderer->set_template_directory('/path/to/my/templates/');
    $renderer->assign('my_variable', 'some value');

    // Optional translation support (requires skeleton-i18n)
    $renderer->set_translation($skeleton_i18n_translation_instance);

    echo $renderer->render('template.twig');
