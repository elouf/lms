<?php
/**
 * Created by PhpStorm.
 * User: markokunic
 * Date: 4/21/17
 * Time: 3:41 PM
 */

namespace AppBundle\Exporter;

use Sonata\CoreBundle\Exporter\Exporter as BaseExporter;
use Sonata\Exporter\Writer\XlsWriter;
use Sonata\Exporter\Writer\XmlWriter;
use Sonata\Exporter\Writer\JsonWriter;
use Sonata\Exporter\Writer\CsvWriter;
use Sonata\Exporter\Handler;
use Sonata\Exporter\Source\SourceIteratorInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Exporter extends BaseExporter
{
    /**
     * @throws \RuntimeException
     *
     * @param string                  $format
     * @param string                  $filename
     * @param SourceIteratorInterface $source
     *
     * @return StreamedResponse
     */
    public function getResponse($format, $filename, SourceIteratorInterface $source)
    {
        switch ($format) {
            case 'xls':
                $writer = new XlsWriter('php://output');
                $contentType = 'application/vnd.ms-excel';
                break;
            case 'xml':
                $writer = new XmlWriter('php://output');
                $contentType = 'text/xml';
                break;
            case 'json':
                $writer = new JsonWriter('php://output');
                $contentType = 'application/json';
                break;
            case 'csv':
                $writer = new CsvWriter('php://output', ',', '"', '', true, true);
                $contentType = 'text/csv';
                break;
            case 'xlsx':
                $writer = new XlsxWriter('php://output');
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            default:
                throw new \RuntimeException('Invalid format');
        }

        $callback = function () use ($source, $writer) {
            $handler = Handler::create($source, $writer);
            $handler->export();
        };

        return new StreamedResponse($callback, 200, array(
            'Content-Type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ));
    }
}