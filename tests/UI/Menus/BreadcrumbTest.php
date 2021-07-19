<?php

/**
 * Test sur le fil d'ariane
 */

namespace App\Tests\UI\Menus;
 
use Twig\Environment as Twig;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\UI\Menus\Breadcrumb\{
    BreadcrumbItem,
    Breadcrumb
};
use App\Tests\BaseTestCase;

final class BreadcrumbTest extends BaseTestCase {
 
    /**
     * Test lorsque le fil d'ariane n'a pas assez d'éléments
     * @return void
     */
    public function testNotEnoughElements() : void
    {
        $emptyBreadcrumb = new Breadcrumb();
        $oneElementBreadcrumbAnchor = (new Breadcrumb)
            ->addItem(new BreadcrumbItem('Label 1', 'Alt Label 1', 'app_testing_default_index'))
        ;
        $oneElementBreadcrumbText = (new Breadcrumb)
            ->addItem(new BreadcrumbItem('Label 1', 'Alt Label 1'))
        ;

        $breadcrumbs = [ 
            $emptyBreadcrumb,
            $oneElementBreadcrumbAnchor,
            $oneElementBreadcrumbText,
        ];
        
        foreach($breadcrumbs as $breadcrumb)
        {
            $this->assertEquals('', $this->getBreadcrumbRender($breadcrumb));
        }
        
    }

    /**
     * Test avec un fil d'ariane à plusieurs éléments
     * @return void
     */
    public function testWithFewElements() : void
    {
        $routeName = 'app_testing_default_params';
        $itemTwoUrl = $this->getService(RouterInterface::class)->generate($routeName, [
            'param1' => 'new-value-1',
        ], referenceType: UrlGeneratorInterface::ABSOLUTE_URL);

        $breadcrumb = (new Breadcrumb())
            ->addItem(new BreadcrumbItem('Label 1', 'Alt Label 1'))
            ->addItem(new BreadcrumbItem('Label 2', 'Alt Label 2', $routeName, [
                'param1' => 'new-value-1',
            ]))
            ->addItem(new BreadcrumbItem('Label 3', 'Alt Label 3', $routeName, [
                'param1' => 'new-value-2',
            ]))
        ;

        $expected = 
            '<ul id="breadcrumb">' . 
                '<li>Label 1</li>' . 
                '<li><a href="' . $itemTwoUrl . '" title="Alt Label 2">Label 2</a></li>' .
                '<li>Label 3</li>' .
            '</ul>'
        ;
        $render = $this->getBreadcrumbRender($breadcrumb);

        $this->assertEquals($expected, $render);
    }

    /**
     * Retourne le rendu du fil d'ariane
     * @param Breadcrumb $breadcrumb
     * @return ?string
     */
    private function getBreadcrumbRender(Breadcrumb $breadcrumb) : ?string
    {
        return $this->getService(Twig::class)->render('testing/ui/menus/breadcrumb.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}