<?php

namespace App\Controller;

use App\Service\LilaJPG;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScreenshotController extends AbstractController
{
    /**
     * @var LilaJPG
     */
    protected $lilaJpg;

    /**
     * @param LilaJPG $lilaJpg
     */
    public function __construct(LilaJPG $lilaJpg)
    {
        $this->lilaJpg = $lilaJpg;
    }

    #[Route('/screenshot', name: 'app_screenshot')]
    public function index(Request $request): Response
    {
        $errors = $this->validate($request);
        if ($errors->count()) {
            return $this->json([
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /**
         * @var StreamInterface $image
         */
        $image = $this->lilaJpg->getImage($request->query->get('fen'));

        return new StreamedResponse(function () use($image) {
            echo $image->getContents();
        }, 200, [
            'Content-Type'     => 'image/gif',
        ]);
    }

    protected function validate(Request $request): \Symfony\Component\Validator\ConstraintViolationListInterface
    {
        /**
         * @var ValidatorInterface $validator
         */
        $validator = Validation::createValidator();

        $constrains = new Collection([
            'fen' => [
                new NotBlank,
                new Type(['type' => 'string']),
            ],
        ]);

        return $validator->validate(['fen' => $request->query->get('fen')], $constrains);
    }
}
