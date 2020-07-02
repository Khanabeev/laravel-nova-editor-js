<?php

namespace Khanabeev\Editor;

use \EditorJS\EditorJS;
use EditorJS\EditorJSException;
use Laravel\Nova\Fields\Field;

class Editor extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'editor';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'editorSettings' => [
                'placeholder' => config('editor.editorSettings.placeholder', ''),
                'initialBlock' => config('editor.editorSettings.initialBlock', 'paragraph'),
                'autofocus' => config('editor.editorSettings.autofocus', false),
            ],
            'toolSettings' => config('editor.toolSettings'),

            'uploadImageByFileEndpoint' => route('editor-upload-image-by-file'),
            'uploadImageByUrlEndpoint' => route('editor-upload-image-by-url'),

            'videoCheckUrlEndpoint' => route('editor-video-check-url'),
        ]);
    }

    /**
     * Resolve the field's value for display.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @throws \Throwable
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $attribute = $attribute ?? $this->attribute;

        if ($attribute === 'ComputedField')
            return;

        if (!$this->displayCallback) {
            $this->withMeta(['asHtml' => true]);
            $this->value = $this->generateHtmlOutput($this->value);

        } elseif (is_callable($this->displayCallback)) {
            $value = data_get($resource, str_replace('->', '.', $attribute), $placeholder = new \stdClass());

            if ($value !== $placeholder) {
                $this->value = call_user_func($this->displayCallback, $value);
            }
        }
    }

    /**
     * @param string|mixed $jsonData
     * @return string
     * @throws \Throwable
     */
    public static function generateHtmlOutput($jsonData): string
    {
        if (!$jsonData)
            return '';

        // Clean non-string data
        if (!is_string($jsonData)) {
            $newData = json_encode($jsonData);
            if (json_last_error() === \JSON_ERROR_NONE) {
                $jsonData = $newData;
            }
        }

        $config = config('editor.validationSettings');

        try {
            // Initialize Editor backend and validate structure
            $editor = new EditorJS($jsonData, json_encode($config));

            // Get sanitized blocks (according to the rules from configuration)
            $blocks = $editor->getBlocks();

            $htmlOutput = '';

            foreach ($blocks as $block) {
                if (in_array($block['type'], ['image']))
                    $block['data']['classes'] = Editor::calculateImageClasses($block['data']);

                $htmlOutput .= view('editor.' . strtolower($block['type']), $block['data'])->render();
            }

            return html_entity_decode($htmlOutput);
        } catch (EditorJSException $e) {
            // process exception
            return 'Something went wrong: ' . $e->getMessage();
        }
    }

    /**
     * @param $blockData
     * @return string
     */
    public static function calculateImageClasses($blockData)
    {
        $classes = [];
        foreach ($blockData as $key => $data) {
            if (is_bool($data) && $data === true) {
                $classes[] = $key;
            }
        }

        return implode(' ', $classes);
    }

    public static function getVideoHtmlByUrl($url)
    {
        if (!$url)
            return '';

        $services = [
            'youtube' => \App\Tools\Editor\Helpers\Youtube::class,
            'vimeo' => \App\Tools\Editor\Helpers\Vimeo::class,
            'rutube' => \App\Tools\Editor\Helpers\Rutube::class
        ];

        $videoHtml = '';
        foreach ($services as $code => $service) {
            $videoHtml = $service::getVideoHtml($url, 320, 180);

            if ($videoHtml)
                break;
        }

        return $videoHtml;
    }
}
