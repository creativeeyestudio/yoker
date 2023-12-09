var googleSerp = document.querySelectorAll('.google-serp');
var bUrl = document.querySelectorAll('.b-url');
var bTitle = document.querySelectorAll('.b-title');
var bDesc = document.querySelectorAll('.b-desc');

function updateContent(element, dataAttribute, fallbackText) {
    let inputElement = document.querySelector('.' + element.getAttribute(dataAttribute));

    if (inputElement) {
        element.textContent = inputElement.value || fallbackText;

        inputElement.addEventListener('input', function() {
            element.textContent = inputElement.value || fallbackText;
        });
    }
}

if (googleSerp && googleSerp.length > 0) {
    const elementsToUpdate = [
        { elements: bUrl, dataAttribute: 'data-url-target', fallbackText: '' },
        { elements: bTitle, dataAttribute: 'data-title-target', fallbackText: 'Meta Title' },
        { elements: bDesc, dataAttribute: 'data-desc-target', fallbackText: 'Meta Description' }
    ];

    for (const { elements, dataAttribute, fallbackText } of elementsToUpdate) {
        for (const element of elements) {
            updateContent(element, dataAttribute, fallbackText);
        }
    }
}