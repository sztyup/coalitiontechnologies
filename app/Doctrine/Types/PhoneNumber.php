<?php

namespace App\Doctrine\Types;

use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Brick\PhoneNumber\PhoneNumber as Base;

class PhoneNumber extends CustomType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|null|string
     * @throws PhoneNumberParseException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if (empty($value)) { // If field is empty
            return null;
        }

        $number = json_decode($value);
        if ($number === null) { // If not valid json
            throw new PhoneNumberParseException('Value is null');
        }

        try {
            $number = Base::parse($number->number, $number->region_code);
        } catch (\Exception $exception) {
            throw new PhoneNumberParseException('', 0, $exception);
        }

        return $number->format(PhoneNumberFormat::INTERNATIONAL);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|null|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToDatabaseValue($value, $platform);

        try {
            $number = Base::parse((string) $value, 'HU');
        } catch (\Throwable | \Exception $error) {
            return null;
        }

        return json_encode([
            'region_code' => $number->getRegionCode(),
            'number' => $number->getNationalNumber()
        ]);
    }
}
