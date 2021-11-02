<?php

namespace Goteo\Model;

use Goteo\Library\Text;

use Goteo\Core\Model;
use Goteo\Model\LegalDocumentType\NIE;
use Goteo\Model\LegalDocumentType\CIF;
use Goteo\Model\LegalDocumentType\NIF;
use Goteo\Model\LegalDocumentType\VAT;
use Goteo\Model\LegalDocumentType\PASSPORT;

abstract class LegalDocumentType {

    const CIF = 'cif';
    const NIF = 'nif';
    const NIE = 'nie';
    const VAT = 'vat';
    const PASSPORT = 'passport';
    const LEGAL_DOCUMENTS = [self::CIF, self::NIF, self::NIE];

    private string $document_type;

    public static function create(string $document_type): LegalDocumentType {

        switch ($document_type) {
            case self::NIE:
                return new NIE();
            case self::CIF:
                return new CIF();
            case self::NIF:
                return new NIF();
            case self::VAT:
                return new VAT();
            case self::PASSPORT:
                return new PASSPORT();
            default:
                return new NIF();
        }
    }

    public function getDocumentType(): string {
        return $this->document_type;
    }

    static public function getLegalDocumentTypes(): array {
        return  [
            self::CIF => Text::get('donor-legal-document-type-cif'),
            self::NIF => Text::get('donor-legal-document-type-nif'),
            self::NIE => Text::get('donor-legal-document-type-nie'),
            self::VAT => Text::get('donor-legal-document-type-vat')
            ];
    }

}