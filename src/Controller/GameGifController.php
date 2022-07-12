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

class GameGifController extends AbstractController
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

    #[Route('/game-gif', name: 'app_game_gif')]
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
        $image = $this->lilaJpg->getGameGif(array_merge(['delay' => 100], json_decode($request->getContent(), true)['game']));

        return new StreamedResponse(function () use($image) {
            echo $image->getContents();
        }, 200, [
            'Content-Type' => 'image/gif',
        ]);
    }

    protected function validate(Request $request): \Symfony\Component\Validator\ConstraintViolationListInterface
    {
        /**
         * @var ValidatorInterface $validator
         */
        $validator = Validation::createValidator();

        $constrains = new Collection([
            'game' => [
                new NotBlank,
                new Type('array'),
            ],
        ]);

        return $validator->validate(['game' => json_decode($request->getContent(), true)['game']], $constrains);
    }
}
