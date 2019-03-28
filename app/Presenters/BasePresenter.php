<?php

namespace App\Presenters;

use App\Entities\Entity;
use Illuminate\Support\Str;

class BasePresenter
{
    public static $enums = [];

    protected $appends = [];

    protected $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function handle($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}($arguments);
        }

        if (Str::contains($name, 'get') && Str::contains($name, 'Text')) {
            return $this->getEnumValue($name);
        }

        throw new \InvalidArgumentException('Unknown Presenter method');
    }

    protected function getEnumValue($getter)
    {
        $originalGetter = Str::replaceFirst('Text', '', $getter);
        $field = lcfirst(Str::after($originalGetter, 'get'));

        if (!method_exists($this->entity, $originalGetter)) {
            throw new \InvalidArgumentException('Undefined presenter method [' . $getter . ']');
        }

        $value = $this->entity->{$originalGetter}();

        if (is_object($value) && method_exists($value, '__toString')) {
            return $value->__toString();
        }

        $map = static::$enums[$field] ?? [];

        if (is_string($value)) {
            return $map[$value] ?? $value;
        }

        if ($value === null) {
            return '';
        }

        return $map[$value];
    }
}
