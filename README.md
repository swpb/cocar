Cocar
===============

Descrição: CocarBundle

Pré-requisitos:
===============
	* Protocolo de gerência SNMP
	* Sistema de Banco de dados RRDTool
	* Agendador de tarefas cron

	- Instalação: apt-get install snmp rrdtool php5-snmp php5-json php-gd libicu-dev

Instalação:
------------

1 – Adicione a seguinte linha ao seu composer.json
```js
//composer.json
{
    //...

    "require": {
        //...
	"jpgraph/jpgraph": "dev-master",
	"symfony/class-loader": "2.2.*",
	"incenteev/composer-parameter-handler": "~2.0",
	"friendsofsymfony/rest-bundle": "dev-master",
	"jms/serializer": "0.14.*@dev",
	"jms/serializer-bundle": "0.13.*@dev",
	"knplabs/knp-paginator-bundle": "dev-master",
	"gerenciador-redes/cocar-bundle": "dev-master"
    }

    //...
}
```
	
2 – Atualize o composer.

        php composer.phar update

3 - Adicione o CocarBundle ao seu AppKernel.php
```php
<?php
        public function registerBundles()
        {
                $bundles = array(
                        //...
                        new Swpb\Bundle\CocarBundle\CocarBundle()
                );
        }
```

4 – Configure a rota do CocarBundle em (app/config/routing.yml)
```php
        CocarBundle_cocar_annotation:
            resource: "@CocarBundle/Resources/config/routing.yml"
            prefix:   /
```

5 – Crie as tabelas do CocarBundle.

        php app/console doctrine:schema:update --force

6 – Instale os assets.
        Edite o arquivo app/config/config.yml e adicione CocarBundle na opção bundles
        //...
        # Assetic Configuration
        assetic:
            debug:          "%kernel.debug%"
            use_controller: false
            bundles:        [ CocarBundle ]
        
         //...

 
	php app/console assetic:dump
        php app/console assets:install

7 – Adicione os agendamentos ao cron.

Atenção: Verifique os caminhos existentes em "schedules.txt" antes de adicioná-lo ao cron.

        crontab -u {usuario} schedules.txt

Configuração:
===============
	1 – Cadastre uma nova entidade no menu (Entidades).

	2 – Cadastre um novo circuito no menu (Circuitos).

Atenção:
===============
		Inicialmente os relatórios (menu Relatórios) estarão em branco, pois são gerados automaticamente 
	pelo sistema (através do cron). Geralmente este processo é executado entre 5 e 6:30 da manhã. 
		Isto é necessário por se tratar de um processo pesado, onde na parte do dia os dados são coletados, 
	e a noite são gerados os demais relatórios.

