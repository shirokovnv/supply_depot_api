<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreDocumentRequest;
use App\UseCases\AddDocumentUseCase;
use App\UseCases\Exceptions\InvalidRemainsException;
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
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $request->getType();

        try {
            (new AddDocumentUseCase(
                $request->getType(),
                $request->getPerformedAt(),
                $request->getItems()
            ))();
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
