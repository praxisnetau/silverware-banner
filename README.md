# SilverWare Banner Module

[![Latest Stable Version](https://poser.pugx.org/silverware/banner/v/stable)](https://packagist.org/packages/silverware/banner)
[![Latest Unstable Version](https://poser.pugx.org/silverware/banner/v/unstable)](https://packagist.org/packages/silverware/banner)
[![License](https://poser.pugx.org/silverware/banner/license)](https://packagist.org/packages/silverware/banner)

Provides an animated banner component consisting of multiple images for [SilverWare][silverware] apps.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverWare][silverware]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/banner
```

## Usage

After installation, a `BannerComponent` will become available for use within your SilverWare templates and layouts.
You can add `Slide` objects as children of the component. The images defined for each slide will be used for the
banner images.

Using the Style tab, you may set the dimensions and resize method for slide images. For example, 100px height and
`scale-height` method will result in a banner with all images set to 100px high.

On the Options tab, you may define the number of slides to be displayed (from all available children), and whether
to display them in the order defined by the site tree, or at random.

### Animation

The `BannerComponent` also supports smooth scrolling animation (left or right) of the banner. On the Options tab, 
check the Animate checkbox, then select the animation type and animation duration in seconds.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](http://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](https://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[composer]: https://getcomposer.org
[issues]: https://github.com/praxisnetau/silverware-banner/issues
