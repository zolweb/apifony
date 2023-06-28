<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F4Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}