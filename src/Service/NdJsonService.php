<?php

namespace App\Service;

class NdJsonService extends \SplFileObject
{

    /**
     * @param string $filename The file to open
     * @param string $mode [optional] The mode in which to open the file. See {@see fopen} for a list of allowed modes.
     */
    public function __construct($filename, $mode = 'a+')
    {
        parent::__construct($filename, $mode);
        $this->setFlags(\SplFileObject::DROP_NEW_LINE);
    }

    /**
     * Gets line from file and decode a JSON string
     *
     * @return array<TKey, TValue>|null
     */
    public function readline()
    {
        try {
            $row = $this->fgets();
            if (empty($row)) {
                if ($this->eof() === false) {
                    return $this->readline();
                }
                return null;
            }

            /** @var array<TKey, TValue>|null */
            return json_decode(trim($row));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get lines from file and decode a JSON string
     *
     * @param int $lines Number of lines to read
     *
     * @return \Generator<array<int, array<TKey, TValue>>>
     */
    public function readlines($lines): \Generator
    {
        $count = 0;
        $rows = [];

        while ($this->eof() === false) {
            $row = $this->readline();
            if ($row === null) {
                continue;
            }
            $rows[] = $row;
            if (++$count === $lines) {
                yield $rows;
                $count = 0;
                $rows = [];
            }
        }

        if ($count > 0) {
            yield $rows;
        }
    }

    /**
     * Write the array to file with JSON encoding
     *
     * @param array<TKey, TValue> $values
     * @param int $json_flags
     * @param string $separator
     *
     * @return int|false the number of bytes written, or 0 (false since 7.4) on error.
     */
    public function writeline($values, $json_flags = 0, $separator = "\n")
    {
        $json_flags &= ~JSON_PRETTY_PRINT;

        return $this->fwrite(json_encode($values, $json_flags) . $separator);
    }

    /**
     * Write multiple arrays to a file with JSON encoding
     *
     * @param array<TKey, TValue> $values
     * @param int $json_flags
     * @param string $separator
     *
     * @return int|false the number of bytes written, or 0 (false since 7.4) on error.
     */
    public function writelines($values, $json_flags = 0, $separator = "\n")
    {
        $json_flags &= ~JSON_PRETTY_PRINT;

        $values = array_map(static function ($value) use ($json_flags) {
            return json_encode($value, $json_flags);
        }, $values);

        return $this->fwrite(implode($separator, $values) . $separator);
    }
}

