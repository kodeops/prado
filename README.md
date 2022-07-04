```
 _     _  _____  ______  _______  _____   _____  _______
 |____/  |     | |     \ |______ |     | |_____] |______
 |    \_ |_____| |_____/ |______ |_____| |       ______|
 
```
 

# kodeops/prado

This package provides a simple to use wrapper for the prado service. 

## Install

* Add composer dependency:

`composer require kodeops/prado`

* Add api token to environment file:

`PRADO_API_TOKEN=<token>`

## Properties

There are several handy properties that can be used for generating a mirror.

*  `width` The width of the image in pixels (can be null)
*  `height` The height of the image in pixels (can be null)
*  `blockchain` The blockchain we are working on (currently supported: `ethereum` and  `tezos`.
*  `contract` The address of the smart contract (some `aliases` allowed, check Contracts alias
section below)
*  `mode` Resize mode (`maintain_aspect_ratio`, `fit` and `framed`)
*  `author` Author of the request (for analytics purposes)

## Supported marketplaces

With these marketplaces you can use the method `url` to avoid specifying `blockchain`, `contract` and `token_id`.

* opensea.io
* hicetnunc.xyz
* hicetnunc.art
* teia.art
* henext.xyz
* hic.af
* versum.xyz
* rarible.com
* objkt.com


## Usage

You can use the facade for quick access to NFT:

```
use kodeops\Prado\Prado;

$url = Prado::nft(500787)
    ->blockchain('tezos')
    ->contract('hicetnunc')
    ->width(1200)
    ->height(1200)
    ->quality(75)
    ->author('xp.lo.it')
    ->url();

// https://pradocdn.s3.eu-central-1.amazonaws.com/tezos/KT1RJ6PbjHpwc3M5rw5s2Nbmefwbuwbdxton/500787/500787_1200x1200-maintain_aspect_ratio-90.jpeg 
```

The example above will generate the following image:

![https://pradocdn.s3.eu-central-1.amazonaws.com/tezos/KT1RJ6PbjHpwc3M5rw5s2Nbmefwbuwbdxton/500787/500787_1200x1200-maintain_aspect_ratio-90.jpeg](https://pradocdn.s3.eu-central-1.amazonaws.com/tezos/KT1RJ6PbjHpwc3M5rw5s2Nbmefwbuwbdxton/500787/500787_1200x1200-maintain_aspect_ratio-90.jpeg)

## Resizing modes

There are three modes of generating an image:

### `maintain_aspect_ratio`

The default one is `maintain_aspect_ratio` and it does just that, preserves the original aspect ratio of an image. If the original image is `2500x3500` (always talking in pixels) and we set `width=1200` and `height=1200` a new image of `857x1200` will be generated. The most restrictive constraint will be used to maitain aspect ratio, in this case, the `height`.

Example:

This original image is `2500x3500`:

![https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500](https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-resized.jpeg)

After being processed it generates the following image:

![https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-resized.jpeg](https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-resized.jpeg)

### `fit`

When `fit` mode is used the image will be cropped to fit the exact proportions (portions of the image may be lost).

This example uses the previous original image and sets `width=1200` and `height=1200`, which generates the following image:

![https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-fit.jpeg](https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-fit.jpeg)

### `framed`

When `framed` mode is used the image will be resized in a new canvas while preserving original aspect ratio.

This example uses the previous original image and sets `width=1200` and `height=1200`, which generates the following image:

![https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-framed.jpeg](https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-framed.jpeg)

As you can see, the image is placed with a background color `bgcolor`, which can be set usign:

`->modeProperty('bgcolor', '#e78b9a')`

which generates the following image:

![https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-framed-bg.jpeg](https://pradocdn.s3-eu-central-1.amazonaws.com/prado-2500x3500-framed-bg.jpeg)

If both width and height are null and mode is default, the image will be resized to maintain the aspect ratio while meeting requirements of set `width` and `height`.


## Contracts alias

For most common contracts in Tezos, you can use the alias available:

* hicetnunc `KT1RJ6PbjHpwc3M5rw5s2Nbmefwbuwbdxton`
* rarible `KT18pVpRXKPY2c4U2yFEGSH3ZnhB2kL8kwXS`
* versum `KT1LjmAdYQCLBjwv4S2oFkEzyHVkomAf5MrW`
* fxhash `KT1KEa8z6vWXDJrVqtMrAeDVzsvxat3kHaCE`
