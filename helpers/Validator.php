<?php
/**
 * PackNGo — Input Validator
 * Fluent, chainable validation for form and API inputs.
 */
declare(strict_types=1);

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field): static
    {
        if (empty($this->data[$field]) && $this->data[$field] !== '0') {
            $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
        return $this;
    }

    public function email(string $field): static
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Invalid email address.';
        }
        return $this;
    }

    public function alpha(string $field): static
    {
        if (!empty($this->data[$field]) && !preg_match('/^[\p{L}\s\'-]+$/u', $this->data[$field])) {
            $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' may only contain letters.';
        }
        return $this;
    }

    public function minLen(string $field, int $min): static
    {
        if (!empty($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
        }
        return $this;
    }

    public function maxLen(string $field, int $max): static
    {
        if (!empty($this->data[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.";
        }
        return $this;
    }

    public function date(string $field): static
    {
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$d || $d->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field][] = 'Invalid date format (expected YYYY-MM-DD).';
            }
        }
        return $this;
    }

    public function futureDate(string $field): static
    {
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if ($d && $d < new DateTime('today')) {
                $this->errors[$field][] = 'Date must be in the future.';
            }
        }
        return $this;
    }

    public function numeric(string $field): static
    {
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
        }
        return $this;
    }

    public function in(string $field, array $allowed): static
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field][] = 'Invalid value for ' . str_replace('_', ' ', $field) . '.';
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        foreach ($this->errors as $msgs) {
            return $msgs[0];
        }
        return '';
    }
}
