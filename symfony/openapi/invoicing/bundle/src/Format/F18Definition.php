<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F18Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}