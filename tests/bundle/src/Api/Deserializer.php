<?php

declare (strict_types=1);
namespace Zol\Ogen\Tests\TestOpenApiServer\Api;

use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
class Deserializer implements DeserializerInterface
{
    private SerializerInterface&DenormalizerInterface $serializer;
    public function __construct()
    {
        $this->serializer = new Serializer(normalizers: [new ObjectNormalizer(propertyTypeExtractor: new PropertyInfoExtractor(typeExtractors: [new PhpStanExtractor(), new ReflectionExtractor()])), new ArrayDenormalizer()], encoders: [new JsonEncoder()]);
    }
    public function deserialize(string $json, string $type): object
    {
        return $this->serializer->deserialize($json, $type, JsonEncoder::FORMAT, [AbstractNormalizer::REQUIRE_ALL_PROPERTIES => true]);
    }
    public function denormalize(array $data, string $type): object
    {
        return $this->serializer->denormalize($data, $type, null, [AbstractNormalizer::REQUIRE_ALL_PROPERTIES => true, AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]);
    }
}