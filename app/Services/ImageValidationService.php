<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImageValidationService
{
    /**
     * Validate image with aspect ratio and size constraints
     *
     * @param UploadedFile $image
     * @param array $options
     * @return array
     * @throws ValidationException
     */
    public function validate(UploadedFile $image, array $options = []): array
    {
        $defaultOptions = [
            'required_ratio' => 1, // 1:1 (square) by default
            'ratio_tolerance' => 0.05, // 5% tolerance
            'min_width' => 800,
            'min_height' => 800,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_size' => 5120, // 5MB in KB
            'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
        ];

        $options = array_merge($defaultOptions, $options);

        // Validate basic file properties
        $validator = Validator::make(
            ['image' => $image],
            [
                'image' => [
                    'required',
                    'image',
                    'mimes:' . implode(',', $options['allowed_formats']),
                    'max:' . $options['max_size'],
                ],
            ],
            [
                'image.required' => 'Hình ảnh là bắt buộc.',
                'image.image' => 'File phải là hình ảnh.',
                'image.mimes' => 'Hình ảnh phải có định dạng: ' . implode(', ', $options['allowed_formats']) . '.',
                'image.max' => 'Hình ảnh không được vượt quá ' . ($options['max_size'] / 1024) . 'MB.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Get image dimensions
        $imageInfo = getimagesize($image->getRealPath());
        
        if (!$imageInfo) {
            throw ValidationException::withMessages([
                'image' => ['Không thể đọc thông tin hình ảnh.']
            ]);
        }

        [$width, $height] = $imageInfo;

        // Validate dimensions
        if ($width < $options['min_width'] || $height < $options['min_height']) {
            throw ValidationException::withMessages([
                'image' => [
                    sprintf(
                        'Kích thước hình ảnh tối thiểu là %dx%d pixels. Hình ảnh của bạn: %dx%d pixels.',
                        $options['min_width'],
                        $options['min_height'],
                        $width,
                        $height
                    )
                ]
            ]);
        }

        if ($width > $options['max_width'] || $height > $options['max_height']) {
            throw ValidationException::withMessages([
                'image' => [
                    sprintf(
                        'Kích thước hình ảnh tối đa là %dx%d pixels. Hình ảnh của bạn: %dx%d pixels.',
                        $options['max_width'],
                        $options['max_height'],
                        $width,
                        $height
                    )
                ]
            ]);
        }

        // Validate aspect ratio
        $actualRatio = $width / $height;
        $expectedRatio = $options['required_ratio'];
        $tolerance = $options['ratio_tolerance'];

        if (abs($actualRatio - $expectedRatio) > $tolerance) {
            $ratioText = $expectedRatio == 1 ? '1:1 (vuông)' : ($expectedRatio > 1 ? 'ngang' : 'dọc');
            throw ValidationException::withMessages([
                'image' => [
                    sprintf(
                        'Hình ảnh phải có tỉ lệ %s. Tỉ lệ hiện tại: %.2f:1 (%.0fx%.0f pixels).',
                        $ratioText,
                        $actualRatio,
                        $width,
                        $height
                    )
                ]
            ]);
        }

        return [
            'width' => $width,
            'height' => $height,
            'ratio' => $actualRatio,
            'size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
        ];
    }

    /**
     * Validate multiple images
     *
     * @param array $images
     * @param array $options
     * @return array
     * @throws ValidationException
     */
    public function validateMultiple(array $images, array $options = []): array
    {
        $results = [];
        $errors = [];

        foreach ($images as $index => $image) {
            try {
                $results[$index] = $this->validate($image, $options);
            } catch (ValidationException $e) {
                $errors["images.{$index}"] = $e->errors()['image'];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $results;
    }

    /**
     * Get suggested dimensions for a given aspect ratio
     *
     * @param float $ratio
     * @param int $minSize
     * @return array
     */
    public function getSuggestedDimensions(float $ratio = 1, int $minSize = 800): array
    {
        if ($ratio == 1) {
            return ['width' => $minSize, 'height' => $minSize];
        }

        if ($ratio > 1) {
            // Landscape
            $width = $minSize;
            $height = (int) ($minSize / $ratio);
        } else {
            // Portrait
            $height = $minSize;
            $width = (int) ($minSize * $ratio);
        }

        return ['width' => $width, 'height' => $height];
    }
}
