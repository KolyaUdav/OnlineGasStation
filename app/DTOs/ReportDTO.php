<?php

namespace App\DTOs;

use App\Enums\Reports\Types;
use DateTime;

readonly class ReportDTO
{
    public function __construct(
        public DateTime $from,
        public DateTime $to,
        public Types $type,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['from'])) {
            throw new \InvalidArgumentException('Необходимый параметр from не был передан');
        }

        if (!isset($data['to'])) {
            throw new \InvalidArgumentException('Необходимый параметр to не был передан');
        }

        if (!isset($data['type'])) {
            throw new \InvalidArgumentException('Необходимый параметр type не был передан');
        }

        if ($data['type'] instanceof Types) {
            $data['type'] = $data['type']->value;
        }

        $from = (new DateTime($data['from']));
        $to = (new DateTime($data['to']));
        $type = Types::from($data['type']);

        return new self(
            from: $from,
            to: $to,
            type: $type,
        );
    }
}
