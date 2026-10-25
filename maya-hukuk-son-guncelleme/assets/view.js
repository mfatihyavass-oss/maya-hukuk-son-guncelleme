(function () {
    var finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise(function (resolve, reject) {
            var field = document.createElement('textarea');

            field.value = text;
            field.setAttribute('readonly', '');
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();

            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (error) {
                reject(error);
            }

            document.body.removeChild(field);
        });
    }

    function bindCopy(button) {
        var status = button.parentNode.querySelector('.mh-sg-copy-status');
        var timer = null;

        button.addEventListener('click', function () {
            var url = button.getAttribute('data-mh-sg-copy') || window.location.href;

            copyText(url).then(function () {
                button.classList.add('is-copied');

                if (status) {
                    status.textContent = 'Bağlantı kopyalandı';
                }
            }).catch(function () {
                if (status) {
                    status.textContent = 'Kopyalanamadı';
                }
            }).then(function () {
                window.clearTimeout(timer);
                timer = window.setTimeout(function () {
                    button.classList.remove('is-copied');

                    if (status) {
                        status.textContent = '';
                    }
                }, 2200);
            });
        });
    }

    function bindPointer(block) {
        var frame = null;

        block.addEventListener('pointermove', function (event) {
            if (frame) {
                return;
            }

            frame = window.requestAnimationFrame(function () {
                var rect = block.getBoundingClientRect();

                block.style.setProperty('--mh-sg-cursor-x', (event.clientX - rect.left) + 'px');
                block.style.setProperty('--mh-sg-cursor-y', (event.clientY - rect.top) + 'px');
                block.classList.add('is-pointer-active');
                frame = null;
            });
        });

        block.addEventListener('pointerleave', function () {
            block.classList.remove('is-pointer-active');
        });
    }

    function bindBlock(block) {
        if (block.getAttribute('data-mh-sg-bound')) {
            return;
        }

        block.setAttribute('data-mh-sg-bound', '1');

        if (finePointer) {
            bindPointer(block);
        }

        Array.prototype.forEach.call(block.querySelectorAll('[data-mh-sg-copy]'), bindCopy);

        Array.prototype.forEach.call(block.querySelectorAll('.mh-sg-action'), function (action) {
            action.addEventListener('click', function () {
                action.classList.remove('is-clicked');
                void action.offsetWidth;
                action.classList.add('is-clicked');
            });
        });
    }

    function init(root) {
        Array.prototype.forEach.call((root || document).querySelectorAll('.mh-sg-block'), bindBlock);
    }

    window.mhSgInit = init;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init();
        });
    } else {
        init();
    }
})();
