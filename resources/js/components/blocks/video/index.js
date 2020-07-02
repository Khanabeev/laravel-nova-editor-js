export class Video {
    constructor({data, config, api}) {
        this._CSS = {
            block: api.styles.block,
            input: api.styles.input,
            container: 'video-tool',
            containerLoading: 'video-tool--loading',
            preloader: 'video-tool__preloader',
            iframeContainer: 'video-tool__iframe-container',
        };

        this.api = api;
        this.config = config;
        this.data = this.normalizeData(data);

        this.element = this.getTag();

        if (this.data.url)
            this.loadFrame(this.data.url);
    }

    render() {
        return this.element;
    }

    save(blockContent) {
        return {
            url: blockContent.getElementsByClassName(this._CSS.input)[0].innerHTML
        };
    }

    validate(savedData) {
        return savedData.url.trim() !== '';
    }

    static get toolbox() {
        return {
            icon: '<svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M10,15L15.19,12L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.07C21.91,9.87 21.94,10.56 21.94,11.16L22,12C22,14.19 21.84,15.8 21.56,16.83C21.31,17.73 20.73,18.31 19.83,18.56C19.36,18.69 18.5,18.78 17.18,18.84C15.88,18.91 14.69,18.94 13.59,18.94L12,19C7.81,19 5.2,18.84 4.17,18.56C3.27,18.31 2.69,17.73 2.44,16.83C2.31,16.36 2.22,15.73 2.16,14.93C2.09,14.13 2.06,13.44 2.06,12.84L2,12C2,9.81 2.16,8.2 2.44,7.17C2.69,6.27 3.27,5.69 4.17,5.44C4.64,5.31 5.5,5.22 6.82,5.16C8.12,5.09 9.31,5.06 10.41,5.06L12,5C16.19,5 18.8,5.16 19.83,5.44C20.73,5.69 21.31,6.27 21.56,7.17Z" /></svg>',
            title: 'Видео',
        };
    }

    normalizeData(data) {
        const newData = {};

        if (typeof data !== 'object')
            data = {};

        newData.url = data.url || '';

        return newData;
    }

    getTag() {
        const container = this.make('div', [this._CSS.block, this._CSS.container, /*this._CSS.containerLoading*/]);

        const url = this.make('div', [this._CSS.input], {
            contentEditable: true,
            innerHTML: this.data.url || ''
        });

        url.dataset.placeholder = 'Enter video url';

        const iframeContainer = this.make('div', [this._CSS.iframeContainer]);

        //const preloader = this.createPreloader();

        //container.appendChild(preloader);
        container.appendChild(url);
        container.appendChild(iframeContainer);

        url.addEventListener('input', (e) => {
            this.loadFrame(url.innerHTML);
        });

        return container;
    }

    createPreloader() {
        const preloader = document.createElement('preloader');
        const url = document.createElement('div');

        url.textContent = this.data.url;

        preloader.classList.add(this._CSS.preloader);
        url.classList.add(this._CSS.url);

        preloader.appendChild(url);

        return preloader;
    }

    loadFrame(url) {
        if (url) {
            fetch(this.config.endpoints.checkUrl + '?url=' + url, {
                headers: this.config.additionalRequestHeaders
            }).then(response => {
                response.json().then(json => {
                    if (json.success) {
                        this.setFrameHtml(json.html);
                    } else {
                        this.setFrameHtml();
                    }
                });
            });
        } else {
            this.setFrameHtml();
        }
    }

    setFrameHtml(html = '') {
        this.element.querySelector('.' + this._CSS.iframeContainer).innerHTML = html;
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
