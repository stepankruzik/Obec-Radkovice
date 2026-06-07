(function () {
    var dropzones = document.querySelectorAll('[data-upload-dropzone]');
    var sortableList = document.querySelector('[data-carousel-sortable]');

    function updateName(input) {
        var label = input.closest('[data-upload-dropzone]');
        var nameTarget = label ? label.querySelector('[data-upload-filename]') : null;
        if (!nameTarget) {
            return;
        }

        if (input.files && input.files.length) {
            if (input.files.length === 1) {
                nameTarget.textContent = input.files[0].name;
                return;
            }

            nameTarget.textContent = input.files.length + ' souborů připraveno k nahrání: ' + Array.prototype.map.call(input.files, function (file) {
                return file.name;
            }).join(', ');
            return;
        }

        nameTarget.textContent = 'Zatím není vybraný žádný soubor.';
    }

    dropzones.forEach(function (dropzone) {
        var input = dropzone.querySelector('[data-upload-input]');
        if (!input) {
            return;
        }

        input.addEventListener('change', function () {
            updateName(input);
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (event) {
            if (!event.dataTransfer || !event.dataTransfer.files.length) {
                return;
            }

            input.files = event.dataTransfer.files;
            updateName(input);
        });
    });

    if (sortableList) {
        var draggedItem = null;

        function sortableItems() {
            return Array.prototype.slice.call(sortableList.querySelectorAll('[data-slide-item]'));
        }

        function refreshSlideNumbers() {
            sortableItems().forEach(function (item, index) {
                var title = item.querySelector('.admin-carousel-copy strong');
                if (!title) {
                    return;
                }

                var rawTitle = title.getAttribute('data-slide-title') || title.textContent;
                title.setAttribute('data-slide-title', rawTitle.replace(/^\d+\.\s*/, ''));
                title.textContent = (index + 1) + '. ' + title.getAttribute('data-slide-title');
            });
        }

        function clearDropTargets() {
            sortableItems().forEach(function (item) {
                item.classList.remove('is-drop-target');
            });
        }

        sortableItems().forEach(function (item) {
            var title = item.querySelector('.admin-carousel-copy strong');
            if (title) {
                title.setAttribute('data-slide-title', title.textContent.replace(/^\d+\.\s*/, ''));
            }

            item.addEventListener('dragstart', function (event) {
                draggedItem = item;
                item.classList.add('is-dragging');
                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', item.querySelector('[data-slide-order]').value);
                }
            });

            item.addEventListener('dragend', function () {
                item.classList.remove('is-dragging');
                clearDropTargets();
                refreshSlideNumbers();
            });

            item.addEventListener('dragover', function (event) {
                event.preventDefault();
                if (!draggedItem || draggedItem === item) {
                    return;
                }

                clearDropTargets();
                item.classList.add('is-drop-target');
                var rect = item.getBoundingClientRect();
                var shouldInsertAfter = event.clientY > rect.top + rect.height / 2;
                sortableList.insertBefore(draggedItem, shouldInsertAfter ? item.nextSibling : item);
            });

            item.addEventListener('drop', function (event) {
                event.preventDefault();
                clearDropTargets();
                refreshSlideNumbers();
            });
        });

        refreshSlideNumbers();
    }
})();
