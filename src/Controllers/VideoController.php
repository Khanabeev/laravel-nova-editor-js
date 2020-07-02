<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Khanabeev\Editor\Editor;
use Laravel\Nova\Http\Requests\NovaRequest;

class VideoController extends Controller
{
    /**
     * Upload file
     *
     * @param NovaRequest $request
     * @return array
     */
    public function checkUrl(NovaRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'string|required',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        $url = $request->get('url');

        $videoHtml = Editor::getVideoHtmlByUrl($url);

        return [
            'success' => !empty($videoHtml),
            'html' => $videoHtml
        ];
    }
}
