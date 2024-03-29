<?php

namespace App\EventSubscriber;

use App\Event\BookCreatedEvent;
use App\Service\Book\GetBook;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BookCreatedEventSubscriber implements EventSubscriberInterface
{
    private GetBook $getBook;

    private LoggerInterface $logger;

    public function __construct(GetBook $getBook, LoggerInterface $logger)
    {
        $this->getBook = $getBook;
        $this->logger = $logger;
    }


    public static function getSubscribedEvents()
    {
        return [
            BookCreatedEvent::class => ['onBookCreated']
        ];
    }

    public function onBookCreated(BookCreatedEvent $event)
    {
        $book = ($this->getBook)($event->getBookId()->toString());
        $this->logger->info(sprintf('Book created: %s', $book->getTitle()));
    }
}
