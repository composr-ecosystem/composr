/*
 This file is intended for customising the way the attachment UI operates/defaults.

 The following variables are defined:
 isImage (boolean)
 isVideo (boolean)
 isAudio (boolean)
 isArchive (boolean)
 ext (the file extension, with no dot)
 */

// Shall we show the options overlay?
showOverlay = !(multi || (isImage && $cms.browserMatches('simplified_attachments_ui')) || isArchive);

// Add any defaults into URL
defaults.thumb = (isImage && !multi && showOverlay) ? '0' : '1';
defaults.type = ''; // =autodetect rendering type

if (isImage) {
    tag = 'attachment_safe'; // [attachment_safe]
}

if (multi || isImage) {
    defaults.framed = '0';
}
