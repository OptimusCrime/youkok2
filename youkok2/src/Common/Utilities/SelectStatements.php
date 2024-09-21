<?php
namespace Youkok\Common\Utilities;

use Iterator;

class SelectStatements implements Iterator
{
    private array $statements;
    private int $position;

    public function __construct(?string $key = null, ?string $value = null)
    {
        $this->statements = [];
        $this->position = 0;

        // Shortcut for adding just one statement
        if ($key !== null && $value !== null) {
            $this->addStatement($key, $value);
        }
    }

    public function addStatement(string $key, $value): void
    {
        $this->statements[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    public function current(): mixed
    {
        return $this->statements[$this->position]['value'];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return $this->statements[$this->position]['key'];
    }

    public function valid(): bool
    {
        return $this->position < count($this->statements);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function getStatements(): array
    {
        return $this->statements;
    }

    public function equals(SelectStatements $other): bool
    {
        $otherStatements = $other->getStatements();
        if ($this->statements === $otherStatements) {
            // Select statements are exactly the same
            return true;
        }

        foreach ($this->statements as $key => $value) {
            if (!isset($otherStatements[$key]) || $otherStatements[$key] !== $value) {
                return false;
            }
        }

        // All the selects in other is also found in `this`
        return true;
    }

    public function __toString(): string
    {
        $output = '';
        foreach ($this->statements as $statement) {
            $output .= '`' . $statement['key'] . '`' . ' = "' . $statement['value'] . '". ';
        }

        return $output;
    }
}
