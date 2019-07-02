<?php
namespace Youkok\Common\Utilities;

class SelectStatements implements \Iterator
{
    private $statements;
    private $position;

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

    public function current()
    {
        return $this->statements[$this->position]['value'];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key()
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
}
