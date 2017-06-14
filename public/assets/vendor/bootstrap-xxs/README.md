bootstrap-xxs
=============

This project provides support for handling smaller screen sizes with Bootstrap 3 (CSS version only).<br>
The new breakpoints are
* bootstrap-xxs.css: `xxs` at 480px
* bootstrap-xxs-tn.css: `xxs` at 480px and `tn` at 384px

New classes are:
* `col-xxs-[1-12]` with corresponding `-pull-`, `-push-` and `-offset-`
* `hidden-xxs`
* `visible-xxs`, `visible-xxs-block`, `visible-xxs-inline`, `visible-xxs-inline-block`
* Same for `-tn-` (only included in `xxs-tn` version)

### Installation

1. Download and include [bootstrap-xxs-tn.css](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bootstrap-xxs-tn.css) or [bootstrap-xxs.css](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bootstrap-xxs.css)  in your project. This will handle most functions.

2. For full support you have to patch your `bootstrap.css` (tested only with v3.2.0)
  * Method 1: Apply [bootstrap.css.patch](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bootstrap.css.patch) (unminified only)
  * Method 2: Use Regex to search and replace in bootstrap.css/bootstrap.min.css<br>
search for `@media \(max-width: ?767px\)( ?\{\n? *.(visible|hidden))`<br>
replace with `@media (min-width: 480px) and (max-width: 767px)$1`<br>
Should be 5 matches.<br>
Method 2 will work for minimized and development version.
  * Method 3: Use [bootstrap-patched.css] (https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bootstrap-patched.css) or [bootstrap-patched.min.css] (https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bootstrap-patched.min.css)

### Usage

Same usage as for `xs`, `sm`, `md` or `lg`.

### Testing
There is a small (and ugly) script, which provides the browser width, calculated difference to the next breakpoints and recognised size by CSS. It requires jQuery (tested only with v1.11.1). Include [bsdebug.js](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bsdebug.js) in your project (after jQuery), reload.<br>
Now resize your browser and watch the overlay.<br>
![](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/doc_images/bsdebug_1.png)<br>
![](https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/doc_images/bsdebug_2.png)

Alternative: Use it from a bookmarklet. Create a new bookmark with target:
```javascript
javascript:(function%20(){var%20n=document.createElement('script');n.setAttribute('language','JavaScript');n.setAttribute('src','https://raw.githubusercontent.com/auipga/bootstrap-xxs/master/bsdebug_bookmarklet.min.js?rand='+new%20Date().getTime());document.body.appendChild(n);})();
```
