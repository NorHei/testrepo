/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

// Register a templates definition set named "default".
CKEDITOR.addTemplates( 'default',
{
    // The name of sub folder which hold the shortcut preview images of the
    // templates.
    imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + '/templates/images/' ),

    // The templates definitions.
    templates :
        [
            {
                                title: 'Page-Flip',
                                image: 'pageflip.gif',
                                description: 'Grafiken mit Buch-Bl&auml;tterfunktion',
                                html:
                                        '<div id="myPageFlip">' +
                                                '<img style="margin-right: 10px" height="100" width="100" align="left" class="class="jPageFlip" />' +

                                        '</div>'
                        },


                        {
                                title: 'Zwei Reihen und Blindleiste',
                                image: 'template_dream.gif',
                                description: 'Template mit 2 Spalten, darunter unsichtbare Bildleiste',
                                html:
                                        '<table cellspacing="0" cellpadding="0" style="width:100%" border="0">' +
                                                '<tr>' +
                                                        '<td colspan="2" class="no_pic">'+
                                                        '<hr class="bottom_line" />' +
                                                        '</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                        '<td style="width:60%" valign="top">' +
                                                                'Hier der Inhalt' +
                                                        '</td>' +
                                                        '<td class="picline" style="width:40%" valign="top">' +
                                                                'Hier der Inhalt' +
                                                        '</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                        '<td colspan="2" class="no_pic">' +
                                                        '</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                        '<td colspan="2" class="nopic">' +
                                                                'Hier die Bilderleiste' +
                                                        '</td>' +
                                                '</tr>' +
                                        '</table>' +
                                        '<p>' +
                                                '<hr class="bottom_line" />' +
                                        '</p>'
                        },

                        {
                                title: 'Bild und Titel',
                                image: 'template1.gif',
                                description: 'Hauptbild mit Titel / Text umflie&szlig;t das Bild.',
                                html:
                                        '<h3>' +
                                                '<img style="margin-right: 10px" height="100" width="100" align="left"/>' +
                                                'Type the title here'+
                                        '</h3>' +
                                        '<p>' +
                                                'Type the text here' +
                                        '</p>'
                        },
                        {
                                title: 'Zwei Reihen',
                                image: 'template2.gif',
                                description: 'Template mit 2 Spalten, jede Spalte hat einen Titel.',
                                html:
                                        '<table cellspacing="0" cellpadding="0" style="width:100%" border="1">' +
                                                '<tr>' +
                                                        '<td style="width:50%">' +
                                                                '<h3>Title 1</h3>' +
                                                        '</td>' +
                                                        '<td></td>' +
                                                        '<td style="width:50%">' +
                                                                '<h3>Title 2</h3>' +
                                                        '</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                        '<td>' +
                                                                'Text 1' +
                                                        '</td>' +
                                                        '<td></td>' +
                                                        '<td>' +
                                                                'Text 2' +
                                                        '</td>' +
                                                '</tr>' +
                                        '</table>' +
                                        '<p>' +
                                                'More text goes here.' +
                                        '</p>'
                        },
                        {
                                title: 'Text and Tabelle',
                                image: 'template3.gif',
                                description: 'Ein Titel, darunter eine 3-spaltige Tabelle.',
                                html:
                                        '<div style="width: 80%">' +
                                                '<h3>' +
                                                        'Titel hier eintragen' +
                                                '</h3>' +
                                                '<table style="width:150px;float: right" cellspacing="0" cellpadding="0" border="1">' +
                                                        '<caption style="border:solid 1px black">' +
                                                                '<strong>Table title</strong>' +
                                                        '</caption>' +
                                                        '</tr>' +
                                                        '<tr>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                        '</tr>' +
                                                        '<tr>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                        '</tr>' +
                                                        '<tr>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                                '<td>&nbsp;</td>' +
                                                        '</tr>' +
                                                '</table>' +
                                                '<p>' +
                                                        'Text hier eingeben' +
                                                '</p>' +
                                        '</div>'
                       }
        ]
});