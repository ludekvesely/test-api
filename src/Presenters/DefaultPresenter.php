<?php

declare(strict_types = 1);

namespace GameeApi\Presenters;

use GameeApi\Scores\CreateScore;
use GameeApi\Scores\GetTopTenScores;
use JsonRPC\Server;
use Nette\Application\UI\Presenter;

class DefaultPresenter extends Presenter
{
    /** @var GetTopTenScores @inject */
    public $topTenScores;

    /** @var CreateScore @inject */
    public $createScore;

    public function renderDefault()
    {
        $server = new Server((string) $this->getHttpRequest()->getRawBody());

        $server->withLocalException('InvalidParameterException');

        $getTopTenScores = $this->topTenScores;
        $server
            ->getProcedureHandler()
            ->withCallback(
                'getTopTenScores',
                function ($gameId) use ($getTopTenScores) {
                    return $getTopTenScores($gameId);
                }
            );

        $createStore = $this->createScore;
        $server
            ->getProcedureHandler()
            ->withCallback(
                'createScore',
                function ($userId, $gameId, $score) use ($createStore) {
                    return $createStore($gameId, $userId, $score);
                }
            );

        $this->sendJson(json_decode($server->execute()));
    }
}
