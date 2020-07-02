
export class Button {
    constructor({data, config, api}) {
        this.api = api;

        /**
         * Styles
         *
         * @type {object}
         */
        this._CSS = {
            block: this.api.styles.block,
            settingsButton: this.api.styles.settingsButton,
            settingsButtonActive: this.api.styles.settingsButtonActive,
            input: this.api.styles.input,
            button: this.api.styles.button,
            wrapper: 'editor-button',
            inputTitle: 'ce-editor-button-input-title',
            inputUrl: 'ce-editor-button-input-url',
        };

        /**
         * Tool's settings passed from Editor
         *
         * @private
         */
        this.config = config;

        /**
         * Block's data
         *
         * @private
         */
        this._data = this.normalizeData(data);

        /**
         * Main Block wrapper
         *
         * @type {HTMLElement}
         * @private
         */
        this._element = this.getTag();
    }

    render() {
        return this._element;
    }

    save(blockContent) {
        return {
            title: blockContent.getElementsByClassName(this._CSS.inputTitle)[0].innerHTML,
            url: blockContent.getElementsByClassName(this._CSS.inputUrl)[0].innerHTML
        }
    }

    static get toolbox() {
        return {
            icon: '<svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19M11,16H10C8.39,16 6,14.94 6,12C6,9.07 8.39,8 10,8H11V10H10C9.54,10 8,10.17 8,12C8,13.9 9.67,14 10,14H11V16M14,16H13V14H14C14.46,14 16,13.83 16,12C16,10.1 14.33,10 14,10H13V8H14C15.61,8 18,9.07 18,12C18,14.94 15.61,16 14,16M15,13H9V11H15V13Z" /></svg>',
            title: 'Кнопка',
        };
    }

    validate(savedData) {
        return savedData.title.trim() !== '' && savedData.url.trim() !== '';
    }

    normalizeData(data) {
        const newData = {};

        if (typeof data !== 'object')
            data = {};

        newData.title = data.title || '';
        newData.url = data.url || '';

        return newData;
    }

    getTag() {
        const container = this.make('div', [this._CSS.block]);

        const title = this.make('div', [this._CSS.input, this._CSS.inputTitle], {
            contentEditable: true,
            innerHTML: this._data.title || ''
        });
        const url = this.make('div', [this._CSS.input, this._CSS.inputUrl], {
            contentEditable: true,
            innerHTML: this._data.url || ''
        });

        title.dataset.placeholder = this.api.i18n.t('Button text');
        url.dataset.placeholder = this.api.i18n.t('Link');

        container.appendChild(title);
        container.appendChild(url);

        return container;
    }

    make(tagName, classNames = null, attributes = {}) {
        const el = document.createElement(tagName);

        if (Array.isArray(classNames)) {
            el.classList.add(...classNames);
        } else if (classNames) {
            el.classList.add(classNames);
        }

        for (const attrName in attributes) {
            el[attrName] = attributes[attrName];
        }

        return el;
    };
}
