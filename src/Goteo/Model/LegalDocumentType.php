<?php

namespace Goteo\Model;

use Goteo\Library\Text;

use Goteo\Core\Model;
use Goteo\Model\LegalDocument\NIE;
use Goteo\Model\LegalDocument\CIF;
use Goteo\Model\LegalDocument\NIF;
use Goteo\Model\LegalDocument\VAT;
use Goteo\Model\LegalDocument\PASSPORT;

abstract class LegalDocumentType {

    const CIF = 'cif';
    const NIF = 'nif';
    const NIE = 'nie';
    const VAT = 'vat';
    const PASSPORT = 'passport';
    const LEGAL_DOCUMENTS = [self::CIF, self::NIF, self::NIE];

    private string $document_type;

    public static function create(string $document_type): LegalDocument {
        $legal_document_type;

        switch ($document_type) {
            case self::NIE:
                $legal_document_type = new NIE();
            case self::CIF:
                $legal_document_type = new CIF();
            case self::NIF:
                $legal_document_type = new NIF();
            case self::VAT:
                $legal_document_type = new VAT();
            case self::PASSPORT:
                $legal_document_type = new PASSPORT();
            default:
                $legal_document_type = new NIF();
        }

        return $legal_document_type;
    }

    public function getDocumentType(): string {
        return $this->document_type;
    }
    
    static public function getLegalDocumentTypes() {
        return  [
            self::CIF => Text::get('donor-legal-document-type-cif'),
            self::NIF => Text::get('donor-legal-document-type-nif'),
            self::NIE => Text::get('donor-legal-document-type-nie'),
            self::VAT => Text::get('donor-legal-document-type-vat')
            ];
    }

}