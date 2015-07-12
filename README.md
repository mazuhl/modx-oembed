# oEmbed for MODX

Embed videos and photos into MODX sites using the oEmbed autodiscover protocol. Forked from the original *oEmbed 0.2-beta* MODX Extra that has a broken transport package.

## Usage

Upload the transport package into your MODX site and install the oEmbed Extra.

Embed a video into your page/template using the `oembed` snippet:

```
[[oembed? &url=`http://www.youtube.com/watch?v=dQw4w9WgXcQ`]]
```

## Todo

* Add alternative to silent failing for errors, e.g. YouTube "Unauthorized"
* Update the list of supported services
* Add support for (optional) templates/alternative outputs
* Replace the version that's in the MODX Extras listing with this version

## Credits

Many thanks to [atma](http://modx.com/extras/author/atma) for the original implementation.

## Licence

Code is available under GPLv2 licence.
