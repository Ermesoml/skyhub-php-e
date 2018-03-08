![SkyHub - MarketPlace Intelligence](/doc/images/logo.png)

# SkyHub - PHP SDK

Esta é a SDK oficial da SkyHub, construída em PHP, que você pode utilizar para integrar sua plataforma aos nossos serviços.

Veja um exemplo de como é fácil utilizar:

```php
<?php

    require_once './vendor/autoload.php';

    $baseUri = 'https://api.skyhub.com.br';
    $email   = 'teste.sdk@skyhub.com.br';
    $apiKey  = 'ddRTGUrf_bho17FooTjC';

    /** @var \SkyHub\Api $api */
    $api = new SkyHub\Api($baseUri, $email, $apiKey);
    
    /** @var \SkyHub\Api\Handler\Request\Catalog\Product\AttributeHandler $requestHandler */
    $requestHandler = $api->productAttribute();
    
    /**
     * Create an Attribute
     * @var SkyHub\Api\Handler\Response\HandlerInterface $response
     */
    $response = $requestHandler->create('color', 'Color', [
        'Blue',
        'White',
        'Green',
        'Yellow'
    ]);
    
    if ($response->success()) {
        echo 'SUCCESS!';
    }
```

## Wiki
1. [Requerimentos do Sistema](doc/SYSTEM_REQUIREMENTS.md)
1. [Credenciais](doc/CREDENTIALS.md) 
1. [Instalação e Setup](doc/INSTALLATION.md)
1. Utilizando a SDK
    1. [Utilizando a API](doc/usage/API.md)
    1. [Catálogo]()
        1. [Atributos de Produto]()
        1. [Produtos]()
        1. [Categorias]()
    1. [Pedidos]()
        1. [Consultando Pedidos]()
        1. [Consultando Status de Pedidos]()
        1. [Trabalhando com Filas de Pedidos]()
        1. [Faturando um Pedido]()
        1. [Cancelando um Pedido]()
        1. [Adicionando um Tracking de Pedido]()
        1. [Definindo um Pedido como Entregue]()
        1. [Obtendo Etiquetas de Pedidos]()
        1. [Pedidos com Problemas de Entrega]()
     
## Contribuindo com o Código

Sua contribuição é sempre bem vinda! Por favor, leia a [documentação](doc/CONTRIBUTING.md) de contribuição de código.

## Autores

Tiago Sampaio

## Suporte

Para solicitações de suporte, por favor, envie um e-mail para o seguinte endereço:

sdk@e-smart.com.br

## Licença
