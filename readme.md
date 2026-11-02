# Kirby Bettersearch plugin

A search plugin for Kirby that searches for full word combinations rather than just individual words.

Requires Kirby 6 and PHP 8.3–8.5. Searches are case-insensitive, including Unicode text.

## Installation

- unzip [master.zip](https://github.com/bvdputte/kirby-bettersearch/archive/master.zip) as folder `site/plugins/kirby-bettersearch` or
- `git submodule add https://github.com/bvdputte/kirby-bettersearch.git site/plugins/kirby-bettersearch` or
- `composer require bvdputte/kirby-bettersearch`

## Usage

Instead of the default `$pages` [search method](https://getkirby.com/docs/reference/objects/pages/search), use `$pages->bettersearch($querystring, $options)` or `$site->bettersearch($querystring, $options)` instead.

Since we're looking for _full query matches_, following options have been removed:

- stopwords
- minlength
- words

## customIndexer

If you need to manipulate the `content` somehow, you can now pass a `customIndexer` function into the bettersearch function to alter the content.

The custom indexer must return an associative array. Field names passed in `$options['fields']` are matched case-insensitively against that array's keys.

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bvdputte/kirby-bettersearch/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.
