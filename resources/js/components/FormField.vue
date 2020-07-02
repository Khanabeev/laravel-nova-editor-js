<template>
    <default-field @keydown.native.stop :field="field" :errors="errors" :fullWidthContent="true">
        <template slot="field">
            <div :id="'editor-js-' + this.field.attribute" class="editor-js"></div>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova';

    const EditorJS = require('@editorjs/editorjs');
    const Paragraph = require('@editorjs/paragraph');

    function setHeadingToolSettings(self, tools) {
        if (self.field.toolSettings.header.activated === true) {
            const Header = require('@editorjs/header');

            tools.header = {
                class: Header,
                config: {
                    placeholder: 'Heading',
                    levels: [2,3,4]
                },
                shortcut: self.field.toolSettings.header.shortcut
            }
        }
    }

    function setListToolSettings(self, tools) {
        if (self.field.toolSettings.list.activated === true) {
            const List = require('@editorjs/list');

            tools.list = {
                class: List,
                inlineToolbar: true,
                shortcut: self.field.toolSettings.list.shortcut
            }
        }
    }

    function setImageToolSettings(self, tools) {
        if (self.field.toolSettings.image.activated === true) {
            const ImageTool = require('@editorjs/image');

            tools.image = {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: self.field.uploadImageByFileEndpoint,
                        byUrl: self.field.uploadImageByUrlEndpoint,
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            }
        }
    }

    function setTableToolSettings(self, tools) {
        if (self.field.toolSettings.table.activated === true) {
            const Table = require('@editorjs/table');

            tools.table = {
                class: Table,
                inlineToolbar: true,
            }
        }
    }

    function setVideoToolSettings(self, tools) {
        const Video = require('./blocks/video/index').Video;

        tools.video = {
            class: Video,
            inlineToolbar: false,
            config: {
                endpoints: {
                    checkUrl: self.field.videoCheckUrlEndpoint,
                },
                additionalRequestHeaders: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        }
    }

    function setButtonToolSettings(self, tools) {
        const Button = require('./blocks/button/index').Button;

        tools.button = {
            class: Button,
            inlineToolbar: false,
            config: {
                placeholder: 'Button text'
            }
        }
    }

    function setImageByUrlToolSettings(self, tools) {
        const ImageByUrl = require('./blocks/image/index').ImageByUrl;

        tools.imageByUrl = {
            class: ImageByUrl,
            inlineToolbar: false,
            config: {
                autofocus: true,
                tools: {
                    image: ImageByUrl
                },
                additionalRequestHeaders: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        }
    }

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {

                this.value = this.field.value;

                let self = this;
                let currentContent = (self.field.value ? JSON.parse(self.field.value) : self.field.value);
                let tools = {};

                setHeadingToolSettings(self, tools);
                setListToolSettings(self, tools);
                setImageToolSettings(self, tools);
                setTableToolSettings(self, tools);
                setButtonToolSettings(self, tools);
                setVideoToolSettings(self, tools);
                setImageByUrlToolSettings(self, tools);

                var editor = new EditorJS({
                    /**
                     * Wrapper of Editor
                     */
                    holderId: 'editor-js-' + self.field.attribute,

                    /**
                     * Tools list
                     */
                    tools,

                    /**
                     * This Tool will be used as default
                     */
                    initialBlock: self.field.editorSettings.initialBlock,

                    /**
                     * Default placeholder
                     */
                    placeholder: self.field.editorSettings.placeholder,

                    /**
                     * Enable autofocus
                     */
                    autofocus: self.field.editorSettings.autofocus,

                    /**
                     * Initial Editor data
                     */
                    data: currentContent,
                    onReady: function () {

                    },
                    onChange: function () {
                        editor.save().then((savedData) => {
                            self.handleChange(savedData)
                        });
                    }
                });
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || '')
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = JSON.stringify(value)
            },
        },
    }
</script>
