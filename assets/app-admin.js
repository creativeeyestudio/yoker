/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/admin/app.scss';

// start the Stimulus application
import './bootstrap';
import Sortable from 'sortablejs';


/* TABS
--------------------------------------------*/
var tabs = require('tabs');
var container = document.querySelector('.tab-container');

if (container != null) {
    tabs(container);
}


/* SORTABLE JS
--------------------------------------------*/
var dragDropList = document.querySelector('#drag-drop-list');
if (dragDropList) {
    document.addEventListener('DOMContentLoaded', function() {
        var sortable = new Sortable(dragDropList, {
            animation: 150,
            onEnd: function (event) {
                // Mettre à jour l'ordre des éléments après le glisser-déposer
                const lines = dragDropList.querySelectorAll('.line');
                lines.forEach(function (line, index) {
                    line.dataset.order = index + 1;
                });
                changeOrderLinks();
            },
        })    
    })
}

function changeOrderLinks(){
    const url = dragDropList.dataset.url;
    const lines = dragDropList.querySelectorAll('.line');
    const orderData = [];

    lines.forEach(function (line) {
        const orderId = line.dataset.id;
        const orderValue = line.dataset.order;
        orderData.push({ id: orderId, order: orderValue });
    });

    // Envoyer les données d'ordre via une requête AJAX au contrôleur
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData),
    })
    .then(function (response) {
        // Gérer la réponse du contrôleur (facultatif)
        console.log('Enregistrement de l\'ordre terminé : ', response);
    })
    .catch(function (error) {
        console.error('Erreur lors de l\'enregistrement de l\'ordre :', error);
    });
}


/* SECTION - NAVIGATION
--------------------------------------------*/
var navLinksRemove = document.querySelectorAll('.nav-remove');
if (navLinksRemove) {
    const urlDel = dragDropList.dataset.urldel;
    navLinksRemove.forEach(link => {
        link.addEventListener('click', function() {
            const linkId = link.dataset.id
            fetch(urlDel, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(linkId),
            })
            .then(function (response) {
                // Gérer la réponse du contrôleur (facultatif)
                var navLinkRemoved = document.querySelector('.nav-link-' + linkId);
                navLinkRemoved.style.display = 'none';
                console.log('Suppression effectuée : ', response);
            })
            .catch(function (error) {
                console.error('Erreur lors de la suppression :', error);
            });
        })
    })
}