<?php

namespace App\Controller\Admin;

use App\Entity\Official;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Survos\EzBundle\Controller\BaseCrudController;

class OfficialCrudController extends BaseCrudController
{
    private $seen = [];

    public static function getEntityFqcn(): string
    {
        return Official::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield $this->push(IdField::new('id'));
        yield $this->push(TextField::new('officialName'));
        yield TextField::new('gender');
        yield TextField::new('currentParty');
        foreach (parent::configureFields($pageName) as $field) {
            if ($field = $this->push($field)) {
                yield $field;
            }
            // ignore what we've already seen
        }
    }
}
