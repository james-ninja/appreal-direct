<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace UpsFreeVendor\Monolog\Handler;

use UpsFreeVendor\Monolog\Logger;
use UpsFreeVendor\Monolog\Utils;
use UpsFreeVendor\Monolog\Formatter\FlowdockFormatter;
use UpsFreeVendor\Monolog\Formatter\FormatterInterface;
/**
 * Sends notifications through the Flowdock push API
 *
 * This must be configured with a FlowdockFormatter instance via setFormatter()
 *
 * Notes:
 * API token - Flowdock API token
 *
 * @author Dominik Liebler <liebler.dominik@gmail.com>
 * @see https://www.flowdock.com/api/push
 *
 * @phpstan-import-type FormattedRecord from AbstractProcessingHandler
 * @deprecated Since 2.9.0 and 3.3.0, Flowdock was shutdown we will thus drop this handler in Monolog 4
 */
class FlowdockHandler extends \UpsFreeVendor\Monolog\Handler\SocketHandler
{
    /**
     * @var string
     */
    protected $apiToken;
    /**
     * @throws MissingExtensionException if OpenSSL is missing
     */
    public function __construct(string $apiToken, $level = \UpsFreeVendor\Monolog\Logger::DEBUG, bool $bubble = \true, bool $persistent = \false, float $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = null, ?int $chunkSize = null)
    {
        if (!\extension_loaded('openssl')) {
            throw new \UpsFreeVendor\Monolog\Handler\MissingExtensionException('The OpenSSL PHP extension is required to use the FlowdockHandler');
        }
        parent::__construct('ssl://api.flowdock.com:443', $level, $bubble, $persistent, $timeout, $writingTimeout, $connectionTimeout, $chunkSize);
        $this->apiToken = $apiToken;
    }
    /**
     * {@inheritDoc}
     */
    public function setFormatter(\UpsFreeVendor\Monolog\Formatter\FormatterInterface $formatter) : \UpsFreeVendor\Monolog\Handler\HandlerInterface
    {
        if (!$formatter instanceof \UpsFreeVendor\Monolog\Formatter\FlowdockFormatter) {
            throw new \InvalidArgumentException('The FlowdockHandler requires an instance of Monolog\\Formatter\\FlowdockFormatter to function correctly');
        }
        return parent::setFormatter($formatter);
    }
    /**
     * Gets the default formatter.
     */
    protected function getDefaultFormatter() : \UpsFreeVendor\Monolog\Formatter\FormatterInterface
    {
        throw new \InvalidArgumentException('The FlowdockHandler must be configured (via setFormatter) with an instance of Monolog\\Formatter\\FlowdockFormatter to function correctly');
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        parent::write($record);
        $this->closeSocket();
    }
    /**
     * {@inheritDoc}
     */
    protected function generateDataStream(array $record) : string
    {
        $content = $this->buildContent($record);
        return $this->buildHeader($content) . $content;
    }
    /**
     * Builds the body of API call
     *
     * @phpstan-param FormattedRecord $record
     */
    private function buildContent(array $record) : string
    {
        return \UpsFreeVendor\Monolog\Utils::jsonEncode($record['formatted']['flowdock']);
    }
    /**
     * Builds the header of the API Call
     */
    private function buildHeader(string $content) : string
    {
        $header = "POST /v1/messages/team_inbox/" . $this->apiToken . " HTTP/1.1\r\n";
        $header .= "Host: api.flowdock.com\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: " . \strlen($content) . "\r\n";
        $header .= "\r\n";
        return $header;
    }
}
