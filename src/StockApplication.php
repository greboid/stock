<?php
    declare(strict_types=1);

    namespace greboid\stock;

    class StockApplication extends \Silex\Application {
        use \Silex\Application\TwigTrait;
        use \Silex\Application\SecurityTrait;
        use \Silex\Application\FormTrait;
        use \Silex\Application\UrlGeneratorTrait;
        use \Silex\Application\SwiftmailerTrait;
        use \Silex\Application\MonologTrait;
        use \Silex\Application\TranslationTrait;
    }
