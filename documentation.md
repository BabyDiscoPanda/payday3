# Custom Markdown Documentation for News Content

This admin panel supports a simple custom markdown syntax for creating news content. The following tags and features are supported:

## Supported Tags

- `<title>...</title>` → `<h1>...</h1>`
  - Use for the main title of your news article (automatically added by the system).
- `<subtitle>...</subtitle>` → `<h2>...</h2>`
  - Use for subtitles. If you upload images, each subtitle can have an image displayed directly below it, in the order of upload.
- `<p>...</p>` → `<p>...</p>`
  - Use for paragraphs of text.
- `<list>...</list>` → `<ul>...</ul>`
  - Use to start and end a list.
- `<item>...</item>` → `<li>...</li>`
  - Use for each item inside a list.

## Images
- You can upload 0, 1, or multiple images for each news event.
- All images are stored in the `news_images` folder.
- The first image(s) are displayed at the top of the news, and then, if you use `<subtitle>...</subtitle>`, each subtitle will have the next image (if any) displayed directly below it, in the order of upload.

## News File Structure
- Each news file starts with `session_start();` and contains a `$newsId` variable with the ID of the news in the database.
- The main title is automatically added as `<h1>`.
- The content is parsed and converted to HTML as described above.
- The site footer is included at the end of the file.

## Example

```
<subtitle>Patch Notes</subtitle>
<p>Here are the latest changes:</p>
<list>
  <item>New heists</item>
  <item>Improved AI</item>
</list>
<subtitle>Gallery</subtitle>
<p>Check out these screenshots!</p>
```

If you upload two images, the first will appear under the first subtitle, the second under the second subtitle.

## Notes
- Only the above tags are supported.
- The URL field in the admin panel determines the filename (letters, numbers, - or _ only).
- The content is saved as a PHP file and included in the news page.
- You can use multiple paragraphs, subtitles, and lists in one article.
- The news ID is available as `$newsId` in the generated file for advanced use.

If you need more tags or formatting, contact the developer to extend the parser.
