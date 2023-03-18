<?php

namespace App\Service\Author;

use App\Entity\Author;
use App\Form\Model\AuthorDto;
use App\Form\Type\AuthorFormType;
use App\Repository\AuthorRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorFormProcessor
{
    private AuthorRepository $authorRepository;

    private GetAuthor $getAuthor;

    private FormFactoryInterface $ffi;

    public function __construct(
        AuthorRepository $authorRepository,
        GetAuthor $getAuthor,
        FormFactoryInterface $ffi,
    ) {
        $this->authorRepository = $authorRepository;
        $this->getAuthor = $getAuthor;
        $this->ffi = $ffi;
    }

    public function __invoke(Request $request, ?string $authorId = null): array
    {
        $author = null;
        $authorDto = null;

        if ($authorId === null) {
            $authorDto = new AuthorDto();
        } else {
            $author = ($this->getAuthor)($authorId);
            $authorDto = AuthorDto::createFromAuthor($author);
        }

        $form = $this->ffi->create(AuthorFormType::class, $authorDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if (!$form->isValid()) {
            return [null, $form];
        }

        if ($author === null) {
            $author = Author::create(
                $authorDto->getName()
            );
        } else {
            $author->update(
                $authorDto->getName()
            );
        }

        $this->authorRepository->save($author);
        return [$author, null];
    }
}
