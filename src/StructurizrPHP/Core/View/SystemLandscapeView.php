<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\StructurizrPHP\Core\View;

use StructurizrPHP\StructurizrPHP\Core\Model\Model;

final class SystemLandscapeView extends StaticView
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var bool
     */
    private $enterpriseBoundaryVisible = true;

    public function __construct(Model $model, string $description, string $key, ViewSet $viewSet)
    {
        parent::__construct(null, $description, $key, $viewSet);
        $this->model = $model;
    }

    protected function getModel(): ?Model
    {
        return $this->model;
    }

    public function addAllElements(): void
    {
        $this->addAllSoftwareSystems();
        $this->addAllPeople();
    }

    public function toArray() : array
    {
        return \array_merge(
            [
                'enterpriseBoundaryVisible' => $this->enterpriseBoundaryVisible,
            ],
            parent::toArray()
        );
    }

    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress PossiblyNullReference
     */
    public static function hydrate(array $viewData, ViewSet $viewSet) : self
    {
        $view = new SystemLandscapeView(
            $viewSet->model(),
            $viewData['description'],
            $viewData['key'],
            $viewSet
        );

        if (isset($viewData['title'])) {
            $view->setTitle($viewData['title']);
        }

        if ($viewData['paperSize']) {
            $view->setPaperSize(PaperSize::hydrate($viewData['paperSize']));
        }

        foreach ($viewData['elements'] as $elementData) {
            $elementView = $view->addElement($viewSet->model()->getElement($elementData['id']), false);

            if (isset($elementData['x']) && isset($elementData['y'])) {
                $elementView
                    ->setX((int) $elementData['x'])
                    ->setY((int) $elementData['y']);
            }
        }

        if (isset($viewData['relationships'])) {
            foreach ($viewData['relationships'] as $relationshipData) {
                $relationshipView = RelationshipView::hydrate(
                    $view->getModel()->getRelationship($relationshipData['id']),
                    $relationshipData
                );

                $view->relationshipsViews[] = $relationshipView;
            }
        }

        if (isset($viewData['automaticLayout'])) {
            $view->automaticLayout = AutomaticLayout::hydrate($viewData['automaticLayout']);
        }

        return $view;
    }
}
