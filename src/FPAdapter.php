<?php

namespace Zipferot3000\LaravelFilepondAdapter;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FPAdapter
{
    public function getFileResponseByUUID(Request $request)
    {
        $uuid = $request->input('load', '');

        if (strlen($uuid) > 0) {
            $media = Media::firstWhere('uuid', $uuid);
            if ($media) {
                $headers = [
                    'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Length',
                    'Content-Type' => $media->mime_type,
                    'Content-Length' => $media->size,
                    'Content-Disposition' => 'inline; filename="' . $media->file_name . '"'
                ];
                return response()->file($media->getPath(), $headers);
            }
        }

        abort(404);
    }

    public function saveTemporaryFile(Request $request, HasMedia $model)
    {
        try {
            $file_request_name = array_keys($request->all())[0];
            if ($request->hasFile($file_request_name)) {
                $media = $model->addMedia($request->file($file_request_name))
                    ->withCustomProperties([config('fp_adapter.custom_property_name') => $file_request_name])
                    ->toMediaCollection(config('fp_adapter.media_collection'), config('fp_adapter.filesystem'));
                return $media->uuid;
            }
            abort(415);
        } catch (Exception) {
            abort(400);
        }
    }

    public function destroyTemporaryFile(Request $request, HasMedia $model): void
    {
        $file_uuid = $request->getContent();
        $model->getMedia(config('fp_adapter.media_collection'))
            ->map(function ($media) use ($file_uuid) {
                if ($media->uuid == $file_uuid) {
                    $media->delete();
                }
            });
    }

    public function formatMediaToFilepond(Collection $media): array
    {
        $response = [];
        foreach ($media as $file) {
            $response[$file->custom_properties[config('fp_adapter.custom_property_name')]][] =
                [
                    'source' => $file->uuid,
                    'options' => [
                        'type' => 'local'
                    ]
                ];
        }

        return $response;
    }

    public function getRoutes()
    {
        return Route::group(['prefix' => 'fp_adapter'], function() {
            Route::get('/', fn() => (new FPAdapter())->getFileResponseByUUID(request()));
            Route::post('/', fn() => (new FPAdapter())->saveTemporaryFile(request(), auth()->user()));
            Route::delete('/', fn() => (new FPAdapter())->destroyTemporaryFile(request(), auth()->user()));
        });
    }
}