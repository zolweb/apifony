<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface FormatDefinition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}