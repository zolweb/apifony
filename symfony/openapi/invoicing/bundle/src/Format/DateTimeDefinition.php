<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface DateTimeDefinition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}