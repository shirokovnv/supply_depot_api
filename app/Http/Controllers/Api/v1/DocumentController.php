<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreDocumentRequest;
use App\UseCases\StoreDocumentUseCase;
use App\UseCases\Exceptions\InvalidRemainsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * @param StoreDocumentRequest $request
     * @return JsonResponse
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $request->getType();

        try {
            $document =  (new StoreDocumentUseCase(
                $request->getType(),
                $request->getPerformedAt(),
                $request->getItems()
            ))();

            return new JsonResponse($document);
        } catch (InvalidRemainsException $exception)
        {
            throw new HttpException(400, $exception->getMessage(), $exception);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
