(function () {
    function bindBlock(block) {
        var actions = block.querySelectorAll('.mh-sg-action');

        block.addEventListener('mousemove', function (event) {
            var rect = block.getBoundingClientRect();
            var x = event.clientX - rect.left;
            var y = event.clientY - rect.top;

            block.style.setProperty('--mh-sg-cursor-x', x + 'px');
            block.style.setProperty('--mh-sg-cursor-y', y + 'px');
            block.classList.add('is-pointer-active');
        });

        block.addEventListener('mouseleave', function () {
            block.classList.remove('is-pointer-active');
        });

        Array.prototype.forEach.call(actions, function (action) {
            action.addEventListener('click', function () {
                action.classList.remove('is-clicked', 'maya-ai-clicked');
                void action.offsetWidth;
                action.classList.add('is-clicked', 'maya-ai-clicked');
            });
        });
    }

    function init() {
        Array.prototype.forEach.call(document.querySelectorAll('.mh-sg-block'), bindBlock);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
