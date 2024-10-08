<?php

namespace Goteo\Model\Invest;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Model;

class InvestAddress extends Model
{
    protected $Table = 'invest_address';
    protected static $Table_static = 'invest_address';

    private int $invest;
    private string $user;
    private ?string $address = null;
    private ?string $zipcode = null;
    private ?string $location = null;
    private ?string $country = null;
    private ?string $name = null;
    private ?string $nif = null;
    private ?string $namedest = null;
    private ?string $emaildest = null;
    private ?bool $regalo = null;
    private ?string $message = null;

    public function getInvest(): int
    {
        return $this->invest;
    }

    public function setInvest(int $invest): InvestAddress
    {
        $this->invest = $invest;
        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): InvestAddress
    {
        $this->user = $user;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): InvestAddress
    {
        $this->address = $address;
        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): InvestAddress
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): InvestAddress
    {
        $this->location = $location;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): InvestAddress
    {
        $this->country = $country;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): InvestAddress
    {
        $this->name = $name;
        return $this;
    }

    public function getNif(): ?string
    {
        return $this->nif;
    }

    public function setNif(?string $nif): InvestAddress
    {
        $this->nif = $nif;
        return $this;
    }

    public function getNamedest(): ?string
    {
        return $this->namedest;
    }

    public function setNamedest(?string $namedest): InvestAddress
    {
        $this->namedest = $namedest;
        return $this;
    }

    public function getEmaildest(): ?string
    {
        return $this->emaildest;
    }

    public function setEmaildest(?string $emaildest): InvestAddress
    {
        $this->emaildest = $emaildest;
        return $this;
    }

    public function getRegalo(): ?bool
    {
        return $this->regalo;
    }

    public function setRegalo(?bool $regalo): InvestAddress
    {
        $this->regalo = $regalo;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): InvestAddress
    {
        $this->message = $message;
        return $this;
    }

    public function save(&$errors = array())
    {
        $fields = [
            'invest' => ':invest',
            'user' => ':user',
            'address' => ':address',
            'zipcode' => ':zipcode',
            'location' => ':location',
            'country' => ':country',
            'name' => ':name',
            'nif' => ':nif',
            'namedest' => ':namedest',
            'emaildest' => ':emaildest',
            'regalo' => ':regalo',
            'message' => ':message'
        ];

        $values = [
            'invest' => $this->invest,
            'user' => $this->user,
            'address' => $this->address,
            'zipcode' => $this->zipcode,
            'location' => $this->location,
            'country' => $this->country,
            'name' => $this->name,
            'nif' => $this->nif,
            'namedest' => $this->namedest,
            'emaildest' => $this->emaildest,
            'regalo' => $this->regalo,
            'message' => $this->message
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields) ) . ") VALUES (" . implode(',', array_values($fields)) . ")";
        try {
            $this->query($sql, $values);
        } catch (PDOException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $this;
    }

    public function validate(&$errors = array()): bool
    {
        return (bool) $this->invest;
    }

    /**
     * @throws ModelNotFoundException
     */
    static function getByInvest(int $invest): InvestAddress
    {
        $sql = "SELECT invest_address.*
                FROM invest_address
                WHERE invest_address.invest = ?";

        $investAddress = self::query($sql, [$invest]);
        if (!$investAddress instanceOf InvestAddress)
            throw new ModelNotFoundException("Not found Invest Address with invest[$invest]");

        return $investAddress;
    }
}
