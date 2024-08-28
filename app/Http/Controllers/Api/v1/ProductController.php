<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ShowInventoryRequest;
use App\Http\Requests\Api\v1\StoreDocumentRequest;
use App\Http\Resources\Api\v1\DocumentResource;
use App\Http\Resources\Api\v1\ProductHistoryResource;
use App\Http\Resources\Api\v1\ProductInventoryResource;
use App\UseCases\ProductItemHistoryUseCase;
use App\UseCases\ProductListHistoryUseCase;
use App\UseCases\ShowInventoryUseCase;
use App\UseCases\StoreDocumentUseCase;
use App\UseCases\Exceptions\InvalidRemainsException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends Controller
{
    /**
     * @param StoreDocumentRequest $request
     * @return DocumentResource
     */
    public function storeDocument(StoreDocumentRequest $request): DocumentResource
    {
        $request->getType();

        try {
            $document =  (new StoreDocumentUseCase(
                $request->getType(),
                $request->getPerformedAt(),
                $request->getItems()
            ))();

            return new DocumentResource($document);
        } catch (InvalidRemainsException $exception)
        {
            throw new HttpException(400, $exception->getMessage(), $exception);
        }
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function listHistory(): AnonymousResourceCollection
    {
        return ProductHistoryResource::collection((new ProductListHistoryUseCase())());
    }

    /**
     * @param int $productId
     * @return ProductHistoryResource
     */
    public function itemHistory(int $productId): ProductHistoryResource
    {
        return new ProductHistoryResource((new ProductItemHistoryUseCase())($productId));
    }

    /**
     * @param ShowInventoryRequest $request
     * @return AnonymousResourceCollection
     */
    public function showInventory(ShowInventoryRequest $request): AnonymousResourceCollection
    {
        return ProductInventoryResource::collection((new ShowInventoryUseCase())($request->getPerformedAt()));
    }
}
