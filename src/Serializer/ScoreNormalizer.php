<?php

namespace App\Serializer;

use App\Entity\Book\Score;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ScoreNormalizer implements NormalizerInterface
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($score, $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($score, $format, $context);

        return $data['value'];
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Score;
    }
}
