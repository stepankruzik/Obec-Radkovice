(function () {
    var dropzones = document.querySelectorAll('[data-upload-dropzone]');

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
})();
