<?php
/**
 * @author G. Giunta
 * @copyright (C) eZ Systems AS 2014
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

namespace GGGeek\eZPerformanceLoggerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use eZPerfLogger;

/**
 * A class designed to be hooked up as a Symfony Event listener, to execute the logging of perf. counters
 */
class PerfLogger implements EventSubscriberInterface
{
    protected $legacyKernelClosure;
    protected $hasRun = false;
    protected $needsResponseRewrite = false;

    /**
     * @param callable $legacyKernelClosure
     * @param bool $needsResponseRewrite Ideally we should figure this out from ezperformancelogger inis, but it would
     *                                   be too complicated, so we allow the developer to tell us
     */
    public function __construct( \Closure $legacyKernelClosure, $needsResponseRewrite = false )
    {
        $this->legacyKernelClosure = $legacyKernelClosure;
        $this->needsResponseRewrite = $needsResponseRewrite;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => 'onTerminate',
            KernelEvents::RESPONSE => 'onFilterResponse'
        );
    }

    /**
     * If needed, executes the ezperformancelogger logging on the Symfony Post-Response event
     * We wrap the logging code in a runCallback call, as it might need to read inis and such stuff
     * NB: we do not need to check for request type - this event only fires on master requests
     *
     * @param $event
     */
    public function onTerminate( PostResponseEvent $event )
    {
        // small speed gain: avoid useless callback if measurement was already done
        if ( $this->hasRun )
            return;

        $response = $event->getResponse();
        $legacyKernelClosure = $this->legacyKernelClosure;
        $legacyKernel = $legacyKernelClosure();
        $legacyKernel->runCallback( function() use ( $response ) {
            eZPerfLogger::cleanup( $response->getContent(), $response->getStatusCode() );
        } );
        $this->hasRun = true;
    }

    /**
     * If needed, executes the ezperformancelogger logging on the Symfony Filter-Response event, so we can modify it
     */
    public function onFilterResponse( FilterResponseEvent $event )
    {
        if ( $event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST )
            return;

        if ( !$this->needsResponseRewrite )
            return;

        $response = $event->getResponse();
        $legacyKernelClosure = $this->legacyKernelClosure;
        $legacyKernel = $legacyKernelClosure();
        $legacyKernel->runCallback( function() use ( $response ) {
            if ( eZPerfLogger::isEnabled() )
            {
                $content = eZPerfLogger::preoutput( $response->getContent(), $response->getStatusCode() );
                $response->setContent( $content );
            }
        } );
        $this->hasRun = true;
    }
}
