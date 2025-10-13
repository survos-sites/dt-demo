<?php

namespace App\Controller\Admin;

use App\Entity\Instrument;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Survos\CoreBundle\Controller\BaseCrudController;

class InstrumentCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Instrument::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield $this->push(IdField::new('id'))->onlyOnDetail();
        yield $this->push(TextField::new('name'));
        yield $this->push(TextField::new('type'));
        yield $this->push(TextField::new('snippet'));
        foreach (parent::configureFields($pageName) as $field) {
            if ($field = $this->push($field)) {
                yield $field;
            }
            // ignore what we've already seen
        }
    }
}
