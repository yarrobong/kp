<?php

namespace Illuminate\Support\Facades;

class Validator
{
    public static function make(array $data, array $rules)
    {
        return new static($data, $rules);
    }

    protected $data;
    protected $rules;
    protected $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    protected function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                if (str_contains($rule, ':')) {
                    [$ruleName, $param] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $param = null;
                }

                if (!$this->checkRule($field, $value, $ruleName, $param)) {
                    $this->errors[$field][] = $this->getErrorMessage($field, $ruleName, $param);
                }
            }
        }
    }

    protected function checkRule($field, $value, $ruleName, $param)
    {
        switch ($ruleName) {
            case 'required':
                return !empty($value) || $value === '0';
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'string':
                return is_string($value);
            case 'numeric':
                return is_numeric($value);
            case 'min':
                return (is_numeric($value) ? (float)$value : strlen($value)) >= (float)$param;
            case 'max':
                return (is_numeric($value) ? (float)$value : strlen($value)) <= (float)$param;
            case 'in':
                return in_array($value, explode(',', $param));
            case 'date':
                return strtotime($value) !== false;
            case 'array':
                return is_array($value);
            case 'unique':
                // Упрощенная проверка - в реальности нужна БД
                return true;
            case 'confirmed':
                return isset($this->data[$field . '_confirmation']) && $this->data[$field] === $this->data[$field . '_confirmation'];
            default:
                return true;
        }
    }

    protected function getErrorMessage($field, $ruleName, $param)
    {
        $messages = [
            'required' => "Поле {$field} обязательно для заполнения",
            'email' => "Поле {$field} должно быть валидным email",
            'min' => "Поле {$field} должно быть не менее {$param}",
            'max' => "Поле {$field} должно быть не более {$param}",
            'in' => "Поле {$field} имеет недопустимое значение",
            'date' => "Поле {$field} должно быть датой",
            'array' => "Поле {$field} должно быть массивом",
            'unique' => "Значение поля {$field} уже используется",
            'confirmed' => "Поле {$field} не совпадает с подтверждением",
        ];

        return $messages[$ruleName] ?? "Ошибка валидации поля {$field}";
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return (object)$this->errors;
    }
}



