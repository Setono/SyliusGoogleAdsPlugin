# Upgrade from v0.1 to v1.0
1. If you are not using the [tag bag bundle](https://github.com/Setono/TagBagBundle) anywhere else in your application
you can safely remove it.
   
2. The entity that was named `Conversion` in v0.1 has been renamed to `ConversionAction` in v1.0 while a new `Conversion`
entity has been created. This means your Doctrine migration file should first rename old 
   `setono_sylius_google_ads__conversion` table to `setono_sylius_google_ads__conversion_action`.
The easiest way to do this is first to run a migration like this:
   ```php
   <?php
   final class Version20210104104617 extends AbstractMigration
    {
        public function up(Schema $schema): void
        {
            $this->addSql('RENAME TABLE setono_sylius_google_ads__conversion TO setono_sylius_google_ads__conversion_action');
            $this->addSql('RENAME TABLE setono_sylius_google_ads__conversion_channels TO setono_sylius_google_ads__conversion_action_channels');
        }
    
        public function down(Schema $schema): void
        {
            $this->addSql('RENAME TABLE setono_sylius_google_ads__conversion_action TO setono_sylius_google_ads__conversion');
            $this->addSql('RENAME TABLE setono_sylius_google_ads__conversion_action_channels TO setono_sylius_google_ads__conversion_channels');
        }
    }
   ```
   
    And then run `php bin/console doctrine:migrations:diff` and `php bin/console doctrine:migrations:migrate`.
   
3. Generate a random string and use it as a salt in the config:
    ```yaml
   # config/packages/setono_sylius_google_ads.yaml
    setono_sylius_google_ads:
        salt: 'insert your random string here'
    ```
