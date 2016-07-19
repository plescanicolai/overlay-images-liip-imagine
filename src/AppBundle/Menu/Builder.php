<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('Latest Blog Post', array(
            'route' => 'last_bloc',
        ));
        $menu->addChild('About Me', array('route' => 'homepage'));
        $menu['About Me']->addChild('Edit profile', array('route' => 'homepage'));

        return $menu;
    }
}