# Custom Markdown Documentation for News Content

This admin panel supports a simple custom markdown syntax for creating news content. The following tags are supported:

## Supported Tags

- `<title>...</title>` → `<h1>...</h1>`
  - Use for the main title of your news article.
- `<p>...</p>` → `<p>...</p>`
  - Use for paragraphs of text.
- `<list>...</list>` → `<ul>...</ul>`
  - Use to start and end a list.
- `<item>...</item>` → `<li>...</li>`
  - Use for each item inside a list.

## Example

```
<title>Big Update Released!</title>
<p>We are excited to announce a major update for Payday 3.</p>
<list>
  <item>New heists</item>
  <item>Improved AI</item>
  <item>Bug fixes</item>
</list>
```

## Resulting HTML

```
<h1>Big Update Released!</h1>
<p>We are excited to announce a major update for Payday 3.</p>
<ul>
  <li>New heists</li>
  <li>Improved AI</li>
  <li>Bug fixes</li>
</ul>
```

## Notes
- Only the above tags are supported.
- The URL field in the admin panel determines the filename (letters, numbers, - or _ only).
- The content is saved as a PHP file and included in the news page.
- You can use multiple paragraphs and lists in one article.

If you need more tags or formatting, contact the developer to extend the parser.
