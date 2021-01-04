# Upgrade from v0.1 to v1.0
1. If you are not using the [tag bag bundle](https://github.com/Setono/TagBagBundle) anywhere else in your application
you can safely remove it.
   
2. The entity that was named `Conversion` in v0.1 has been renamed to `ConversionAction` in v1.0 while a new `Conversion`
entity has been created. This means your Doctrine migration file should first rename old 
   `setono_sylius_google_ads__conversion` table to `setono_sylius_google_ads__conversion_action`.
   
3. Generate a random string and use it as a salt in the config:
    ```yaml
   # config/packages/setono_sylius_google_ads.yaml
    setono_sylius_google_ads:
        salt: 'insert your random string here'
    ```
