// AJOUT DU PLUGIN
// ---------------------------------------------------------------------------
CKEDITOR.plugins.add('responsiveimage', {
    icons: 'image',
    init: function(editor) {
        // Ajoute une nouvelle commande pour insérer une image responsive
        editor.addCommand('insertResponsiveImage', {
            exec: function(editor) {
                // Ouvre la boîte de dialogue d'insertion d'image
                editor.openDialog('responsiveimage');
            }
        });

        // Ajoute un bouton à la barre d'outils
        editor.ui.addButton('ResponsiveImage', {
            label: 'Insérer une image responsive',
            command: 'insertResponsiveImage',
            toolbar: 'insert',
            icon: this.path + 'icons/responsiveimage.png'
        });

        // Définit la boîte de dialogue d'insertion d'image responsive
        CKEDITOR.dialog.add('responsiveimage', function(editor) {
            return {
                title: 'Insérer une image responsive',
                minWidth: 400,
                minHeight: 200,
                contents: [
                    {
                        id: 'required',
                        label: editor.lang.common.generalTab,
                        elements: [
                            {
                                id: 'imageSrc',
                                type: 'text',
                                label: editor.lang.common.url,
                                required: true,
                                validate: CKEDITOR.dialog.validate.notEmpty('Le champ URL de l\'image ne peut pas être vide'),
                                setup: function(element) {
                                    this.setValue(element.getAttribute('src') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('src', trim(this.getValue()));
                                }
                            },
                            {
                                id: 'imageSrcBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                            {
                                id: 'imageAlt',
                                type: 'text',
                                label: 'Texte alternatif de l\'image',
                                setup: function(element) {
                                    this.setValue(element.getAttribute('alt') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('alt', this.getValue());
                                }
                            },
                        ]
                    },
                    {
                        id: 'responsive',
                        label: 'Images Responsive',
                        elements: [
                            {
                                id: 'largeJpg',
                                type: 'text',
                                label: 'URL de l\'image large en format JPEG/PNG',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-large-jpg') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-large-jpg', this.getValue());
                                }
                            },
                            {
                                id: 'largeJpgBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                            {
                                id: 'mediumJpg',
                                type: 'text',
                                label: 'URL de l\'image moyenne en format JPEG/PNG',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-medium-jpg') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-medium-jpg', this.getValue());
                                }
                            },
                            {
                                id: 'mediumJpgBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                            {
                                id: 'smallJpg',
                                type: 'text',
                                label: 'URL de l\'image petite en format JPEG/PNG',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-small-jpg') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-small-jpg', this.getValue());
                                }
                            },
                            {
                                id: 'smallJpgBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            }
                        ]
                    },
                    {
                        id: 'webp',
                        label: 'Images WEBP',
                        elements: [
                            {
                                id: 'largeWebp',
                                type: 'text',
                                label: 'URL de l\'image large en format WEBP',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-large-webp') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-large-webp', this.getValue());
                                }
                            },
                            {
                                id: 'largeWebpBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                            {
                                id: 'mediumWebp',
                                type: 'text',
                                label: 'URL de l\'image moyenne en format WEBP',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-medium-webp') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-medium-webp', this.getValue());
                                }
                            },
                            {
                                id: 'mediumWebpBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                            {
                                id: 'smallWebp',
                                type: 'text',
                                label: 'URL de l\'image petite en format WEBP',
                                onChange: function() {
                                    updatePreview(this.getDialog());
                                },
                                setup: function(element) {
                                    this.setValue(element.getAttribute('data-small-webp') || '');
                                },
                                commit: function(element) {
                                    element.setAttribute('data-small-webp', this.getValue());
                                }
                            },
                            {
                                id: 'smallWebpBrowse',
                                type: 'button',
                                filebrowser: 'info:src',
                                hidden: true,
                                label: editor.lang.common.browseServer,
                            },
                        ]
                    }
                ],
                onOk: function() {
                    var dialog = this;
                    var imageSrc = dialog.getContentElement('required', 'imageSrc').getValue();
                    var imageAlt = dialog.getContentElement('required', 'imageAlt').getValue();
                    var largeWebp = dialog.getContentElement('webp', 'largeWebp').getValue();
                    var mediumWebp = dialog.getContentElement('webp', 'mediumWebp').getValue();
                    var smallWebp = dialog.getContentElement('webp', 'smallWebp').getValue();
                    var largeJpg = dialog.getContentElement('responsive', 'largeJpg').getValue();
                    var mediumJpg = dialog.getContentElement('responsive', 'mediumJpg').getValue();
                    var smallJpg = dialog.getContentElement('responsive', 'smallJpg').getValue();

                    var lgWebp = largeWebp ? largeWebp + ' type=image/webp 1200w, ' : '';
                    var lgWebpData = largeWebp ? largeWebp : '';
                    var lgImg = largeJpg ? largeJpg + ' 1200w, ' : '';
                    var lgImgData = largeJpg ? largeJpg : '';

                    var mdWebp = mediumWebp ? mediumWebp + ' type=image/webp 768w, ' : '';
                    var mdWebpData = mediumWebp ? mediumWebp : '';
                    var mdImg = mediumJpg ? mediumJpg + ' 768w, ' : '';
                    var mdImgData = mediumJpg ? mediumJpg : '';

                    var smWebp = smallWebp ? smallWebp + ' type=image/webp 480w, ' : '';
                    var smWebpData = smallWebp ? smallWebp : '';
                    var smImg = smallJpg ? smallJpg + ' 480w' : '';
                    var smImgData = smallJpg ? smallJpg : '';
                      

                    // Construit le code HTML pour l'image responsive avec les déclinaisons WEBP
                    var responsiveImageHtml =
                        `<img   src="` + imageSrc +`"
                                srcset="` + lgWebp + lgImg + mdWebp + mdImg + smWebp + smImg + `" 
                                data-webp-lg = "` + lgWebpData + `"
                                data-webp-md = "` + mdWebpData + `"
                                data-webp-sm = "` + smWebpData + `"
                                data-img-lg = "` + lgImgData + `"
                                data-img-md = "` + mdImgData + `"
                                data-img-sm = "` + smImgData + `"
                                sizes="(max-width: 767px) 480px, (min-width: 768px) 768px, 1200px" 
                                alt="` + imageAlt +`" />`;

                    // Crée un élément DOM pour l'image responsive
                    var element = CKEDITOR.dom.element.createFromHtml(responsiveImageHtml, editor.document);

                    // Insère l'élément dans l'éditeur
                    editor.insertElement(element);
                },
            };

        });
        // MODIFICATION D'UNE IMAGE
        // ---------------------------------------------------------------------------
        editor.on('doubleclick', function(evt) {
            var target = evt.data.element;
            if (target.is('img')) {
                // On récupère les données de l'image
                var alt = target.$.attributes[0].nodeValue;
                var src = target.$.attributes[9].nodeValue;
                var webpSm = target.$.attributes[6].nodeValue;
                var webpMd = target.$.attributes[5].nodeValue;
                var webpLg = target.$.attributes[4].nodeValue;
                var imgSm = target.$.attributes[3].nodeValue;
                var imgMd = target.$.attributes[2].nodeValue;
                var imgLg = target.$.attributes[1].nodeValue;
                // On ouvre la boite de dialogue
                // dialog.open('responsiveimage')
                editor.openDialog('responsiveimage');
            }
        });
    }
});