<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Response;

trait HasApiResponse
{

    /**
     * Parse given data to return appropriate response where keys are set
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return array
     */
    public function parseGivenData(array $data = [], int $statusCode = Response::HTTP_OK, array $headers = []): array
    {
        $responseStructure = [];

        if (isset($data['data'])) {
            $responseStructure = $data['data'];
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

    /**
     * Parse and Handle Api Response
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function apiResponse(array $data, int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        $responseData = $this->parseGivenData($data, $statusCode, $headers);
        return response()->json(
            data: $responseData['content'],
            status: $responseData['statusCode'],
            headers: $responseData['headers']
        );
    }

    /**
     * Handle successful response with passed data
     *
     * @param array $data
     * @return JsonResponse
     */
    public function respondSuccess(array $data = []): JsonResponse
    {
        return $this->apiResponse([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Handle item created and return data
     *
     * @param array $data
     * @return JsonResponse
     */
    protected function respondCreated(array $data): JsonResponse
    {
        return $this->apiResponse([
            'success' => true,
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    /**
     * Respond with an error. Takes care of all errors.
     *
     * @param string|null $error
     * @param int $statusCode
     * @param array|null $errors
     * @param array|null $trace
     * @return JsonResponse
     */
    protected function respondError(?string $error, int $statusCode = Response::HTTP_BAD_REQUEST, array $errors = null, array $trace = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'success' => false,
                'error' => $error ?? __('errors.server'),
                'errors' => $errors,
                'trace' => $trace
            ],
            $statusCode
        );
    }
}