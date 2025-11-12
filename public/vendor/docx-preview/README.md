# DOCX Preview Dependencies

This directory bundles the assets required to render Microsoft Word documents inside the report evidence viewer.

- `jszip.min.js` — JSZip 3.10.1 (MIT License) from https://stuk.github.io/jszip/
- `docx-preview.min.js` — docx-preview 0.3.4 (MIT License) from https://github.com/VolodymyrTsurkan/docxjs
- `docx-preview.min.css` — Local styling to keep the rendered document legible on light and dark themes.

Vendoring these files allows the DOCX preview to work without additional CDN requests.
