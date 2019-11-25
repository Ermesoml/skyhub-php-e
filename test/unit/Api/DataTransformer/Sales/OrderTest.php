<?php

namespace SkyHubTest\Api\DataTransformer\Sales;

use PHPUnit\Framework\TestCase;
use SkyHub\Api\DataTransformer\Sales\Order\Cancel;
use SkyHub\Api\DataTransformer\Sales\Order\Delivery;
use SkyHub\Api\DataTransformer\Sales\Order\Invoice;
use SkyHub\Api\DataTransformer\Sales\Order\Shipment;
use SkyHub\Api\DataTransformer\Sales\Order\ShipmentException;
use SkyHub\Api\DataTransformer\Sales\Order\Status\Create;
use SkyHub\Api\DataTransformer\Sales\Order\Status\Update;
use SkyHub\Api\Handler\Request\Sales\OrderHandler;

/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  SkuHubTest
 * @package   SkuHubTest
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */
class OrderTest extends TestCase
{

    /**
     * @test
     */
    public function assertDataTransformerOrderCancel()
    {
        $transformer = new Cancel(OrderHandler::STATUS_CANCELLED);
        $expected = [
            'status' => OrderHandler::STATUS_CANCELLED
        ];

        $this->assertEquals($expected, $transformer->output());
    }


    /**
     * @test
     */
    public function assertDataTransformerOrderDeliveryWithoutDeliveryDate()
    {
        $transformer = new Delivery(OrderHandler::STATUS_COMPLETE);
        $expected = [
            'status' => OrderHandler::STATUS_COMPLETE
        ];

        $this->assertEquals($expected, $transformer->output());
    }

    /**
     * @test
     */
    public function assertDataTransformerOrderDeliveryWithDeliveryDate()
    {
        $transformer = new Delivery(OrderHandler::STATUS_COMPLETE, '12/09/2019');
        $expected = [
            'status'        => OrderHandler::STATUS_COMPLETE,
            'delivered_date'=> '12/09/2019'
        ];

        $this->assertEquals($expected, $transformer->output());
    }

    /**
     * @test
     */
    public function assertDataTransformerOrderInvoice()
    {
        $invoiceKey = '999888777999888777';
        $transformer = new Invoice(OrderHandler::STATUS_PAID, $invoiceKey);
        $expected = [
            'status'  => OrderHandler::STATUS_PAID,
            'invoice' => [
                'key' => $invoiceKey
            ],
        ];

        $this->assertEquals($expected, $transformer->output());
    }

    /**
     * @test
     */
    public function assertDataTransformerOrderInvoiceWithStatusNull()
    {
        $invoiceKey = '999888777999888777';
        $transformer = new Invoice(null, $invoiceKey);
        $expected = [
            'invoice' => [
                'key' => $invoiceKey
            ],
        ];

        $this->assertEquals($expected, $transformer->output());
    }

    /**
     * @test
     */
    public function assertDataTransformerOrderInvoiceEmptyStatus()
    {
        $invoiceKey = '999888777999888777';
        $transformer = new Invoice('', $invoiceKey);
        $expected = [
            'invoice' => [
                'key' => $invoiceKey
            ],
        ];

        $this->assertEquals($expected, $transformer->output());
    }

    /**
     * @test
     */
    public function assertDataTransformerOrderShipmentException()
    {
        $orderId = '99';
        $datetime = '2018-01-20 16:00:00';
        $observation = 'This is a simple observation';
        $status = OrderHandler::STATUS_SHIPMENT_EXCEPTION;

        $transformer = new ShipmentException($orderId, $datetime, $observation, $status);
        $expected = [
            'shipment_exception' => [
                'occurrence_date' => $datetime,
                'observation'     => $observation
            ],
            'status' => $status
        ];

        $this->assertEquals($expected, $transformer->output());
    }


    /**
     * @test
     */
    public function assertDataTransformerOrderShipment()
    {
        $orderId = '12345';
        $status = OrderHandler::STATUS_SHIPPED;
        $trackCode = 'SS987654321XX';
        $trackCarrier = 'Correios';
        $trackMethod = 'PAC';
        $trackUrl = 'http://www.correios.com.br';
        $items = [
            [
                'sku' => 'XYZ',
                'qty' => '2',
            ], [
                'sku' => 'QWE',
                'qty' => '4',
            ]
        ];

        $transformer = new Shipment($orderId, $status, $items, $trackCode, $trackCarrier, $trackMethod, $trackUrl);
        $expected = [
            'status'   => $status,
            'shipment' => [
                'code'  => $orderId,
                'track' => [
                    'code'    => $trackCode,
                    'carrier' => $trackCarrier,
                    'method'  => $trackMethod,
                    'url'     => $trackUrl,
                ]
            ],
            'items' => $items
        ];

        $this->assertEquals($expected, $transformer->output());
    }


    /**
     * @test
     */
    public function assertDataTransformerOrderStatusCreate()
    {
        $code = '99';
        $label = 'This is a Label';
        $type = 'NEW';

        $transformer = new Create($code, $label, $type);
        $expected = [
            'status' => [
                'code'  => $code,
                'label' => $label,
                'type'  => $type
            ]
        ];

        $this->assertEquals($expected, $transformer->output());
    }


    /**
     * @test
     */
    public function assertDataTransformerOrderStatusUpdate()
    {
        $code = '99';
        $label = 'This is a Label';
        $type = 'NEW';

        $transformer = new Update($code, $label, $type);
        $expected = [
            'status' => [
                'code'  => $code,
                'label' => $label,
                'type'  => $type
            ]
        ];

        $this->assertEquals($expected, $transformer->output());
    }
}
