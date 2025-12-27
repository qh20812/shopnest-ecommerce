<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeService $attributeService
    ) {}

    /**
     * Get all attributes for a category.
     */
    public function getCategoryAttributes(int $categoryId): JsonResponse
    {
        $attributes = $this->attributeService->getCategoryAttributes($categoryId);

        return response()->json([
            'success' => true,
            'data' => [
                'attributes' => $attributes,
                'variant_attributes' => $attributes->where('is_variant', true)->values(),
                'specification_attributes' => $attributes->where('is_variant', false)->values(),
            ],
        ]);
    }

    /**
     * Generate variant combinations from selected attributes.
     */
    public function generateVariantCombinations(Request $request): JsonResponse
    {
        $request->validate([
            'variant_attributes' => 'required|array',
            'variant_attributes.*' => 'array',
        ]);

        $combinations = $this->attributeService->generateVariantCombinations(
            $request->input('variant_attributes')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'combinations' => $combinations,
                'count' => count($combinations),
            ],
        ]);
    }
}
