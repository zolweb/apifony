<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface Int32Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}