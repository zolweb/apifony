<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F2Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}