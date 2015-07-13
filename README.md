# oEmbed for MODX

Embed videos and photos into MODX sites using the oEmbed autodiscover protocol. Forked from the original *oEmbed 0.2.1-beta* MODX Extra that has a broken transport package.

oEmbed is a format for allowing an embedded representation of a URL on third party sites. The simple API allows a website to display embedded content (such as photos or videos) when a user posts a link to that resource, without having to parse the resource directly.

## Usage

Upload the transport package into your MODX site and install the oEmbed Extra.

Embed a video into your page/template using the `oembed` snippet:

```
[[oembed? &url=`http://www.youtube.com/watch?v=dQw4w9WgXcQ`]]
```

*Remember to call the snippet cached - otherwise it'll be trying to find the video each time the page loads.*

## Options

Use the following options:

|Parameter|Description|Default|
|---------|-----------|-------|
|&strictMatch|||
|&maxWidth|Sets width of container|600|
|&maxHeight|Sets height of container|600|
|&format|Sets response format (JSON or XML)|json|
|&outputMode||full|
|&discover|||
|&tpl|(Optional) Use a custom chunk to display content||
|&url|URL of what you want to embed||

## Placeholders

The following placeholders are available in your custom template chunks:

|Placeholder|Description|
|[[+embed]]|Outputs the main `<embed>` content|
|[[+title]]|Title of the embedded media|
|[[+author_url]]|Link to media author's profile|
|[[+author_name]]|Media author's name|
|[[+wrapperWidth]]|From snippet call|
|[[+wrapperHeight]]|From snippet call|

## CSS Classes

Useful for if you're targeting videos or other items with something like [http://fitvidsjs.com/](FitVids.js).

|CSS class|Item|
|---------|----|
|.oembed|Applied to all templates|
|.oembed-photo|Photo media|
|.oembed-video|Video media|
|.oembed-rich|Rich media|
|.oembed-link|URL media|

## Supported Sites

* YouTube
* BlipTV
* Vimeo
* Daily Motion
* Flickr
* Hulu
* Viddler
* Qik
* Revision 3
* Photobucket
* Scribd
* Wordpress TV

## Todo

* Add alternative to silent failing for errors, e.g. YouTube "Unauthorized"
* Update the list of supported services
* Replace the version that's in the MODX Extras listing with this version
* Switch over to [NoEmbed](http://www.noembed.com)

## Credits

Many thanks to [atma](http://modx.com/extras/author/atma) for the original implementation.

## Licence

Code is available under GPLv2 licence.
