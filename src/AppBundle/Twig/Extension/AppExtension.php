<?php

namespace AppBundle\Twig\Extension;

use Symfony\Bridge\Doctrine\RegistryInterface;

class AppExtension extends \Twig_Extension
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('contain', array($this, 'containFunction')),
            new \Twig_SimpleFunction('getApps', array($this, 'getAppsFunction')),
        );
    }

    /**
     * Retourne true si $search à été trouvé dans $string
     *
     * @param string $string Chaine dans laquelle rechercher
     * @param array $search Chaine à rechercher
     * @return bool
     */
    public function containFunction($string, $search = array())
    {
        foreach ($search as $item) {
            if (substr_count($string, $item)) {
                return true;
            }
        }
        return false;
    }

    public function getAppsFunction()
    {
        return $this->doctrine->getRepository('AppBundle:Application')->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }
}
