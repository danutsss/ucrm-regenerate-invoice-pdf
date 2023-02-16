# Invoice regenerate PDF

Configurable regenerate of invoices' PDF. Using this plugin, user can define From and To date filters and regenerate the given invoices' PDF.

Also, this plugin can be used as an example to show some of the possibilities of what you can do with UCRM plugins. It can be used by developers as a template for creating a new plugin.

Read more about creating your own plugin in the [Developer documentation](../../master/docs/index.md).

## Useful classes

### `App\Service\TemplateRenderer`

Very simple class to load a PHP template. When writing a PHP template be careful to use correct escaping function: `echo htmlspecialchars($string, ENT_QUOTES);`.

### UCRM Plugin SDK
The [UCRM Plugin SDK](https://github.com/Ubiquiti-App/UCRM-Plugin-SDK) is used by this plugin. It contains classes able to help you with calling UCRM API, getting plugin's configuration and much more.
