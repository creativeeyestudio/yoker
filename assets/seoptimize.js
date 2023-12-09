var googleSerp = document.querySelectorAll('.google-serp');
var bUrl = document.querySelectorAll('.b-url');
var bTitle = document.querySelectorAll('.b-title');
var bDesc = document.querySelectorAll('.b-desc');

function updateContent(element, dataAttribute, fallbackText) {
    let inputTarget = element.getAttribute(dataAttribute);
    let inputElement = document.querySelector('.' + inputTarget);

    if (inputElement) {
        element.textContent = inputElement.value || fallbackText;

        inputElement.addEventListener('input', function() {
            element.textContent = inputElement.value || fallbackText;
        });
    }
}

if (googleSerp) {
    console.log('Les éléments de SEOptimize ont été localisés');

    for (const title of bTitle) {
        updateContent(title, 'data-title-target', 'Meta Title');
    }

    for (const desc of bDesc) {
        updateContent(desc, 'data-desc-target', 'Meta Description');
    }
}
