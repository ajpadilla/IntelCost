<?php


namespace IntelCost\Apps\Furniture\Frontend\Controllers;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;

class BaseController
{
    const TEMPLATE_DIRECTORY = "/app/apps/furniture/frontend/templates";


    protected function makeView($templateFileToLoad, array &$params, $fileNameEndsWith = ".html.twig")
    {
        $path = static::TEMPLATE_DIRECTORY;
        $fileFullName = $templateFileToLoad . $fileNameEndsWith;

        $twig = $this->getTwig($path, [
            "debug" => true
        ]);

        $twig->addExtension(new DebugExtension());
        try {
            echo $twig->render($fileFullName, $params);
        } catch (LoaderError $e) {
            echo $e->getMessage();
        } catch (RuntimeError $e) {
            echo $e->getMessage();
        } catch (SyntaxError $e) {
            echo $e->getMessage();
        }
    }

    protected function getTwig($path, array $options = null)
    {

        $loader = new FilesystemLoader($path);

        return new Environment($loader, $options);
    }
}