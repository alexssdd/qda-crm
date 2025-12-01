<?php

namespace app\core\forms;

use Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\base\UnknownPropertyException;

/**
 * Composite form
 */
abstract class CompositeForm extends Model
{
    /**
     * @var Model[]|array[]
     */
    private $forms = [];

    /**
     * @return array
     */
    abstract protected function internalForms(): array;

    /**
     * @param $data
     * @param $formName
     * @return bool
     */
    public function load($data, $formName = null): bool
    {
        $success = parent::load($data, $formName);

        foreach ($this->forms as $name => &$form) {
            if (is_array($form)) {
                for ($i = 0; $i < count($data[$name]) - 1; $i++) {
                    $form[] = clone $form[0];
                }

                $success = Model::loadMultiple($form, $data, $formName === null ? null : $name) && $success;
            } else {
                $success = $form->load($data, $formName !== '' ? null : $name) && $success;
            }
        }
        return $success;
    }

    /**
     * @param $attributeNames
     * @param bool $clearErrors
     * @return bool
     * @throws Exception
     */
    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        $parentNames = $attributeNames !== null ? array_filter((array)$attributeNames, 'is_string') : null;
        $success = parent::validate($parentNames, $clearErrors);
        foreach ($this->forms as $name => $form) {
            if (is_array($form)) {
                $success = Model::validateMultiple($form) && $success;
            } else {
                $innerNames = $attributeNames !== null ? ArrayHelper::getValue($attributeNames, $name) : null;
                $success = $form->validate($innerNames ?: null, $clearErrors) && $success;
            }
        }
        return $success;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function hasErrors($attribute = null): bool
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        if (parent::hasErrors($attribute)) {
            return true;
        }
        foreach ($this->forms as $form) {
            if (is_array($form)) {
                foreach ($form as $item) {
                    if ($item->hasErrors()) {
                        return true;
                    }
                }
            } else {
                if ($form->hasErrors()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getFirstErrors(): array
    {
        $errors = parent::getFirstErrors();
        foreach ($this->forms as $name => $form) {
            if (is_array($form)) {
                foreach ($form as $i => $item) {
                    foreach ($item->getFirstErrors() as $attribute => $error) {
                        $errors[$name . '.' . $i . '.' . $attribute] = $error;
                    }
                }
            } else {
                foreach ($form->getFirstErrors() as $attribute => $error) {
                    $errors[$name . '.' . $attribute] = $error;
                }
            }
        }
        return $errors;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        $message = null;
        $errors = $this->getFirstErrors();

        foreach ($errors as $error) {
            $message = $error;
            break;
        }
        return $message;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        $message = null;
        $errors = $this->getFirstErrors();

        foreach ($errors as $name => $error) {
            $message = $name . ' - ' . $error;
            break;
        }
        return $message;
    }

    /**
     * @param $name
     * @return array|mixed|Model
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        if (isset($this->forms[$name])) {
            return $this->forms[$name];
        }
        return parent::__get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->internalForms(), true)) {
            $this->forms[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->forms[$name]) || parent::__isset($name);
    }
}