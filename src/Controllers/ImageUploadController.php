<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

class ImageUploadController extends Controller
{
    /**
     * Upload file
     *
     * @param NovaRequest $request
     * @return array
     */
    public function file(NovaRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $image = $request->file('image');
        $fileName = md5(Str::random(16) . uniqid()) . '.' . $image->getClientOriginalExtension();
        $subDir = mb_substr($fileName, 0, 2);
        $subDir2 = mb_substr($fileName, 2, 2);

        $path = $request->file('image')->storePubliclyAs(
            config('editor.toolSettings.image.path') . '/' . $subDir . '/' . $subDir2,
            $fileName,
            config('editor.toolSettings.image.disk')
        );

        $this->applyAlterations(Storage::disk(config('editor.toolSettings.image.disk'))->path($path));
        $thumbnails = $this->applyThumbnails($path);

        return [
            'success' => 1,
            'file' => [
                'url' => Storage::disk(config('editor.toolSettings.image.disk'))->url($path),
                'thumbnails' => $thumbnails
            ]
        ];
    }

    /**
     * @param NovaRequest $request
     * @return array
     */
    public function url(NovaRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'active_url',
                function ($attribute, $value, $fail) {
                    $imageDetails = getimagesize($value);

                    if (!in_array($imageDetails['mime'] ?? '', [
                        'image/jpeg',
                        'image/webp',
                        'image/gif',
                        'image/png',
                        'image/svg+xml',
                    ])) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $url = $request->input('url');
        $imageContents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        $fullName = uniqid() . $name;
        $subDir = mb_substr($fullName, 0, 3);
        $nameWithPath = config('editor.toolSettings.image.path') . '/' . $subDir . '/' . $fullName;


        Storage::disk(config('editor.toolSettings.image.disk'))->put($nameWithPath, $imageContents);

        return [
            'success' => 1,
            'file' => [
                'url' => Storage::disk(config('editor.toolSettings.image.disk'))->url($nameWithPath)
            ]
        ];
    }

    /**
     * @param $path
     * @param array $alterations
     */
    private function applyAlterations($path, $alterations = [])
    {
        try {
            $image = Image::load($path);

            $imageSettings = config('editor.toolSettings.image.alterations');

            if (!empty($alterations)) {
                $imageSettings = $alterations;
            }

            if (empty($imageSettings))
                return;

            if (!empty($imageSettings['resize']['width'])) {
                $image->width($imageSettings['resize']['width']);
            }

            if (!empty($imageSettings['resize']['height'])) {
                $image->height($imageSettings['resize']['height']);
            }

            if (!empty($imageSettings['optimize'])) {
                $image->optimize();
            }

            if (!empty($imageSettings['adjustments']['brightness'])) {
                $image->brightness($imageSettings['adjustments']['brightness']);
            }

            if (!empty($imageSettings['adjustments']['contrast'])) {
                $image->contrast($imageSettings['adjustments']['contrast']);
            }

            if (!empty($imageSettings['adjustments']['gamma'])) {
                $image->gamma($imageSettings['adjustments']['gamma']);
            }

            if (!empty($imageSettings['effects']['blur'])) {
                $image->blur($imageSettings['effects']['blur']);
            }

            if (!empty($imageSettings['effects']['pixelate'])) {
                $image->pixelate($imageSettings['effects']['pixelate']);
            }

            if (!empty($imageSettings['effects']['greyscale'])) {
                $image->greyscale();
            }
            if (!empty($imageSettings['effects']['sepia'])) {
                $image->sepia();
            }

            if (!empty($imageSettings['effects']['sharpen'])) {
                $image->sharpen($imageSettings['effects']['sharpen']);
            }

            $image->save();

        } catch (InvalidManipulation $exception) {
            report($exception);
        }
    }

    /**
     * @param $path
     * @return array
     */
    private function applyThumbnails($path)
    {
        $thumbnailSettings = config('editor.toolSettings.image.thumbnails');

        $generatedThumbnails = [];

        if (!empty($thumbnailSettings)) {
            foreach ($thumbnailSettings as $thumbnailName => $setting) {
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                $newThumbnailName = md5($filename . $thumbnailName) . '.' . $extension;

                $subDir = mb_substr($filename, 0, 2);
                $subDi2 = mb_substr($filename, 2, 2);

                $newThumbnailPath = config('editor.toolSettings.image.path') . '/' . $subDir . '/' . $subDi2 . '/' . $newThumbnailName;

                Storage::disk(config('editor.toolSettings.image.disk'))->copy($path, $newThumbnailPath);

                $newPath = Storage::disk(config('editor.toolSettings.image.disk'))->path($newThumbnailPath);
                $this->applyAlterations($newPath, $setting);

                $generatedThumbnails[] = Storage::disk(config('editor.toolSettings.image.disk'))->url($newThumbnailPath);
            }
        }

        return $generatedThumbnails;
    }
}
