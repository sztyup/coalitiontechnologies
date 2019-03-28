<?php

namespace App\Entities;

use App\Presenters\BasePresenter;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaravelDoctrine\ORM\Contracts\UrlRoutable;

/**
 * @ORM\MappedSuperclass(repositoryClass="App\Doctrine\EntityRepository")
 */
abstract class Entity implements UrlRoutable
{
    /*
     * Properties shared by all Entity
     */

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Carbon $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /*
     * Methods for the shared properties
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return Carbon::make($this->updatedAt);
    }

    /**
     * @param Carbon $updatedAt
     *
     * @return static
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = Carbon::make($updatedAt);

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return Carbon::make($this->createdAt);
    }

    /**
     * @param Carbon $createdAt
     *
     * @return static
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = Carbon::make($createdAt);

        return $this;
    }


    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    public function getFormValue($field)
    {
        if (method_exists($this, $getter = 'get' . Str::studly($field))) {
            return $this->{'get' . Str::studly($field)}();
        }

        $embedded = Str::before($field, '_');

        if (method_exists($this, $getter = 'get' . Str::studly($embedded))) {
            if (method_exists($this->{$getter}(), $fieldGetter = 'get' . Str::studly(Str::after($field, '_')))) {
                return $this->{$getter}()->{$fieldGetter}();
            }

            return null;
        }

        return null;
    }

    public function getDisplayValue($field)
    {
        if (method_exists($this, $getter = 'get' . Str::studly($field))) {
            return $this->{'get' . Str::studly($field) . 'Text'}();
        }

        $embedded = Str::before($field, '_');

        if (method_exists($this, $getter = 'get' . Str::studly($embedded))) {
            if (method_exists($this->{$getter}(), $fieldGetter = 'get' . Str::studly(Str::after($field, '_')))) {
                return $this->{$getter}()->{$fieldGetter . 'Text'}();
            }

            return null;
        }

        return null;
    }

    /*
     * Presenter methods
     */

    /**
     * @return BasePresenter
     */
    protected function getPresenter()
    {
        $class = static::getPresenterClass();

        return new $class($this);
    }

    public static function getPresenterClass()
    {
        $ns = explode("\\", static::class);
        $entity_name = array_pop($ns);

        if (Arr::last($ns) === 'Entities') {
            $presenter_class = "App\\Presenters\\${entity_name}Presenter";
        } else {
            $presenter_class = "App\\Presenters\\" . Arr::last($ns) . "\\${entity_name}Presenter";
        }

        if (class_exists($presenter_class)) {
            return $presenter_class;
        }

        return BasePresenter::class;
    }

    /**
     * Implements presenting
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $presenter = $this->getPresenter();

        if (!$presenter) {
            throw new \BadMethodCallException('Presenter cannot be created');
        }

        try {
            return $presenter->handle($name, $arguments);
        } catch (\InvalidArgumentException $exception) {
            throw new \BadMethodCallException('Undefined method ' . static::class . '[' . $name . ']');
        }
    }

    /*
     * Misc. functions
     */

    public function __toString()
    {
        return (string) $this->{'get' . Str::snake(static::getRouteKeyName())}();
    }

    public static function getRouteKeyName(): string
    {
        return 'id';
    }
}
