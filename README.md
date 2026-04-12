# Wirestrap

> [!WARNING]
>
> **Wirestrap is still in active development**
>
> This project is `pre-1.0` and may introduce breaking changes at any time without prior notice.

---

Wirestrap is a set of ready-to-use Blade components that covers the parts of a Livewire UI
that are time-consuming to get right on your own: modal dialogs, floating elements,
custom select, and more.


Each component is built on Alpine.js, ships with its own styles, and keeps external dependencies
to a minimum. The goal is to stay simple and flexible: components work out of the box and get
out of your way when you need to customize them.

## Official documentation

Full documentation on the **[Wirestrap website](https://wirestrap.dev)**.

## Requirements

- Livewire 4 and its own requirements
- `@floating-ui/dom`

## Installation

```bash
composer require wirestrap/wirestrap
```

Install the JS dependency:

```bash
npm install @floating-ui/dom
```

Import Wirestrap in your JS bundle:

```js
import '../../vendor/wirestrap/wirestrap/resources/js/wirestrap.js';
```

Import the stylesheet in your CSS entry point:

```css
@import '../../vendor/wirestrap/wirestrap/dist/wirestrap.css';
```

## Configuration

Publish the config file to set package-wide defaults for every component:

```bash
php artisan vendor:publish --tag=wirestrap:config
```

Every prop has a corresponding config entry. Set a default once and all instances pick it up. A prop passed directly on a component always takes precedence.

## Coding assistants

Wirestrap ships with a concise Markdown reference covering every component, their props, and their JavaScript API. Publish it so your coding assistant can read it and make accurate use of Wirestrap without guessing:

```bash
php artisan vendor:publish --tag=wirestrap:docs
```

---

⭐ Star the repo if Wirestrap saves you time!

Made with ☕ by Damien FOSSEY
