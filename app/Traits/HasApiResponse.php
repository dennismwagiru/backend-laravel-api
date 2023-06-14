<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as Response;

trait HasApiResponse
{

    public function parseGivenData(array $data = [], int $statusCode = Response::HTTP_OK, array $headers = []): array
    {
        $responseStructure = [
            'success' => $data['success'] ?? false,
        ];

        if (isset($data['data'])) {
            $responseStructure['data'] = $data['data'];
        }

        if (isset($data['error'])) {
            $responseStructure['error'] = $data['error'];
        }

        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['trace'])) {
            $responseStructure['trace'] = $data['trace'];
        }

        return ["content" => $responseStructure, "statusCode" => $statusCode, "headers" => $headers];
    }

    protected function apiResponse(array $data, int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        $responseData = $this->parseGivenData($data, $statusCode, $headers);
        return response()->json(
            data: $responseData['content'],
            status: $responseData['statusCode'],
            headers: $responseData['headers']
        );
    }

    public function respondSuccess(array $data = []): JsonResponse
    {
        return $this->apiResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    protected function respondCreated($data): JsonResponse
    {
        return $this->apiResponse($data, Response::HTTP_CREATED);
    }

    protected function respondError(?string $error, int $statusCode = Response::HTTP_BAD_REQUEST, array $errors = null, array $trace = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => false,
                'error' => $error ?? 'There was an internal error, Please try again later',
                'errors' => $errors,
                'trace' => $trace
            ],
            $statusCode
        );
    }
}