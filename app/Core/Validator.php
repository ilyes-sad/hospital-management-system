<?php
// ============================================================
//  app/Core/Validator.php
//  Server-side form validation - NO HTML5 validation
// ============================================================

class Validator
{
    private array $errors = [];
    private array $data;
    private array $rules;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function validate(): self
    {
        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;

            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : '';
    }

    private function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;

        // Parse rule with parameters (e.g., max:150)
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $param = $parts[1] ?? null;

        match ($ruleName) {
            'required' => $this->validateRequired($field, $value),
            'email'    => $this->validateEmail($field, $value),
            'min'      => $this->validateMin($field, $value, (int) $param),
            'max'      => $this->validateMax($field, $value, (int) $param),
            'numeric'  => $this->validateNumeric($field, $value),
            'integer'  => $this->validateInteger($field, $value),
            'date'     => $this->validateDate($field, $value),
            'phone'    => $this->validatePhone($field, $value),
            'in'       => $this->validateIn($field, $value, $param),
            'unique'   => $this->validateUnique($field, $value, $param),
            'regex'    => $this->validateRegex($field, $value, $param),
            default    => null,
        };
    }

    private function validateRequired(string $field, mixed $value): void
    {
        if ($value === null || trim((string) $value) === '') {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . ' est obligatoire.');
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if ($value !== null && trim((string) $value) !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . " n'est pas une adresse email valide.");
        }
    }

    private function validateMin(string $field, mixed $value, int $min): void
    {
        if ($value !== null && trim((string) $value) !== '' && strlen((string) $value) < $min) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . " doit contenir au moins {$min} caracteres.");
        }
    }

    private function validateMax(string $field, mixed $value, int $max): void
    {
        if ($value !== null && trim((string) $value) !== '' && strlen((string) $value) > $max) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . " ne doit pas depasser {$max} caracteres.");
        }
    }

    private function validateNumeric(string $field, mixed $value): void
    {
        if ($value !== null && trim((string) $value) !== '' && !is_numeric($value)) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . ' doit etre un nombre.');
        }
    }

    private function validateInteger(string $field, mixed $value): void
    {
        if ($value !== null && trim((string) $value) !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . ' doit etre un entier.');
        }
    }

    private function validateDate(string $field, mixed $value): void
    {
        if ($value !== null && trim((string) $value) !== '') {
            $d = DateTime::createFromFormat('Y-m-d', (string) $value);
            if ($d === false || $d->format('Y-m-d') !== $value) {
                $this->addError($field, ucfirst($this->formatFieldName($field)) . " n'est pas une date valide (format: AAAA-MM-JJ).");
            }
        }
    }

    private function validatePhone(string $field, mixed $value): void
    {
        if ($value !== null && trim((string) $value) !== '' && !preg_match('/^[0-9\s\+\-\.]{8,20}$/', $value)) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . " n'est pas un numero de telephone valide.");
        }
    }

    private function validateIn(string $field, mixed $value, ?string $param): void
    {
        if ($param === null) return;
        $allowed = explode(',', $param);
        if ($value !== null && trim((string) $value) !== '' && !in_array($value, $allowed, true)) {
            $allowedList = implode(', ', $allowed);
            $this->addError($field, ucfirst($this->formatFieldName($field)) . " doit etre l'un des valeurs suivantes : {$allowedList}.");
        }
    }

    private function validateUnique(string $field, mixed $value, ?string $param): void
    {
        if ($value === null || trim((string) $value) === '' || $param === null) return;

        $parts = explode(',', $param);
        $table = $parts[0];
        $column = $parts[1] ?? $field;
        $excludeId = $parts[2] ?? null;

        $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
        $params = [$value];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = (int) $excludeId;
        }

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);
        $count = (int) $stmt->fetch()['cnt'];

        if ($count > 0) {
            $this->addError($field, ucfirst($this->formatFieldName($field)) . ' existe deja.');
        }
    }

    private function validateRegex(string $field, mixed $value, ?string $param): void
    {
        if ($value !== null && trim((string) $value) !== '' && $param !== null) {
            if (!preg_match($param, $value)) {
                $this->addError($field, ucfirst($this->formatFieldName($field)) . " n'a pas le format attendu.");
            }
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function formatFieldName(string $field): string
    {
        return str_replace('_', ' ', $field);
    }
}
